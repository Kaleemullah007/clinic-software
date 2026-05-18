<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Http\Requests\StoreClinicRequest;
use App\Http\Requests\UpdateClinicRequest;

class ClinicController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Clinic::class);
        $this->middleware(['auth', 'avoid-back-history']);
    }

    public function index()
    {
        $clinics = Clinic::latest()->get();
        return view('admin.clinic.index', compact('clinics'));
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
