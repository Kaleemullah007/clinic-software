<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentProduct;
use App\Models\DoctorAgreement;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class AppointmentProductController extends Controller
{
    public function index()
    {
        $this->authorize('appointment-products.view');
        $items = AppointmentProduct::with('appointment', 'product', 'variation', 'addedBy')
            ->latest()->paginate(50);
        return view('admin.appointment-products.index', compact('items'));
    }

    public function create()
    {
        $this->authorize('appointment-products.create');
        $products = Product::where('status', 1)->with('variations')->get(['id','name','price','has_variations']);

        $appointment    = null;
        $existingItems  = collect();
        $appointmentToken = null;

        if ($rawId = request('appointment_id')) {
            $appointment      = Appointment::with(['products.product', 'patient'])->find($rawId);
            $existingItems    = $appointment ? $appointment->products : collect();
            $appointmentToken = encrypt($rawId);
        }

        return view('admin.appointment-products.create', compact(
            'products', 'appointment', 'existingItems', 'appointmentToken'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('appointment-products.create');

        // Decrypt appointment id
        try {
            $appointmentId = decrypt($request->input('appointment_token'));
        } catch (\Exception $e) {
            abort(422, 'Invalid appointment token.');
        }

        $request->validate([
            'appointment_token'          => 'required',
            'products'                   => 'required|array|min:1',
            'products.*.product_name'    => 'required|string|max:255',
            'products.*.quantity'        => 'required|numeric|min:0.01',
            'products.*.unit_price'      => 'required|numeric|min:0',
            'products.*.product_id'      => 'nullable|exists:products,id',
            'products.*.variation_id'    => 'nullable|exists:product_variations,id',
            'products.*.product_code'    => 'nullable|string|max:50',
            'products.*.notes'           => 'nullable|string',
        ]);

        $deductInventory = $request->boolean('deduct_inventory', true);
        $appointment     = Appointment::findOrFail($appointmentId);

        foreach ($request->input('products') as $item) {
            $productId   = $item['product_id']   ?? null;
            $variationId = $item['variation_id'] ?? null;
            $qty         = (float) $item['quantity'];
            $unitPrice   = (float) $item['unit_price'];

            // ── If same product already on this appointment → just increment qty ──
            if ($productId) {
                $existing = AppointmentProduct::where('appointment_id', $appointmentId)
                    ->where('product_id', $productId)
                    ->where(fn($q) => $variationId
                        ? $q->where('variation_id', $variationId)
                        : $q->whereNull('variation_id'))
                    ->first();

                if ($existing) {
                    $newQty = $existing->quantity + $qty;
                    $existing->update([
                        'quantity'    => $newQty,
                        'total_price' => round($newQty * $existing->unit_price, 2),
                    ]);
                    // Deduct the added qty from inventory
                    if ($deductInventory) {
                        $this->deductStock($productId, $variationId, $qty, $unitPrice, $existing->id);
                    }
                    continue;
                }
            }

            // ── New row ─────────────────────────────────────────────────────────
            $rowData = [
                'appointment_id'   => $appointmentId,
                'product_id'       => $productId,
                'variation_id'     => $variationId,
                'product_name'     => $item['product_name'],
                'product_code'     => $item['product_code'] ?? null,
                'quantity'         => $qty,
                'unit_price'       => $unitPrice,
                'total_price'      => round($qty * $unitPrice, 2),
                'deduct_inventory' => $deductInventory,
                'notes'            => $item['notes'] ?? null,
                'added_by'         => auth()->id(),
            ];

            // Doctor/clinic share
            if ($appointment->doctor_id) {
                $agreement = DoctorAgreement::activeFor(
                    $appointment->doctor_id,
                    $appointment->service_id ?? null,
                    $appointment->clinic_id  ?? null
                );
                if ($agreement) {
                    if ($agreement->share_type === 'percentage') {
                        $rowData['doctor_share_amount'] = round($rowData['total_price'] * $agreement->doctor_share / 100, 2);
                        $rowData['clinic_share_amount']  = round($rowData['total_price'] * $agreement->clinic_share  / 100, 2);
                    } else {
                        $rowData['doctor_share_amount'] = $agreement->doctor_share;
                        $rowData['clinic_share_amount']  = $agreement->clinic_share;
                    }
                }
            }

            $apProd = AppointmentProduct::create($rowData);

            if ($deductInventory && $productId) {
                $this->deductStock($productId, $variationId, $qty, $unitPrice, $apProd->id);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Products saved successfully.']);
        }

        return redirect()->route('appointments.show', $appointmentId)
            ->with('success', 'Products added to appointment.');
    }

    /** Deduct qty from inventory + log movement */
    private function deductStock($productId, $variationId, float $qty, float $unitPrice, int $refId): void
    {
        InventoryMovement::create([
            'product_id'     => $productId,
            'variation_id'   => $variationId,
            'type'           => 'appointment_use',
            'quantity'       => -$qty,
            'unit_price'     => $unitPrice,
            'reference_type' => 'appointment_product',
            'reference_id'   => $refId,
            'created_by'     => auth()->id(),
        ]);

        $inv = Inventory::firstOrNew(['product_id' => $productId, 'variation_id' => $variationId]);
        $inv->quantity = max(0, ($inv->quantity ?? 0) - $qty);
        $inv->save();
    }

    public function destroy(AppointmentProduct $appointmentProduct)
    {
        $this->authorize('appointment-products.delete');
        $appointmentProduct->delete();
        return back()->with('success', 'Product removed.');
    }

    /** AJAX: Lookup product by code */
    public function lookupByCode(Request $request)
    {
        $code = $request->input('code','');
        $product = Product::where('status', 1)
            ->where(fn($q) => $q->where('id', $code)->orWhere('name', 'like', "%{$code}%"))
            ->with('variations')
            ->first();
        return response()->json($product ? ['found' => true, 'product' => $product] : ['found' => false]);
    }

    /** Generate WhatsApp-shareable receipt for an appointment */
    public function receipt(Appointment $appointment)
    {
        $this->authorize('appointment-products.view');
        $appointment->load('products.product', 'products.variation', 'patient');

        $whatsappPrefix = \DB::table('settings')->where('key_name', 'whatsapp_prefix')->value('key_value') ?? '+92';
        $receiptMsg     = \DB::table('settings')->where('key_name', 'receipt_message')->value('key_value') ?? 'Thank you for visiting!';

        $lines   = [];
        $lines[] = "🏥 *Receipt - " . config('app.name') . "*";
        $lines[] = "📅 " . now()->format('d M Y');
        $lines[] = "Patient: " . ($appointment->patient->name ?? '—');
        $lines[] = "Appointment: " . ($appointment->appointment_id ?? $appointment->id);
        $lines[] = "─────────────────";

        $total = 0;
        foreach ($appointment->products as $p) {
            $sub    = $p->total_price;
            $total += $sub;
            $lines[] = "• {$p->product_name} x{$p->quantity} = PKR " . number_format($sub, 2);
        }
        $lines[] = "─────────────────";
        $lines[] = "💰 *Total: PKR " . number_format($total, 2) . "*";
        $lines[] = "";
        $lines[] = $receiptMsg;

        $message = implode("\n", $lines);
        $phone   = ltrim($appointment->patient->phone ?? '', '0');
        $phone   = $whatsappPrefix . $phone;
        $waUrl   = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $phone) . '?text=' . rawurlencode($message);

        return view('admin.appointment-products.receipt', compact('appointment', 'waUrl', 'message', 'total'));
    }
}
