<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Expense;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('expenses.view');

        if ($request->ajax()) {
            $query = Expense::with(['clinic:id,name', 'creator:id,name'])->latest('expense_date');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('amount_col', fn (Expense $e) => 'PKR ' . number_format($e->amount, 2))
                ->addColumn('clinic_col', fn (Expense $e) =>
                    $e->clinic ? e($e->clinic->name) : '<span class="text-muted">General</span>'
                )
                ->addColumn('creator_col', fn (Expense $e) =>
                    $e->creator ? e($e->creator->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('action', function (Expense $e) {
                    $edit = auth()->user()->can('expenses.edit')
                        ? '<a href="' . route('expenses.edit', $e->id) . '" class="btn btn-sm btn-outline-theme me-1"><i class="bi bi-pencil"></i></a>'
                        : '';
                    $del = auth()->user()->can('expenses.delete')
                        ? '<form action="' . route('expenses.destroy', $e->id) . '" method="POST" class="d-inline"
                               onsubmit="return confirm(\'Delete?\')">
                               ' . csrf_field() . method_field('DELETE') . '
                               <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                           </form>'
                        : '';
                    return $edit . $del;
                })
                ->rawColumns(['clinic_col', 'creator_col', 'action'])
                ->make(true);
        }

        $totalMonth = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)->sum('amount');

        return view('admin.expenses.index', compact('totalMonth'));
    }

    public function create()
    {
        $this->authorize('expenses.create');
        $clinics = Clinic::all(['id','name']);
        return view('admin.expenses.create', compact('clinics'));
    }

    public function store(Request $request)
    {
        $this->authorize('expenses.create');
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'nullable|string|max:100',
            'clinic_id'        => 'nullable|exists:clinics,id',
            'amount'           => 'required|numeric|min:0.01',
            'expense_date'     => 'required|date',
            'payment_method'   => 'nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        Expense::create($data);
        return redirect()->route('expenses.index')->with('success', 'Expense recorded.');
    }

    public function edit(Expense $expense)
    {
        $this->authorize('expenses.edit');
        $clinics = Clinic::all(['id','name']);
        return view('admin.expenses.edit', compact('expense', 'clinics'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('expenses.edit');
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'nullable|string|max:100',
            'clinic_id'        => 'nullable|exists:clinics,id',
            'amount'           => 'required|numeric|min:0.01',
            'expense_date'     => 'required|date',
            'payment_method'   => 'nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ]);
        $expense->update($data);
        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('expenses.delete');
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }
}
