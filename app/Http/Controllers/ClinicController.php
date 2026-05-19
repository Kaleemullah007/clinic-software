<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Http\Requests\StoreClinicRequest;
use App\Http\Requests\UpdateClinicRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClinicController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Clinic::class);
        $this->middleware(['auth', 'avoid-back-history']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Clinic::latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', function (Clinic $c) {
                    return $c->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function (Clinic $c) {
                    $edit = auth()->user()->can('update', $c)
                        ? '<a href="' . route('clinic.edit', $c->id) . '" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>'
                        : '';
                    $del = auth()->user()->can('delete', $c)
                        ? '<form action="' . route('clinic.destroy', $c->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                           </form>'
                        : '';
                    return '<div class="d-flex gap-1">' . $edit . $del . '</div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.clinic.index');
    }

    public function create()
    {
        return view('admin.clinic.create');
    }

    public function store(StoreClinicRequest $request)
    {
        Clinic::create($request->validated());
        return redirect()->route('clinic.index')->with('success', 'Clinic created successfully.');
    }

    public function show(Clinic $clinic)
    {
        return redirect()->route('clinic.index');
    }

    public function edit(Clinic $clinic)
    {
        return view('admin.clinic.edit', compact('clinic'));
    }

    public function update(UpdateClinicRequest $request, Clinic $clinic)
    {
        $clinic->update($request->validated());
        return redirect()->route('clinic.index')->with('success', 'Clinic updated successfully.');
    }

    public function destroy(Clinic $clinic)
    {
        $clinic->delete();
        return redirect()->route('clinic.index')->with('success', 'Clinic deleted.');
    }
}
