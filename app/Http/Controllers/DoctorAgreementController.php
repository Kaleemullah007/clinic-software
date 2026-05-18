<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\DoctorAgreement;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorAgreementController extends Controller
{
    public function index()
    {
        $this->authorize('doctor-agreements.view');
        $agreements = DoctorAgreement::with('doctor', 'clinic', 'service')->latest()->get();
        return view('admin.doctor-agreements.index', compact('agreements'));
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
