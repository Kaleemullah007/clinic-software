<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('salaries.view');

        if ($request->ajax()) {
            $query = Salary::with(['user:id,name', 'processedBy:id,name'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('staff_col', fn (Salary $s) =>
                    $s->user ? e($s->user->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('period_col', fn (Salary $s) =>
                    \Carbon\Carbon::create($s->year, $s->month)->format('M Y')
                )
                ->addColumn('amount_col', fn (Salary $s) =>
                    'PKR ' . number_format($s->net_salary ?? $s->amount ?? 0, 2)
                )
                ->addColumn('status_badge', fn (Salary $s) =>
                    match($s->status ?? 'pending') {
                        'paid'    => '<span class="badge bg-success">Paid</span>',
                        'partial' => '<span class="badge bg-warning text-dark">Partial</span>',
                        default   => '<span class="badge bg-secondary">Pending</span>',
                    }
                )
                ->addColumn('processor_col', fn (Salary $s) =>
                    $s->processedBy ? e($s->processedBy->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('action', function (Salary $s) {
                    $view = '<a href="' . route('salaries.show', $s->id) . '" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-eye"></i></a>';
                    $del = auth()->user()->can('salaries.delete')
                        ? '<form action="' . route('salaries.destroy', $s->id) . '" method="POST" class="d-inline"
                               onsubmit="return confirm(\'Delete?\')">
                               ' . csrf_field() . method_field('DELETE') . '
                               <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                           </form>'
                        : '';
                    return $view . $del;
                })
                ->rawColumns(['staff_col', 'status_badge', 'processor_col', 'action'])
                ->make(true);
        }

        return view('admin.salaries.index');
    }

    public function create()
    {
        $this->authorize('salaries.create');
        $users  = User::where('status', 1)->get(['id','name','salary_type','salary_amount']);
        $months = range(1, 12);
        $years  = range(now()->year - 1, now()->year + 1);
        return view('admin.salaries.create', compact('users', 'months', 'years'));
    }

    public function store(Request $request)
    {
        $this->authorize('salaries.create');
        $data = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'month'        => 'required|integer|between:1,12',
            'year'         => 'required|integer|min:2020',
            'basic_salary' => 'required|numeric|min:0',
            'bonus'        => 'nullable|numeric|min:0',
            'deductions'   => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);
        $data['bonus']      = $data['bonus'] ?? 0;
        $data['deductions'] = $data['deductions'] ?? 0;
        $data['net_salary'] = $data['basic_salary'] + $data['bonus'] - $data['deductions'];

        $salary = Salary::updateOrCreate(
            ['user_id' => $data['user_id'], 'month' => $data['month'], 'year' => $data['year']],
            $data
        );

        return redirect()->route('salaries.index')->with('success', 'Salary record saved.');
    }

    public function show(Salary $salary)
    {
        $this->authorize('salaries.view');
        $salary->load('user');
        return view('admin.salaries.slip', compact('salary'));
    }

    public function edit(Salary $salary)
    {
        $this->authorize('salaries.edit');
        $users  = User::where('status', 1)->get(['id','name']);
        $months = range(1, 12);
        $years  = range(now()->year - 1, now()->year + 1);
        return view('admin.salaries.edit', compact('salary', 'users', 'months', 'years'));
    }

    public function update(Request $request, Salary $salary)
    {
        $this->authorize('salaries.edit');
        $data = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'bonus'        => 'nullable|numeric|min:0',
            'deductions'   => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);
        $data['bonus']      = $data['bonus'] ?? 0;
        $data['deductions'] = $data['deductions'] ?? 0;
        $data['net_salary'] = $data['basic_salary'] + $data['bonus'] - $data['deductions'];
        $salary->update($data);
        return redirect()->route('salaries.index')->with('success', 'Salary updated.');
    }

    public function destroy(Salary $salary)
    {
        $this->authorize('salaries.delete');
        $salary->delete();
        return redirect()->route('salaries.index')->with('success', 'Salary record deleted.');
    }

    public function markPaid(Request $request, Salary $salary)
    {
        $this->authorize('salaries.edit');
        $salary->update([
            'status'         => 'paid',
            'paid_date'      => now()->toDateString(),
            'payment_method' => $request->input('payment_method', 'cash'),
            'processed_by'   => auth()->id(),
        ]);
        return back()->with('success', 'Salary marked as paid.');
    }

    public function slip(Salary $salary)
    {
        $this->authorize('salaries.view');
        $salary->load('user');
        return view('admin.salaries.slip', compact('salary'));
    }
}
