<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\DoctorAgreement;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DoctorAgreementController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('doctor-agreements.view');

        if ($request->ajax()) {
            $query = DoctorAgreement::with('doctor', 'clinic', 'service')->latest('doctor_agreements.created_at');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('doctor_name', fn(DoctorAgreement $a) =>
                    $a->doctor->name ?? '—'
                )
                ->addColumn('clinic_name', fn(DoctorAgreement $a) =>
                    $a->clinic->name ?? 'All'
                )
                ->addColumn('service_name', fn(DoctorAgreement $a) =>
                    $a->service->name ?? 'All'
                )
                ->addColumn('share_type_badge', fn(DoctorAgreement $a) =>
                    '<span class="badge bg-secondary">' . ucfirst($a->share_type) . '</span>'
                )
                ->addColumn('doctor_share_fmt', fn(DoctorAgreement $a) =>
                    $a->doctor_share . ($a->share_type === 'percentage' ? '%' : ' PKR')
                )
                ->addColumn('clinic_share_fmt', fn(DoctorAgreement $a) =>
                    $a->clinic_share . ($a->share_type === 'percentage' ? '%' : ' PKR')
                )
                ->addColumn('effective_from_fmt', fn(DoctorAgreement $a) =>
                    $a->effective_from ? \Carbon\Carbon::parse($a->effective_from)->format('d M Y') : '—'
                )
                ->addColumn('effective_to_fmt', fn(DoctorAgreement $a) =>
                    $a->effective_to ? \Carbon\Carbon::parse($a->effective_to)->format('d M Y') : 'Ongoing'
                )
                ->addColumn('status_badge', fn(DoctorAgreement $a) =>
                    '<span class="badge ' . ($a->is_active ? 'bg-success' : 'bg-secondary') . '">'
                    . ($a->is_active ? 'Active' : 'Inactive') . '</span>'
                )
                ->addColumn('action', function(DoctorAgreement $a) {
                    $edit = auth()->user()->can('doctor-agreements.edit')
                        ? '<a href="' . route('doctor-agreements.edit', $a->id) . '" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>'
                        : '';
                    $del = auth()->user()->can('doctor-agreements.delete')
                        ? '<form action="' . route('doctor-agreements.destroy', $a->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete?\')">'
                          . csrf_field() . method_field('DELETE')
                          . '<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>'
                        : '';
                    return $edit . $del;
                })
                ->rawColumns(['share_type_badge', 'status_badge', 'action'])
                ->make(true);
        }

        return view('admin.doctor-agreements.index');
    }

    public function create()
    {
        $this->authorize('doctor-agreements.create');
        $doctors  = User::role('doctor')->get(['id','name']);
        $clinics  = Clinic::all(['id','name']);
        $services = Service::where('status', 1)->get(['id','name']);
        return view('admin.doctor-agreements.create', compact('doctors', 'clinics', 'services'));
    }

    public function store(Request $request)
    {
        $this->authorize('doctor-agreements.create');
        $data = $request->validate([
            'doctor_id'      => 'required|exists:users,id',
            'clinic_id'      => 'nullable|exists:clinics,id',
            'service_id'     => 'nullable|exists:services,id',
            'share_type'     => 'required|in:percentage,fixed',
            'doctor_share'   => 'required|numeric|min:0',
            'clinic_share'   => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to'   => 'nullable|date|after_or_equal:effective_from',
            'is_active'      => 'boolean',
            'notes'          => 'nullable|string',
        ]);
        $data['is_active']  = $request->boolean('is_active', true);
        $data['created_by'] = auth()->id();

        // Deactivate existing overlapping agreements
        DoctorAgreement::where('doctor_id', $data['doctor_id'])
            ->where('is_active', true)
            ->when($data['service_id'], fn($q) => $q->where('service_id', $data['service_id']))
            ->when($data['clinic_id'], fn($q) => $q->where('clinic_id', $data['clinic_id']))
            ->update(['is_active' => false, 'effective_to' => now()]);

        DoctorAgreement::create($data);
        return redirect()->route('doctor-agreements.index')->with('success', 'Agreement created.');
    }

    public function edit(DoctorAgreement $doctorAgreement)
    {
        $this->authorize('doctor-agreements.edit');
        $doctors  = User::role('doctor')->get(['id','name']);
        $clinics  = Clinic::all(['id','name']);
        $services = Service::where('status', 1)->get(['id','name']);
        return view('admin.doctor-agreements.edit', compact('doctorAgreement', 'doctors', 'clinics', 'services'));
    }

    public function update(Request $request, DoctorAgreement $doctorAgreement)
    {
        $this->authorize('doctor-agreements.edit');
        $data = $request->validate([
            'doctor_id'      => 'required|exists:users,id',
            'clinic_id'      => 'nullable|exists:clinics,id',
            'service_id'     => 'nullable|exists:services,id',
            'share_type'     => 'required|in:percentage,fixed',
            'doctor_share'   => 'required|numeric|min:0',
            'clinic_share'   => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to'   => 'nullable|date|after_or_equal:effective_from',
            'is_active'      => 'boolean',
            'notes'          => 'nullable|string',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $doctorAgreement->update($data);
        return redirect()->route('doctor-agreements.index')->with('success', 'Agreement updated.');
    }

    public function destroy(DoctorAgreement $doctorAgreement)
    {
        $this->authorize('doctor-agreements.delete');
        $doctorAgreement->delete();
        return redirect()->route('doctor-agreements.index')->with('success', 'Agreement deleted.');
    }
}
