<?php

namespace App\Http\Controllers;

use App\Models\AppointmentProduct;
use App\Models\DoctorAgreement;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $this->authorize('reports.view');
        return view('admin.reports.index');
    }

    public function revenue(Request $request)
    {
        $this->authorize('reports.view');
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');

        $query = AppointmentProduct::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                SUM(total_price) as revenue,
                SUM(doctor_share_amount) as doctor_share,
                SUM(clinic_share_amount) as clinic_share
            ')
            ->where(DB::raw('YEAR(created_at)'), $year)
            ->when($month, fn($q) => $q->where(DB::raw('MONTH(created_at)'), $month))
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        $totalRevenue = $query->sum('revenue');
        return view('admin.reports.revenue', compact('query', 'year', 'month', 'totalRevenue'));
    }

    public function inventory(Request $request)
    {
        $this->authorize('reports.view');
        $lowStock  = Inventory::with('product', 'variation')->where('quantity', '<=', 5)->get();
        $allStock  = Inventory::with('product', 'variation')->get();
        $totalValue = $allStock->sum(fn($i) => $i->quantity * $i->cost_price);
        return view('admin.reports.inventory', compact('lowStock', 'allStock', 'totalValue'));
    }

    public function expenses(Request $request)
    {
        $this->authorize('reports.view');
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');

        $data = Expense::selectRaw('category, SUM(amount) as total')
            ->where(DB::raw('YEAR(expense_date)'), $year)
            ->when($month, fn($q) => $q->where(DB::raw('MONTH(expense_date)'), $month))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $monthly = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->where(DB::raw('YEAR(expense_date)'), $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        return view('admin.reports.expenses', compact('data', 'monthly', 'year', 'month'));
    }

    public function salaries(Request $request)
    {
        $this->authorize('reports.view');
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

        return view('admin.reports.salaries', compact('data', 'monthlyTotals', 'year'));
    }

    public function doctorPerformance(Request $request)
    {
        $this->authorize('reports.view');
        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $doctors = User::role('doctor')->get(['id','name']);
        $performance = AppointmentProduct::selectRaw('
                    appointments.doctor_id,
                    COUNT(appointment_products.id) as items_count,
                    SUM(appointment_products.total_price) as revenue,
                    SUM(appointment_products.doctor_share_amount) as doctor_earnings
                ')
            ->join('appointments', 'appointments.id', '=', 'appointment_products.appointment_id')
            ->where(DB::raw('YEAR(appointment_products.created_at)'), $year)
            ->where(DB::raw('MONTH(appointment_products.created_at)'), $month)
            ->groupBy('appointments.doctor_id')
            ->get()
            ->keyBy('doctor_id');

        return view('admin.reports.doctor-performance', compact('doctors', 'performance', 'year', 'month'));
    }
}
