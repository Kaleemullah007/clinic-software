<?php

namespace App\Http\Controllers;

use App\Models\DeviceApproval;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class DeviceApprovalController extends Controller
{
    /* ── Only superadmin can access the management pages ──────────────── */
    private function requireSuperAdmin()
    {
        $this->authorize('device-approvals.manage');
    }

    /* ══════════════════════════════════════════════════════════════════
       USER-FACING — Pending page (no auth required)
    ══════════════════════════════════════════════════════════════════ */
    public function pendingPage(Request $request)
    {
        $code = session('device_pending_code');
        $device = $code ? DeviceApproval::where('code', $code)->first() : null;

        return view('auth.device-pending', compact('code', 'device'));
    }

    /* ── AJAX: check status of a device code ─────────────────────────── */
    public function checkStatus(Request $request)
    {
        $code   = $request->input('code');
        $device = DeviceApproval::where('code', $code)->first();

        if (! $device) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json(['status' => $device->status]);
    }

    /* ══════════════════════════════════════════════════════════════════
       SUPERADMIN — Management index
    ══════════════════════════════════════════════════════════════════ */
    public function index()
    {
        $this->requireSuperAdmin();

        $counts = [
            'all'      => DeviceApproval::count(),
            'pending'  => DeviceApproval::where('status', 'pending')->count(),
            'approved' => DeviceApproval::where('status', 'approved')->count(),
            'rejected' => DeviceApproval::where('status', 'rejected')->count(),
        ];

        $deviceApprovalEnabled = Setting::where('key_name', 'device_approval_enabled')->value('key_value') ?? '0';

        return view('admin.device-approvals.index', compact('counts', 'deviceApprovalEnabled'));
    }

    /* ── AJAX DataTables data ─────────────────────────────────────────── */
    public function data(Request $request)
    {
        $this->requireSuperAdmin();

        $status = $request->input('status', 'all');

        $query = DeviceApproval::with(['actionedBy:id,name'])
            ->select(
                'device_approvals.*',
                'users.name  as u_name',
                'users.email as u_email',
                'users.role  as u_role'
            )
            ->leftJoin('users', 'users.id', '=', 'device_approvals.user_id');

        if ($status !== 'all') {
            $query->where('device_approvals.status', $status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('code_col', fn($d) =>
                '<span class="badge" style="background:#B1083C;font-family:monospace;font-size:.8rem;letter-spacing:1px">'
                . e($d->code) . '</span>'
            )
            ->addColumn('user_col', function ($d) {
                $name  = e($d->u_name  ?? '—');
                $email = e($d->u_email ?? '');
                $role  = e(ucfirst($d->u_role ?? ''));
                return "<div class='fw-semibold'>{$name}</div>"
                     . "<div class='small text-muted'>{$email}</div>"
                     . "<span class='badge bg-light text-dark border small'>{$role}</span>";
            })
            ->addColumn('device_col', function ($d) {
                $browser = e($d->browser_label);
                $os      = e($d->os_label);
                $ip      = e($d->ip_address ?? '—');
                return "<div class='fw-semibold'><i class='bi bi-display me-1'></i>{$browser} on {$os}</div>"
                     . "<div class='small text-muted'><i class='bi bi-geo me-1'></i>{$ip}</div>";
            })
            ->addColumn('status_col', function ($d) {
                return match($d->status) {
                    'pending'  => '<span class="badge bg-warning text-dark">Pending</span>',
                    'approved' => '<span class="badge bg-success">Approved</span>',
                    'rejected' => '<span class="badge bg-danger">Rejected</span>',
                    default    => '<span class="badge bg-secondary">' . e($d->status) . '</span>',
                };
            })
            ->addColumn('actioned_col', function ($d) {
                if (! $d->actioned_at) return '<span class="text-muted small">—</span>';
                $by   = e($d->actionedBy?->name ?? '—');
                $when = $d->actioned_at->format('d M Y H:i');
                return "<div class='small fw-semibold'>{$by}</div><div class='small text-muted'>{$when}</div>";
            })
            ->addColumn('actions', function ($d) {
                $approve = $reject = $revoke = '';
                if ($d->status === 'pending') {
                    $approve = '<button class="btn btn-sm btn-success me-1 btn-approve" data-id="'.$d->id.'" title="Approve"><i class="bi bi-check-lg"></i></button>';
                    $reject  = '<button class="btn btn-sm btn-danger btn-reject" data-id="'.$d->id.'" title="Reject"><i class="bi bi-x-lg"></i></button>';
                }
                if ($d->status === 'approved') {
                    $revoke = '<button class="btn btn-sm btn-warning btn-revoke" data-id="'.$d->id.'" title="Revoke"><i class="bi bi-slash-circle me-1"></i>Revoke</button>';
                }
                if ($d->status === 'rejected') {
                    $approve = '<button class="btn btn-sm btn-success btn-approve" data-id="'.$d->id.'" title="Re-approve"><i class="bi bi-check-lg me-1"></i>Re-approve</button>';
                }
                return $approve . $reject . $revoke;
            })
            ->rawColumns(['code_col', 'user_col', 'device_col', 'status_col', 'actioned_col', 'actions'])
            ->make(true);
    }

    /* ── Approve ──────────────────────────────────────────────────────── */
    public function approve($id)
    {
        $this->requireSuperAdmin();
        $device = DeviceApproval::findOrFail($id);
        $device->update([
            'status'      => 'approved',
            'actioned_by' => auth()->id(),
            'actioned_at' => now(),
        ]);
        return response()->json(['status' => 'success', 'message' => 'Device approved successfully.']);
    }

    /* ── Reject ───────────────────────────────────────────────────────── */
    public function reject($id)
    {
        $this->requireSuperAdmin();
        $device = DeviceApproval::findOrFail($id);
        $device->update([
            'status'      => 'rejected',
            'actioned_by' => auth()->id(),
            'actioned_at' => now(),
        ]);
        return response()->json(['status' => 'success', 'message' => 'Device rejected.']);
    }

    /* ── Revoke (reset approved → pending) ───────────────────────────── */
    public function revoke($id)
    {
        $this->requireSuperAdmin();
        $device = DeviceApproval::findOrFail($id);
        $device->update([
            'status'      => 'pending',
            'actioned_by' => null,
            'actioned_at' => null,
        ]);
        return response()->json(['status' => 'success', 'message' => 'Device access revoked.']);
    }

    /* ── Toggle the global setting ───────────────────────────────────── */
    public function toggleSetting(Request $request)
    {
        $this->requireSuperAdmin();

        $enabled = $request->boolean('enabled') ? '1' : '0';

        Setting::updateOrCreate(
            ['key_name' => 'device_approval_enabled'],
            ['key_value' => $enabled, 'status' => 1]
        );

        Cache::forget('device_approval_enabled');

        return response()->json([
            'status'  => 'success',
            'enabled' => $enabled,
            'message' => $enabled === '1'
                ? 'Device approval is now ENABLED.'
                : 'Device approval is now DISABLED.',
        ]);
    }
}
