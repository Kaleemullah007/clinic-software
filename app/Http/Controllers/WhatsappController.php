<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\WhatsappLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class WhatsappController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'avoid-back-history']);
    }

    /* ══════════════════════════════════════════════════════════════════════
       SEND  —  POST appointments/{appointment}/send-whatsapp-receipt
    ══════════════════════════════════════════════════════════════════════ */
    public function send(Request $request, Appointment $appointment)
    {
        abort_unless(auth()->user()->can('whatsapp.send'), 403);

        $user = auth()->user();

        // ── 1. Load settings ──────────────────────────────────────────────
        $settingsMap   = \App\Models\Setting::whereIn('key_name', [
            'whatsapp_prefix', 'default_receipt', 'receipt_message', 'currency_symbol',
        ])->pluck('key_value', 'key_name');

        $waPrefix       = preg_replace('/[^0-9]/', '', $settingsMap['whatsapp_prefix'] ?? '92');
        $defaultReceipt = $settingsMap['default_receipt'] ?? 'services_receipt';

        // ── 2. Format recipient phone ─────────────────────────────────────
        $rawPhone = preg_replace('/[^0-9]/', '', $appointment->phone ?? '');
        if (str_starts_with($rawPhone, '0')) {
            $waPhone = $waPrefix . substr($rawPhone, 1);
        } elseif (str_starts_with($rawPhone, $waPrefix)) {
            $waPhone = $rawPhone;
        } else {
            $waPhone = $waPrefix . $rawPhone;
        }

        if (strlen($waPhone) < 10) {
            return response()->json([
                'success' => false,
                'message' => 'Patient has no valid phone number.',
            ], 422);
        }

        // ── 3. Load appointment relations ─────────────────────────────────
        $appointment->load([
            'appointmentService',
            'products.product',
            'products.variation',
            'patient',
            'customer',
            'doctor',
        ]);

        // ── 4. Generate PDF(s) based on default_receipt setting ───────────
        $pdfs = [];  // [ ['url' => '...', 'filename' => '...'] ]

        if (in_array($defaultReceipt, ['services_receipt', 'both'])) {
            $receiptView = \App\Http\Controllers\AppointmentController::resolveReceiptView();
            $pdf         = Pdf::loadView($receiptView, compact('appointment'));
            $fname    = 'receipts/services_' . $appointment->id . '_' . time() . '.pdf';
            Storage::disk('public')->put($fname, $pdf->output());
            $pdfs[]   = [
                'url'      => Storage::disk('public')->url($fname),
                'filename' => 'Services_Receipt_' . ($appointment->serial_series ?? $appointment->id) . '.pdf',
            ];
        }

        if (in_array($defaultReceipt, ['products_receipt', 'both'])) {
            $pdf      = Pdf::loadView('admin.appointments.receipt-products-pdf', compact('appointment'));
            $fname    = 'receipts/products_' . $appointment->id . '_' . time() . '.pdf';
            Storage::disk('public')->put($fname, $pdf->output());
            $pdfs[]   = [
                'url'      => Storage::disk('public')->url($fname),
                'filename' => 'Products_Receipt_' . ($appointment->serial_series ?? $appointment->id) . '.pdf',
            ];
        }

        if (empty($pdfs)) {
            return response()->json(['success' => false, 'message' => 'No receipt type configured.'], 422);
        }

        // ── 5. Send each PDF via Meta API ─────────────────────────────────
        $token        = config('services.whatsapp.token');
        $phoneId      = config('services.whatsapp.phone_id');
        $overallStatus = 'sent';
        $lastError     = null;
        $metaMessageId = null;

        foreach ($pdfs as $pdf) {
            $status       = 'sent';
            $errorMessage = null;

            try {
                $response = Http::withToken($token)
                    ->timeout(15)
                    ->post("https://graph.facebook.com/v18.0/{$phoneId}/messages", [
                        'messaging_product' => 'whatsapp',
                        'to'                => $waPhone,
                        'type'              => 'document',
                        'document'          => [
                            'link'     => $pdf['url'],
                            'caption'  => "Dear {$appointment->name}, please find your receipt attached. Ref: {$appointment->serial_series}. Thank you for visiting D.M.D Clinic.",
                            'filename' => $pdf['filename'],
                        ],
                    ]);

                if ($response->successful()) {
                    $metaMessageId = $response->json('messages.0.id');
                } else {
                    $status        = 'failed';
                    $overallStatus = 'failed';
                    $errorMessage  = $response->json('error.message') ?? $response->body();
                    $lastError     = $errorMessage;
                }
            } catch (\Throwable $e) {
                $status        = 'failed';
                $overallStatus = 'failed';
                $errorMessage  = $e->getMessage();
                $lastError     = $errorMessage;
            }

            // Log each send attempt
            WhatsappLog::create([
                'appointment_id'  => $appointment->id,
                'sent_by'         => $user->id,
                'phone'           => $waPhone,
                'status'          => $status,
                'meta_message_id' => $metaMessageId,
                'error_message'   => $errorMessage,
            ]);
        }

        // ── 6. Stamp first-send time on appointment ───────────────────────
        if ($overallStatus === 'sent' && is_null($appointment->whatsapp_sent_at)) {
            $appointment->update(['whatsapp_sent_at' => now()]);
        }

        if ($overallStatus === 'failed') {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp API error: ' . $lastError,
            ], 500);
        }

        $label = $defaultReceipt === 'both' ? 'Both receipts sent' : 'Receipt sent';

        return response()->json([
            'success'          => true,
            'message'          => $label . ' successfully via WhatsApp!',
            'whatsapp_sent_at' => $appointment->fresh()->whatsapp_sent_at?->format('d M Y, H:i'),
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════════
       LOGS INDEX  —  GET whatsapp-logs
    ══════════════════════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('whatsapp.logs.view'), 403);

        if ($request->ajax()) {
            $query = WhatsappLog::with(['appointment', 'sender'])
                ->latest();

            // Optional date filters
            if ($from = $request->input('date_from')) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to = $request->input('date_to')) {
                $query->whereDate('created_at', '<=', $to);
            }
            if ($status = $request->input('filter_status')) {
                $query->where('status', $status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('sent_at_fmt', fn(WhatsappLog $l) =>
                    $l->created_at->format('d M Y, H:i')
                )
                ->addColumn('patient_col', fn(WhatsappLog $l) =>
                    $l->appointment
                        ? '<a href="' . route('appointments.show', $l->appointment_id) . '" class="text-decoration-none fw-semibold">'
                          . e($l->appointment->name) . '</a>'
                          . '<br><small class="text-muted">' . e($l->phone ?? '') . '</small>'
                        : '<span class="text-muted">—</span>'
                )
                ->addColumn('sender_col', fn(WhatsappLog $l) =>
                    $l->sender ? e($l->sender->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('status_col', fn(WhatsappLog $l) =>
                    $l->status === 'sent'
                        ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sent</span>'
                        : '<span class="badge bg-danger" title="' . e($l->error_message ?? '') . '"><i class="bi bi-x-circle me-1"></i>Failed</span>'
                )
                ->addColumn('appointment_col', fn(WhatsappLog $l) =>
                    $l->appointment
                        ? '<a href="' . route('appointments.show', $l->appointment_id) . '" class="btn btn-sm btn-outline-secondary">
                               <i class="bi bi-eye me-1"></i>' . e($l->appointment->serial_series ?? '#' . $l->appointment_id) . '
                           </a>'
                        : '—'
                )
                ->rawColumns(['patient_col', 'sender_col', 'status_col', 'appointment_col'])
                ->make(true);
        }

        // Stats for the summary bar
        $totalSent     = WhatsappLog::sent()->count();
        $totalFailed   = WhatsappLog::failed()->count();
        $sentToday     = WhatsappLog::sent()->today()->count();
        $sentThisMonth = WhatsappLog::sent()->thisMonth()->count();

        return view('admin.whatsapp.logs', compact(
            'totalSent', 'totalFailed', 'sentToday', 'sentThisMonth'
        ));
    }
}
