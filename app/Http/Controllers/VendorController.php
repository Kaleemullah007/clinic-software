<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('vendors.view');

        if ($request->ajax()) {
            $query = Vendor::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_badge', fn (Vendor $v) =>
                    $v->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>'
                )
                ->addColumn('action', function (Vendor $v) {
                    $edit = $del = '';
                    if (auth()->user()->can('vendors.edit')) {
                        $edit = '<a href="' . route('vendor.edit', $v) . '" class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="bi bi-pencil"></i>
                                 </a>';
                    }
                    if (auth()->user()->can('vendors.delete')) {
                        $del = '<form action="' . route('vendor.destroy', $v) . '" method="POST" class="d-inline"
                                    onsubmit="return confirm(\'Delete this vendor?\')">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                               </form>';
                    }
                    return $edit . $del;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('admin.vendors.index');
    }

    public function create()
    {
        $this->authorize('vendors.create');
        return view('admin.vendors.create');
    }

    public function store(Request $request)
    {
        $this->authorize('vendors.create');
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone'   => 'required|string|max:30',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string',
            'status'  => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        Vendor::create($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Vendor added successfully.']);
        }
        return redirect()->route('vendor.index')->with('success', 'Vendor added successfully.');
    }

    public function edit(Vendor $vendor)
    {
        $this->authorize('vendors.edit');
        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $this->authorize('vendors.edit');
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone'   => 'required|string|max:30',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string',
            'status'  => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        $vendor->update($data);
        return redirect()->route('vendor.index')->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $this->authorize('vendors.delete');
        $vendor->delete();
        return redirect()->route('vendor.index')->with('success', 'Vendor deleted.');
    }

    /** AJAX: return all active vendors as JSON (for inline dropdowns) */
    public function list()
    {
        $vendors = Vendor::where('status', true)->select('id', 'name', 'company', 'phone')->get();
        return response()->json($vendors);
    }
}
