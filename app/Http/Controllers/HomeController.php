<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Category;
use App\Models\Clinic;
use App\Models\Inventory;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'avoid-back-history']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // ── Date range ───────────────────────────────────────────────────────
        [$dateFrom, $dateTo, $period] = $this->resolveDateRange($request);

        // ── Base appointment query ────────────────────────────────────────────
        $apptQuery = Appointment::query()
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo);

        if (!$user->isSuperAdmin() && $user->role === 'doctor') {
            $apptQuery->where('doctor_id', $user->id);
        }
        if ($clinicId = $request->input('clinic_id')) {
            $apptQuery->where('clinic_id', $clinicId);
        }
        if ($user->isSuperAdmin() && $doctorId = $request->input('doctor_id')) {
            $apptQuery->where('doctor_id', $doctorId);
        }

        // ── Appointment stats ─────────────────────────────────────────────────
        $totalAppointments = (clone $apptQuery)->count();
        $paidCount         = (clone $apptQuery)->where('is_paid', 'paid')->count();
        $unpaidCount       = $totalAppointments - $paidCount;
        $revenue           = (clone $apptQuery)->sum('subtotal_discounted_price_after_discount');

        // ── Product stats ─────────────────────────────────────────────────────
        $activeProducts   = Product::where('status', 1)->count();
        $inactiveProducts = Product::where('status', 0)->count();
        $totalProducts    = $activeProducts + $inactiveProducts;

        // ── Stock alert threshold (from settings) ─────────────────────────────
        $stockAlertQty = (int) (Setting::where('key_name', 'stock_alert')->value('key_value') ?? 5);

        // Low-stock: tracked products whose inventory qty < threshold
        $lowStockProducts = Product::where('track_inventory', 1)
            ->where('status', 1)
            ->with('inventory')
            ->get()
            ->filter(fn($p) => ($p->inventory?->quantity ?? 0) < $stockAlertQty)
            ->sortBy(fn($p) => $p->inventory?->quantity ?? 0)
            ->values();

        // ── Other stats ───────────────────────────────────────────────────────
        $categories    = Category::count();
        $users         = User::whereNotIn('id', [1, 13, 17, 64])->count();
        $prescriptions = Prescription::count();

        // ── Latest 10 appointments ────────────────────────────────────────────
        $latestAppointments = (clone $apptQuery)
            ->with(['doctor:id,name', 'patient:id,name'])
            ->latest('date')
            ->limit(10)
            ->get();

        // ── Chart 1: appointments per doctor ──────────────────────────────────
        $doctorChartData = (clone $apptQuery)
            ->select('doctor_id', DB::raw('COUNT(*) as total'))
            ->groupBy('doctor_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $doc = User::find($row->doctor_id);
                return ['label' => $doc ? $doc->name : 'Unknown', 'value' => $row->total];
            });

        // ── Chart 2: appointments over time ───────────────────────────────────
        $diffDays = $dateFrom->diffInDays($dateTo);

        $timeChartRaw = (clone $apptQuery)
            ->select(DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as day"), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $timeLabels = [];
        $timeValues = [];
        if ($diffDays <= 31) {
            $cur = $dateFrom->copy();
            while ($cur->lte($dateTo)) {
                $key = $cur->format('Y-m-d');
                $timeLabels[] = $cur->format('d M');
                $timeValues[] = $timeChartRaw[$key] ?? 0;
                $cur->addDay();
            }
        } elseif ($diffDays <= 90) {
            $weekMap = [];
            foreach ($timeChartRaw as $day => $cnt) {
                $wk = Carbon::parse($day)->format('Y-W');
                $weekMap[$wk] = ($weekMap[$wk] ?? 0) + $cnt;
            }
            ksort($weekMap);
            foreach ($weekMap as $wk => $cnt) {
                $timeLabels[] = 'Wk ' . explode('-', $wk)[1];
                $timeValues[] = $cnt;
            }
        } else {
            $monthMap = [];
            foreach ($timeChartRaw as $day => $cnt) {
                $mo = Carbon::parse($day)->format('Y-m');
                $monthMap[$mo] = ($monthMap[$mo] ?? 0) + $cnt;
            }
            ksort($monthMap);
            foreach ($monthMap as $mo => $cnt) {
                $timeLabels[] = Carbon::parse($mo . '-01')->format('M Y');
                $timeValues[] = $cnt;
            }
        }

        // ── Dropdowns ─────────────────────────────────────────────────────────
        $clinics = Clinic::orderBy('name')->get(['id', 'name']);
        $doctors = $user->isSuperAdmin()
            ? User::where('role', 'doctor')->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('admin.dashboard', compact(
            'totalAppointments', 'paidCount', 'unpaidCount', 'revenue',
            'categories', 'users', 'prescriptions',
            'totalProducts', 'activeProducts', 'inactiveProducts',
            'lowStockProducts', 'stockAlertQty',
            'latestAppointments',
            'doctorChartData', 'timeLabels', 'timeValues',
            'clinics', 'doctors',
            'dateFrom', 'dateTo', 'period'
        ));
    }

    private function resolveDateRange(Request $request): array
    {
        $period = $request->input('period', 'this_month');
        switch ($period) {
            case 'this_week':
                $from = Carbon::now()->startOfWeek();
                $to   = Carbon::now()->endOfWeek();
                break;
            case 'last_week':
                $from = Carbon::now()->subWeek()->startOfWeek();
                $to   = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'last_month':
                $from = Carbon::now()->subMonth()->startOfMonth();
                $to   = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $from = Carbon::now()->startOfYear();
                $to   = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $from = Carbon::parse($request->input('date_from', now()->startOfMonth()->toDateString()));
                $to   = Carbon::parse($request->input('date_to',   now()->toDateString()));
                break;
            default:
                $period = 'this_month';
                $from   = Carbon::now()->startOfMonth();
                $to     = Carbon::now()->endOfMonth();
        }
        return [$from, $to, $period];
    }

    public function dashboard()
    {
        return view('home');
    }
}
