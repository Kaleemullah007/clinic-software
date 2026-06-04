<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentProduct;
use App\Models\AppointmentReturn;
use App\Models\AppointmentService;
use App\Models\Category;
use App\Models\Clinic;
use App\Models\PosOrderItem;
use App\Models\Product;
use App\Models\DoctorAgreement;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\PosOrder;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $this->authorize('reports.view');
        return view('admin.reports.index');
    }

    /* ── helper: check sub-permission, fallback to reports.view for backwards compat ── */
    private function authorizeReport(string $sub): void
    {
        $user = auth()->user();
        if (!$user->can($sub) && !$user->can('reports.view')) {
            abort(403);
        }
    }

    /* ── helper: resolve clinic/doctor scope + filter lists for super admin ── */
    private function scopeData(Request $request): array
    {
        $user     = auth()->user();
        $clinics  = $user->isSuperAdmin() ? Clinic::orderBy('name')->get() : collect();
        $doctors  = $user->isSuperAdmin() ? User::role('doctor')->orderBy('name')->get(['id', 'name']) : collect();
        $clinicId = $user->isSuperAdmin() ? $request->input('clinic_id') : $user->clinic_id;
        $doctorId = $user->isSuperAdmin() ? $request->input('doctor_id') : null;
        return compact('clinics', 'doctors', 'clinicId', 'doctorId');
    }

    /* ══════════════════════════════════════════════════════════════════════
       REVENUE REPORT
    ══════════════════════════════════════════════════════════════════════ */
    public function revenue(Request $request)
    {
        $this->authorize('reports.view');
        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');

        $query = AppointmentProduct::selectRaw('
                YEAR(appointment_products.created_at) as year,
                MONTH(appointment_products.created_at) as month,
                SUM(total_price) as revenue,
                SUM(doctor_share_amount) as doctor_share,
                SUM(clinic_share_amount) as clinic_share
            ')
            ->join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
            ->where(DB::raw('YEAR(appointment_products.created_at)'), $year)
            ->when($month, fn($q) => $q->where(DB::raw('MONTH(appointment_products.created_at)'), $month))
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        $totalRevenue = $query->sum('revenue');
        return view('admin.reports.revenue', compact('query', 'year', 'month', 'totalRevenue', 'clinics', 'doctors'));
    }

    /* ══════════════════════════════════════════════════════════════════════
       INVENTORY REPORT
    ══════════════════════════════════════════════════════════════════════ */
    public function inventory(Request $request)
    {
        $this->authorize('reports.view');
        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $lowStock   = Inventory::with('product', 'variation')->where('quantity', '<=', 5)->get();
        $allStock   = Inventory::with('product', 'variation')->get();
        $totalValue = $allStock->sum(fn($i) => $i->quantity * $i->cost_price);
        return view('admin.reports.inventory', compact('lowStock', 'allStock', 'totalValue', 'clinics', 'doctors'));
    }

    /* ══════════════════════════════════════════════════════════════════════
       EXPENSES REPORT
    ══════════════════════════════════════════════════════════════════════ */
    public function expenses(Request $request)
    {
        $this->authorize('reports.view');
        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');

        $data = Expense::selectRaw('category, SUM(amount) as total')
            ->where(DB::raw('YEAR(expense_date)'), $year)
            ->when($month,    fn($q) => $q->where(DB::raw('MONTH(expense_date)'), $month))
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $monthly = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->where(DB::raw('YEAR(expense_date)'), $year)
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $grandTotal = $data->sum('total');

        return view('admin.reports.expenses', compact(
            'data', 'monthly', 'year', 'month',
            'clinics', 'doctors', 'clinicId', 'grandTotal'
        ));
    }

    /* ══════════════════════════════════════════════════════════════════════
       SALARIES REPORT
    ══════════════════════════════════════════════════════════════════════ */
    public function salaries(Request $request)
    {
        $this->authorize('reports.view');
        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $year = $request->input('year', now()->year);

        $data = Salary::with('user')
            ->where('year', $year)
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        $monthlyTotals = Salary::selectRaw('month, SUM(net_salary) as total')
            ->where('year', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        return view('admin.reports.salaries', compact('data', 'monthlyTotals', 'year', 'clinics', 'doctors'));
    }

    /* ══════════════════════════════════════════════════════════════════════
       DOCTOR PERFORMANCE REPORT
    ══════════════════════════════════════════════════════════════════════ */
    public function doctorPerformance(Request $request)
    {
        $this->authorize('reports.view');
        $scope    = $this->scopeData($request);
        $clinics  = $scope['clinics'];
        $clinicId = $scope['clinicId'];
        $doctorId = $scope['doctorId'];
        // $doctors = full list for both filter UI and performance table rows
        $doctors = User::role('doctor')->get(['id', 'name']);

        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $performance = AppointmentProduct::selectRaw('
                    appointments.doctor_id,
                    COUNT(appointment_products.id) as items_count,
                    SUM(appointment_products.total_price) as revenue,
                    SUM(appointment_products.doctor_share_amount) as doctor_earnings
                ')
            ->join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
            ->where(DB::raw('YEAR(appointment_products.created_at)'), $year)
            ->where(DB::raw('MONTH(appointment_products.created_at)'), $month)
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->groupBy('appointments.doctor_id')
            ->get()
            ->keyBy('doctor_id');

        return view('admin.reports.doctor-performance', compact('doctors', 'performance', 'year', 'month', 'clinics', 'clinicId', 'doctorId'));
    }

    /* ══════════════════════════════════════════════════════════════════════
       APPOINTMENTS REPORT  —  Volume, Paid/Unpaid, Status breakdown
    ══════════════════════════════════════════════════════════════════════ */
    public function appointmentsReport(Request $request)
    {
        $this->authorizeReport('reports.appointments');
        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $year = $request->input('year', now()->year);

        $volume = Appointment::selectRaw('MONTH(date) as month, COUNT(*) as total')
            ->whereYear('date', $year)
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->groupBy('month')
            ->orderBy('month')
            ->get()->keyBy('month');

        $paidCount = Appointment::where('is_paid', 'paid')->whereYear('date', $year)
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->count();

        $unpaidCount = Appointment::where('is_paid', '!=', 'paid')->whereYear('date', $year)
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->count();

        $collected = Appointment::whereYear('date', $year)
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->sum('paid_amount');

        $remaining = Appointment::whereYear('date', $year)
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->sum('remaining_amount');

        $totalAppts = Appointment::whereYear('date', $year)
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->count();

        $patientIds = Appointment::whereYear('date', $year)
            ->whereNotNull('user_id')
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->pluck('user_id');

        $allTimeFirstVisit = Appointment::whereIn('user_id', $patientIds->unique())
            ->groupBy('user_id')
            ->selectRaw('user_id, MIN(date) as first_visit')
            ->get()->keyBy('user_id');

        $newCount       = 0;
        $returningCount = 0;
        foreach ($patientIds->unique() as $pid) {
            $first = $allTimeFirstVisit[$pid]->first_visit ?? null;
            if ($first && Carbon::parse($first)->year == $year) {
                $newCount++;
            } else {
                $returningCount++;
            }
        }

        return view('admin.reports.appointments', compact(
            'year', 'volume', 'paidCount', 'unpaidCount',
            'collected', 'remaining', 'totalAppts', 'newCount', 'returningCount',
            'clinics', 'doctors', 'clinicId', 'doctorId'
        ));
    }

    /* ══════════════════════════════════════════════════════════════════════
       SERVICE REVENUE REPORT  —  Revenue & volume per service
    ══════════════════════════════════════════════════════════════════════ */
    public function serviceRevenue(Request $request)
    {
        $this->authorizeReport('reports.services');
        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $year        = $request->input('year', now()->year);
        $month       = $request->input('month');
        $serviceName = $request->input('service_name');

        // All distinct service names for the dropdown (unfiltered)
        $services = AppointmentService::select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        $data = AppointmentService::selectRaw('
                appointment_services.name as service_name,
                COUNT(*) as booking_count,
                SUM(appointment_services.price) as gross,
                SUM(appointment_services.discounted_price) as revenue,
                SUM(appointment_services.discount) as total_discount
            ')
            ->join('appointments', 'appointments.id', '=', 'appointment_services.appointment_id')
            ->whereYear('appointments.date', $year)
            ->when($month,       fn($q) => $q->whereMonth('appointments.date', $month))
            ->when($clinicId,    fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId,    fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->when($serviceName, fn($q) => $q->where('appointment_services.name', $serviceName))
            ->groupBy('appointment_services.name')
            ->orderByDesc('revenue')
            ->get();

        $totalRevenue  = $data->sum('revenue');
        $totalBookings = $data->sum('booking_count');
        $totalDiscount = $data->sum('total_discount');

        return view('admin.reports.services', compact(
            'data', 'year', 'month', 'totalRevenue', 'totalBookings', 'totalDiscount',
            'clinics', 'doctors', 'clinicId', 'doctorId',
            'services', 'serviceName'
        ));
    }

    /* ══════════════════════════════════════════════════════════════════════
       PATIENTS REPORT  —  Top spenders, new vs returning, retention
    ══════════════════════════════════════════════════════════════════════ */
    public function patients(Request $request)
    {
        $this->authorizeReport('reports.patients');
        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $year          = $request->input('year', now()->year);
        $retentionDays = (int) $request->input('retention_days', 60);

        $topPatients = Appointment::selectRaw('
                user_id, name, phone,
                COUNT(*) as visit_count,
                SUM(paid_amount) as total_paid,
                MAX(date) as last_visit
            ')
            ->whereNotNull('user_id')
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->groupBy('user_id', 'name', 'phone')
            ->orderByDesc('total_paid')
            ->limit(20)
            ->get();

        $yearPatients = Appointment::whereYear('date', $year)
            ->whereNotNull('user_id')
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->pluck('user_id')->unique();

        $firstVisits = Appointment::whereIn('user_id', $yearPatients)
            ->groupBy('user_id')
            ->selectRaw('user_id, MIN(date) as first_visit')
            ->get()->keyBy('user_id');

        $newCount = $returningCount = 0;
        foreach ($yearPatients as $pid) {
            $first = $firstVisits[$pid]->first_visit ?? null;
            Carbon::parse($first)->year == $year ? $newCount++ : $returningCount++;
        }

        $monthlyNew = collect();
        for ($m = 1; $m <= 12; $m++) {
            $ids = Appointment::whereYear('date', $year)->whereMonth('date', $m)
                ->whereNotNull('user_id')
                ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
                ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
                ->pluck('user_id')->unique();
            $count = 0;
            foreach ($ids as $pid) {
                $first = $firstVisits[$pid]->first_visit ?? null;
                if ($first && Carbon::parse($first)->year == $year
                    && Carbon::parse($first)->month == $m) {
                    $count++;
                }
            }
            $monthlyNew[$m] = $count;
        }

        $cutoff = now()->subDays($retentionDays)->toDateString();
        $lostPatients = Appointment::selectRaw('
                user_id, name, phone,
                COUNT(*) as total_visits,
                MAX(date) as last_visit,
                SUM(paid_amount) as lifetime_value
            ')
            ->whereNotNull('user_id')
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->groupBy('user_id', 'name', 'phone')
            ->havingRaw('MAX(date) < ?', [$cutoff])
            ->orderBy('last_visit', 'desc')
            ->limit(25)
            ->get();

        return view('admin.reports.patients', compact(
            'year', 'topPatients', 'newCount', 'returningCount',
            'monthlyNew', 'lostPatients', 'retentionDays',
            'clinics', 'doctors', 'clinicId', 'doctorId'
        ));
    }

    /* ══════════════════════════════════════════════════════════════════════
       PRODUCTS SOLD REPORT  —  Sales, returns, return rate
    ══════════════════════════════════════════════════════════════════════ */
    public function productsSold(Request $request)
    {
        $this->authorizeReport('reports.products-sold');
        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');

        $sold = AppointmentProduct::selectRaw('
                product_name,
                SUM(quantity) as qty_sold,
                SUM(total_price) as revenue,
                SUM(doctor_share_amount) as doctor_share,
                SUM(clinic_share_amount) as clinic_share,
                COUNT(DISTINCT appointment_id) as in_appointments
            ')
            ->join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
            ->whereYear('appointment_products.created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('appointment_products.created_at', $month))
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->get();

        $returns = AppointmentReturn::selectRaw('
                appointment_products.product_name,
                SUM(appointment_returns.quantity) as returned_qty,
                SUM(appointment_returns.refund_amount) as refund_total
            ')
            ->join('appointment_products', 'appointment_products.id', '=', 'appointment_returns.appointment_product_id')
            ->whereYear('appointment_returns.created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('appointment_returns.created_at', $month))
            ->groupBy('appointment_products.product_name')
            ->get()->keyBy('product_name');

        $sold->each(function ($row) use ($returns) {
            $ret = $returns[$row->product_name] ?? null;
            $row->returned_qty = $ret?->returned_qty ?? 0;
            $row->refund_total = $ret?->refund_total ?? 0;
            $row->return_rate  = $row->qty_sold > 0
                ? round(($row->returned_qty / $row->qty_sold) * 100, 1)
                : 0;
        });

        $totalRevenue = $sold->sum('revenue');
        $totalQty     = $sold->sum('qty_sold');
        $totalRefunds = $sold->sum('refund_total');

        return view('admin.reports.products-sold', compact(
            'sold', 'year', 'month', 'totalRevenue', 'totalQty', 'totalRefunds',
            'clinics', 'doctors', 'clinicId', 'doctorId'
        ));
    }

    /* ══════════════════════════════════════════════════════════════════════
       SUMMARY / P&L REPORT  —  Total income, expenses, net profit
    ══════════════════════════════════════════════════════════════════════ */
    public function summary(Request $request)
    {
        $this->authorize('reports.view');
        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);

        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->input('to',   now()->format('Y-m-d'));

        // ── INCOME ───────────────────────────────────────────────────────
        $serviceRevenue = (float) AppointmentService::join('appointments', 'appointments.id', '=', 'appointment_services.appointment_id')
            ->whereBetween('appointments.date', [$from, $to])
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->sum('appointment_services.discounted_price');

        $productRevenue = (float) AppointmentProduct::join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
            ->whereBetween('appointments.date', [$from, $to])
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->sum('appointment_products.total_price');

        $posSales = (float) PosOrder::where('payment_status', 'paid')
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
            ->sum('grand_total');

        $totalIncome = $serviceRevenue + $productRevenue + $posSales;

        // ── EXPENSES ─────────────────────────────────────────────────────
        $businessExpenses = (float) Expense::whereBetween('expense_date', [$from, $to])->sum('amount');

        $salaryCosts = (float) Salary::whereRaw(
            "CONCAT(year, '-', LPAD(month, 2, '0'), '-01') BETWEEN ? AND ?",
            [$from, $to]
        )->sum('net_salary');

        $doctorShares = (float) AppointmentProduct::join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
            ->whereBetween('appointments.date', [$from, $to])
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->sum('appointment_products.doctor_share_amount');

        $productCogs = (float) AppointmentProduct::join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
            ->leftJoin('inventory', function ($join) {
                $join->on('inventory.product_id', '=', 'appointment_products.product_id')
                     ->whereNull('inventory.variation_id');
            })
            ->whereBetween('appointments.date', [$from, $to])
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->selectRaw('SUM(appointment_products.quantity * COALESCE(inventory.cost_price, 0)) as cogs')
            ->value('cogs');

        $totalExpenses = $businessExpenses + $salaryCosts + $doctorShares + $productCogs;
        $netProfit     = $totalIncome - $totalExpenses;

        // ── MONTHLY TREND ─────────────────────────────────────────────────
        $trendMonths  = [];
        $trendIncome  = [];
        $trendExpense = [];
        $trendProfit  = [];

        $cursor  = Carbon::parse($from)->startOfMonth();
        $endDate = Carbon::parse($to)->endOfMonth();

        while ($cursor <= $endDate) {
            $mFrom = $cursor->copy()->startOfMonth()->format('Y-m-d');
            $mTo   = $cursor->copy()->endOfMonth()->format('Y-m-d');

            $mService = (float) AppointmentService::join('appointments', 'appointments.id', '=', 'appointment_services.appointment_id')
                ->whereBetween('appointments.date', [$mFrom, $mTo])
                ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
                ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
                ->sum('appointment_services.discounted_price');

            $mProduct = (float) AppointmentProduct::join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
                ->whereBetween('appointments.date', [$mFrom, $mTo])
                ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
                ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
                ->sum('appointment_products.total_price');

            $mPos = (float) PosOrder::where('payment_status', 'paid')
                ->whereBetween(DB::raw('DATE(created_at)'), [$mFrom, $mTo])
                ->when($clinicId, fn($q) => $q->where('clinic_id', $clinicId))
                ->sum('grand_total');

            $mIncome = $mService + $mProduct + $mPos;

            $mBizExp   = (float) Expense::whereBetween('expense_date', [$mFrom, $mTo])->sum('amount');
            $mSalary   = (float) Salary::whereRaw(
                "CONCAT(year, '-', LPAD(month, 2, '0'), '-01') BETWEEN ? AND ?",
                [$mFrom, $mTo]
            )->sum('net_salary');
            $mDocShare = (float) AppointmentProduct::join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
                ->whereBetween('appointments.date', [$mFrom, $mTo])
                ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
                ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
                ->sum('appointment_products.doctor_share_amount');

            $mExpense = $mBizExp + $mSalary + $mDocShare;

            $trendMonths[]  = $cursor->format('M Y');
            $trendIncome[]  = round($mIncome, 2);
            $trendExpense[] = round($mExpense, 2);
            $trendProfit[]  = round($mIncome - $mExpense, 2);

            $cursor->addMonth();
        }

        return view('admin.reports.summary', compact(
            'clinics', 'doctors', 'from', 'to', 'clinicId', 'doctorId',
            'serviceRevenue', 'productRevenue', 'posSales', 'totalIncome',
            'businessExpenses', 'salaryCosts', 'doctorShares', 'productCogs',
            'totalExpenses', 'netProfit',
            'trendMonths', 'trendIncome', 'trendExpense', 'trendProfit'
        ));
    }

    /* ══════════════════════════════════════════════════════════════════════
       SERVICE GAP REPORT  —  Top services vs unused/dead services
    ══════════════════════════════════════════════════════════════════════ */
    public function serviceGap(Request $request)
    {
        $this->authorize('reports.service-gap');

        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');

        // ── Top services booked in the selected period ────────────────────
        $topServices = AppointmentService::selectRaw('
                appointment_services.service_id,
                appointment_services.name   AS service_name,
                COUNT(*)                    AS booking_count,
                SUM(appointment_services.discounted_price) AS revenue,
                SUM(appointment_services.discount)         AS total_discount,
                MAX(appointments.date)      AS last_booked
            ')
            ->join('appointments', 'appointments.id', '=', 'appointment_services.appointment_id')
            ->whereYear('appointments.date', $year)
            ->when($month,    fn($q) => $q->whereMonth('appointments.date', $month))
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->groupBy('appointment_services.service_id', 'appointment_services.name')
            ->orderByDesc('booking_count')
            ->get();

        // ── All services in the master categories table ───────────────────
        $allServices = Category::orderBy('name')->get(['id', 'name', 'price', 'status']);

        // ── Service IDs booked this period ───────────────────────────────
        $bookedIds = $topServices->pluck('service_id')->filter()->unique();

        // ── Unused = in categories but NOT booked this period ────────────
        $unusedServices = $allServices->whereNotIn('id', $bookedIds)->values();

        // ── All-time last-booked per service (context for unused list) ────
        $everBooked = AppointmentService::selectRaw('
                service_id,
                MAX(appointments.date) AS last_booked_ever
            ')
            ->join('appointments', 'appointments.id', '=', 'appointment_services.appointment_id')
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->groupBy('service_id')
            ->get()
            ->keyBy('service_id');

        $totalServices = $allServices->count();
        $activeCount   = $bookedIds->count();
        $unusedCount   = $unusedServices->count();
        $totalRevenue  = $topServices->sum('revenue');
        $totalBookings = $topServices->sum('booking_count');

        return view('admin.reports.service-gap', compact(
            'topServices', 'unusedServices', 'everBooked', 'allServices',
            'year', 'month', 'clinics', 'doctors', 'clinicId', 'doctorId',
            'totalServices', 'activeCount', 'unusedCount', 'totalRevenue', 'totalBookings'
        ));
    }

    /* ══════════════════════════════════════════════════════════════════════
       PRODUCT GAP REPORT  —  Dead products across appointments & POS
    ══════════════════════════════════════════════════════════════════════ */
    public function productGap(Request $request)
    {
        $this->authorize('reports.product-gap');

        ['clinics' => $clinics, 'doctors' => $doctors, 'clinicId' => $clinicId, 'doctorId' => $doctorId] = $this->scopeData($request);
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');

        // ── Master product list ───────────────────────────────────────────
        $allProducts = Product::with('inventory')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'has_variations', 'track_inventory', 'status']);

        // ── Products used in appointments (period) ───────────────────────
        $apptActive = AppointmentProduct::selectRaw('
                appointment_products.product_id,
                appointment_products.product_name,
                COUNT(*) as usage_count,
                SUM(appointment_products.total_price) as revenue,
                MAX(appointments.date) as last_used
            ')
            ->join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
            ->whereYear('appointments.date', $year)
            ->when($month,    fn($q) => $q->whereMonth('appointments.date', $month))
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->whereNotNull('appointment_products.product_id')
            ->groupBy('appointment_products.product_id', 'appointment_products.product_name')
            ->orderByDesc('usage_count')
            ->get();

        // ── Products sold in POS (period) ────────────────────────────────
        $posActive = PosOrderItem::selectRaw('
                pos_order_items.product_id,
                pos_order_items.product_name,
                SUM(pos_order_items.quantity) as qty_sold,
                SUM(pos_order_items.line_total) as revenue,
                MAX(pos_orders.created_at) as last_sold
            ')
            ->join('pos_orders', 'pos_orders.id', '=', 'pos_order_items.pos_order_id')
            ->whereYear('pos_orders.created_at', $year)
            ->when($month,    fn($q) => $q->whereMonth('pos_orders.created_at', $month))
            ->when($clinicId, fn($q) => $q->where('pos_orders.clinic_id', $clinicId))
            ->whereNotNull('pos_order_items.product_id')
            ->groupBy('pos_order_items.product_id', 'pos_order_items.product_name')
            ->orderByDesc('qty_sold')
            ->get();

        $apptIds = $apptActive->pluck('product_id')->filter()->unique();
        $posIds  = $posActive->pluck('product_id')->filter()->unique();

        // ── Dead product sets ─────────────────────────────────────────────
        $deadBoth = $allProducts->filter(fn($p) => !$apptIds->contains($p->id) && !$posIds->contains($p->id))->values();
        $deadAppt = $allProducts->filter(fn($p) => !$apptIds->contains($p->id))->values();
        $deadPos  = $allProducts->filter(fn($p) => !$posIds->contains($p->id))->values();

        // ── All-time last used (context for dead products) ────────────────
        $everAppt = AppointmentProduct::selectRaw('product_id, MAX(appointments.date) as last_used')
            ->join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
            ->when($clinicId, fn($q) => $q->where('appointments.clinic_id', $clinicId))
            ->when($doctorId, fn($q) => $q->where('appointments.doctor_id', $doctorId))
            ->whereNotNull('appointment_products.product_id')
            ->groupBy('product_id')
            ->get()->keyBy('product_id');

        $everPos = PosOrderItem::selectRaw('product_id, MAX(pos_orders.created_at) as last_sold')
            ->join('pos_orders', 'pos_orders.id', '=', 'pos_order_items.pos_order_id')
            ->when($clinicId, fn($q) => $q->where('pos_orders.clinic_id', $clinicId))
            ->whereNotNull('pos_order_items.product_id')
            ->groupBy('product_id')
            ->get()->keyBy('product_id');

        return view('admin.reports.product-gap', compact(
            'allProducts', 'apptActive', 'posActive',
            'deadBoth', 'deadAppt', 'deadPos',
            'everAppt', 'everPos', 'apptIds', 'posIds',
            'year', 'month', 'clinics', 'doctors', 'clinicId', 'doctorId'
        ));
    }
}
