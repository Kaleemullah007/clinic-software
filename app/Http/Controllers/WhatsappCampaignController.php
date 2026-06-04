<?php

namespace App\Http\Controllers;

use App\Jobs\DispatchCampaignJob;
use App\Models\Clinic;
use App\Models\User;
use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignLog;
use App\Models\WhatsappTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class WhatsappCampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'avoid-back-history']);
    }

    /* ════════════════════════════════════════════════════════════════════
       CAMPAIGNS — INDEX
    ════════════════════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.view'), 403);

        if ($request->ajax()) {
            $query = WhatsappCampaign::with(['template', 'creator'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name_col', fn($c) =>
                    '<div class="fw-semibold">' . e($c->name) . '</div>'
                    . '<small class="text-muted">' . e($c->template?->name ?? '—') . '</small>'
                )
                ->addColumn('target_col', function ($c) {
                    $html = '<span class="badge bg-light text-dark border">' . ucfirst($c->target_role) . '</span>';
                    if ($c->clinic)  $html .= ' <span class="badge bg-light text-dark border">' . e($c->clinic->name ?? '') . '</span>';
                    if ($c->doctor)  $html .= ' <span class="badge bg-light text-dark border">' . e($c->doctor->name ?? '') . '</span>';
                    return $html;
                })
                ->addColumn('scheduled_col', fn($c) =>
                    '<div class="small">' . $c->scheduled_at->format('d M Y') . '</div>'
                    . '<small class="text-muted">' . $c->scheduled_at->format('H:i') . ' ' . $c->timezone . '</small>'
                )
                ->addColumn('status_col', function ($c) {
                    return match($c->status) {
                        'draft'     => '<span class="badge bg-secondary">Draft</span>',
                        'scheduled' => '<span class="badge bg-info text-dark">Scheduled</span>',
                        'running'   => '<span class="badge bg-warning text-dark"><span class="spinner-border spinner-border-sm me-1" style="width:.65rem;height:.65rem"></span>Running</span>',
                        'completed' => '<span class="badge bg-success">Completed</span>',
                        'failed'    => '<span class="badge bg-danger">Failed</span>',
                        default     => '<span class="badge bg-light text-dark">' . $c->status . '</span>',
                    };
                })
                ->addColumn('stats_col', function ($c) {
                    $pct = $c->total_recipients > 0
                        ? round(($c->sent_count + $c->failed_count) / $c->total_recipients * 100)
                        : 0;
                    return '<div class="d-flex gap-2 align-items-center">'
                        . '<span class="text-success fw-bold">' . $c->sent_count . '</span>'
                        . '/<span class="text-warning fw-bold">' . $c->pending_count . '</span>'
                        . '/<span class="text-danger fw-bold">' . $c->failed_count . '</span>'
                        . '</div>'
                        . '<div class="progress mt-1" style="height:4px;width:100px">'
                        . '<div class="progress-bar bg-success" style="width:' . $pct . '%"></div>'
                        . '</div>'
                        . '<small class="text-muted">' . $c->total_recipients . ' recipients</small>';
                })
                ->addColumn('action', function ($c) {
                    $show = '<a href="' . route('whatsapp-campaign.show', $c->id) . '" class="btn btn-sm btn-outline-secondary me-1" title="View Stats"><i class="bi bi-eye"></i></a>';
                    $del  = auth()->user()->can('whatsapp-campaign.create')
                        ? '<button class="btn btn-sm btn-outline-danger btn-delete-campaign" data-id="' . $c->id . '" title="Delete"><i class="bi bi-trash3"></i></button>'
                        : '';
                    return $show . $del;
                })
                ->rawColumns(['name_col','target_col','scheduled_col','status_col','stats_col','action'])
                ->make(true);
        }

        // Stats for summary bar
        $totalCampaigns   = WhatsappCampaign::count();
        $running          = WhatsappCampaign::where('status', 'running')->count();
        $completed        = WhatsappCampaign::where('status', 'completed')->count();
        $totalSent        = WhatsappCampaignLog::sent()->count();

        return view('admin.whatsapp-campaign.index', compact(
            'totalCampaigns', 'running', 'completed', 'totalSent'
        ));
    }

    /* ════════════════════════════════════════════════════════════════════
       CAMPAIGNS — CREATE
    ════════════════════════════════════════════════════════════════════ */
    public function create()
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.create'), 403);

        $templates = WhatsappTemplate::active()->orderBy('name')->get(['id','name','message_type']);
        $roles     = User::select('role')->distinct()->whereNotNull('role')->pluck('role');
        $clinics   = auth()->user()->isSuperAdmin() ? Clinic::orderBy('name')->get(['id','name']) : collect();
        $doctors   = auth()->user()->isSuperAdmin() ? User::where('role','doctor')->orderBy('name')->get(['id','name']) : collect();
        $timezones = ['Asia/Karachi','Asia/Kolkata','Asia/Dubai','Asia/Riyadh','UTC','Europe/London','America/New_York'];

        return view('admin.whatsapp-campaign.create', compact('templates','roles','clinics','doctors','timezones'));
    }

    /* ════════════════════════════════════════════════════════════════════
       CAMPAIGNS — STORE
    ════════════════════════════════════════════════════════════════════ */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.create'), 403);

        $request->validate([
            'name'          => 'required|string|max:255',
            'template_id'   => 'required|exists:whatsapp_templates,id',
            'target_role'   => 'required|string',
            'scheduled_at'  => 'required|date',
            'timezone'      => 'required|string',
            'message_delay' => 'required|integer|min:0|max:60',
            'clinic_id'     => 'nullable|exists:clinics,id',
            'doctor_id'     => 'nullable|exists:users,id',
        ]);

        // Convert scheduled_at from selected timezone to UTC for storage
        $scheduledAt = Carbon::createFromFormat(
            'Y-m-d\TH:i',
            $request->scheduled_at,
            $request->timezone
        )->utc();

        $campaign = WhatsappCampaign::create([
            'name'          => $request->name,
            'template_id'   => $request->template_id,
            'target_role'   => $request->target_role,
            'clinic_id'     => auth()->user()->isSuperAdmin() ? $request->clinic_id : null,
            'doctor_id'     => auth()->user()->isSuperAdmin() ? $request->doctor_id : null,
            'scheduled_at'  => $scheduledAt,
            'timezone'      => $request->timezone,
            'message_delay' => $request->message_delay,
            'status'        => 'scheduled',
            'created_by'    => auth()->id(),
        ]);

        // Dispatch with delay from now until scheduled_at
        $delaySeconds = max(0, now()->diffInSeconds($scheduledAt, false));
        DispatchCampaignJob::dispatch($campaign->id)->delay($delaySeconds > 0 ? now()->addSeconds($delaySeconds) : now());

        return redirect()->route('whatsapp-campaign.index')
            ->with('success', 'Campaign "' . $campaign->name . '" scheduled successfully!');
    }

    /* ════════════════════════════════════════════════════════════════════
       CAMPAIGNS — SHOW (Stats)
    ════════════════════════════════════════════════════════════════════ */
    public function show(Request $request, WhatsappCampaign $whatsappCampaign)
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.view'), 403);

        $campaign = $whatsappCampaign->load('template', 'creator', 'clinic', 'doctor');

        if ($request->ajax()) {
            $query = WhatsappCampaignLog::where('campaign_id', $campaign->id)->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('recipient_col', fn($l) =>
                    '<div class="fw-semibold small">' . e($l->recipient_name ?? '—') . '</div>'
                    . '<small class="text-muted">' . e($l->phone) . '</small>'
                )
                ->addColumn('status_col', fn($l) =>
                    match($l->status) {
                        'sent'    => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sent</span>',
                        'failed'  => '<span class="badge bg-danger" title="' . e($l->error_message ?? '') . '"><i class="bi bi-x-circle me-1"></i>Failed</span>',
                        default   => '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>',
                    }
                )
                ->addColumn('sent_at_col', fn($l) =>
                    $l->sent_at ? $l->sent_at->format('d M Y, H:i') : '<span class="text-muted">—</span>'
                )
                ->rawColumns(['recipient_col','status_col','sent_at_col'])
                ->make(true);
        }

        return view('admin.whatsapp-campaign.show', compact('campaign'));
    }

    /* ════════════════════════════════════════════════════════════════════
       CAMPAIGNS — DESTROY
    ════════════════════════════════════════════════════════════════════ */
    public function destroy(WhatsappCampaign $whatsappCampaign)
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.create'), 403);
        $whatsappCampaign->delete();
        return response()->json(['success' => true]);
    }

    /* ════════════════════════════════════════════════════════════════════
       TEMPLATES — INDEX
    ════════════════════════════════════════════════════════════════════ */
    public function templatesIndex(Request $request)
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.templates'), 403);

        if ($request->ajax()) {
            $query = WhatsappTemplate::with('creator')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name_col', fn($t) =>
                    '<div class="fw-semibold">' . e($t->name) . '</div>'
                    . '<small class="text-muted">' . e($t->creator?->name ?? '—') . '</small>'
                )
                ->addColumn('type_col', fn($t) =>
                    match($t->message_type) {
                        'text'  => '<span class="badge bg-info text-dark"><i class="bi bi-chat-text me-1"></i>Text</span>',
                        'image' => '<span class="badge bg-purple" style="background:#8b5cf6"><i class="bi bi-image me-1"></i>Image</span>',
                        'both'  => '<span class="badge" style="background:#B1083C;color:#fff"><i class="bi bi-file-image me-1"></i>Text + Image</span>',
                        default => '<span class="badge bg-secondary">' . $t->message_type . '</span>',
                    }
                )
                ->addColumn('preview_col', function ($t) {
                    $preview = '';
                    if ($t->message_body) {
                        $preview .= '<p class="small text-muted mb-1" style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">'
                            . e($t->message_body) . '</p>';
                    }
                    if ($t->image_path) {
                        $preview .= '<button class="btn btn-xs btn-outline-secondary btn-preview-img" '
                            . 'data-img="' . e($t->image_url) . '" data-name="' . e($t->name) . '" '
                            . 'style="font-size:.75rem;padding:2px 8px">'
                            . '<i class="bi bi-eye me-1"></i>Preview Image</button>';
                    }
                    return $preview ?: '<span class="text-muted small">—</span>';
                })
                ->addColumn('status_col', fn($t) =>
                    $t->status === 'active'
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>'
                )
                ->addColumn('action', function ($t) {
                    $edit = '<a href="' . route('whatsapp-campaign.templates.edit', $t->id) . '" class="btn btn-sm btn-outline-theme me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>';
                    $del  = '<button class="btn btn-sm btn-outline-danger btn-delete-template" data-id="' . $t->id . '" title="Delete"><i class="bi bi-trash3"></i></button>';
                    return $edit . $del;
                })
                ->rawColumns(['name_col','type_col','preview_col','status_col','action'])
                ->make(true);
        }

        return view('admin.whatsapp-campaign.templates.index');
    }

    /* ════════════════════════════════════════════════════════════════════
       TEMPLATES — CREATE / STORE
    ════════════════════════════════════════════════════════════════════ */
    public function templatesCreate()
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.templates'), 403);
        return view('admin.whatsapp-campaign.templates.create');
    }

    public function templatesStore(Request $request)
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.templates'), 403);

        $request->validate([
            'name'         => 'required|string|max:255',
            'message_type' => 'required|in:text,image,both',
            'message_body' => 'required_if:message_type,text|required_if:message_type,both|nullable|string',
            'image'        => 'required_if:message_type,image|required_if:message_type,both|nullable|image|max:5120',
            'status'       => 'required|in:active,inactive',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('campaign-images', 'public');
        }

        WhatsappTemplate::create([
            'name'         => $request->name,
            'message_type' => $request->message_type,
            'message_body' => $request->message_body,
            'image_path'   => $imagePath,
            'status'       => $request->status,
            'created_by'   => auth()->id(),
        ]);

        return redirect()->route('whatsapp-campaign.templates')
            ->with('success', 'Template created successfully!');
    }

    /* ════════════════════════════════════════════════════════════════════
       TEMPLATES — EDIT / UPDATE
    ════════════════════════════════════════════════════════════════════ */
    public function templatesEdit(WhatsappTemplate $template)
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.templates'), 403);
        return view('admin.whatsapp-campaign.templates.create', compact('template'));
    }

    public function templatesUpdate(Request $request, WhatsappTemplate $template)
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.templates'), 403);

        $request->validate([
            'name'         => 'required|string|max:255',
            'message_type' => 'required|in:text,image,both',
            'message_body' => 'required_if:message_type,text|required_if:message_type,both|nullable|string',
            'image'        => 'nullable|image|max:5120',
            'status'       => 'required|in:active,inactive',
        ]);

        $imagePath = $template->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $request->file('image')->store('campaign-images', 'public');
        }

        $template->update([
            'name'         => $request->name,
            'message_type' => $request->message_type,
            'message_body' => $request->message_body,
            'image_path'   => $imagePath,
            'status'       => $request->status,
        ]);

        return redirect()->route('whatsapp-campaign.templates')
            ->with('success', 'Template updated successfully!');
    }

    /* ════════════════════════════════════════════════════════════════════
       TEMPLATES — DESTROY
    ════════════════════════════════════════════════════════════════════ */
    public function templatesDestroy(WhatsappTemplate $template)
    {
        abort_unless(auth()->user()->can('whatsapp-campaign.templates'), 403);
        if ($template->image_path) Storage::disk('public')->delete($template->image_path);
        $template->delete();
        return response()->json(['success' => true]);
    }
}
