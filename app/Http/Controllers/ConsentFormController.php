<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ConsentForm;
use Illuminate\Http\Request;

class ConsentFormController extends Controller
{
    public function index()
    {
        $this->authorize('consent-forms.view');
        $forms = ConsentForm::with('appointment', 'patient', 'creator')->latest()->get();
        return view('admin.consent-forms.index', compact('forms'));
    }

    public function create()
    {
        $this->authorize('consent-forms.create');
        $appointments = Appointment::with('patient')->latest()->limit(100)->get();
        return view('admin.consent-forms.create', compact('appointments'));
    }

    public function store(Request $request)
    {
        $this->authorize('consent-forms.create');
        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'patient_id'     => 'required|exists:users,id',
            'form_title'     => 'required|string|max:255',
            'form_content'   => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        ConsentForm::create($data);
        return redirect()->route('consent-forms.index')->with('success', 'Consent form created.');
    }

    public function edit(ConsentForm $consentForm)
    {
        $this->authorize('consent-forms.edit');
        $appointments = Appointment::with('patient')->latest()->limit(100)->get();
        return view('admin.consent-forms.edit', compact('consentForm', 'appointments'));
    }

    public function update(Request $request, ConsentForm $consentForm)
    {
        $this->authorize('consent-forms.edit');
        $data = $request->validate([
            'form_title'   => 'required|string|max:255',
            'form_content' => 'nullable|string',
        ]);
        $consentForm->update($data);
        return redirect()->route('consent-forms.index')->with('success', 'Form updated.');
    }

    public function destroy(ConsentForm $consentForm)
    {
        $this->authorize('consent-forms.delete');
        $consentForm->delete();
        return redirect()->route('consent-forms.index')->with('success', 'Form deleted.');
    }

    public function signView(ConsentForm $consentForm)
    {
        return view('admin.consent-forms.sign', compact('consentForm'));
    }

    public function saveSignature(Request $request, ConsentForm $consentForm)
    {
        $data = $request->validate(['signature_image' => 'required|string']);
        $consentForm->update([
            'signature_image' => $data['signature_image'],
            'signed'          => true,
            'signed_at'       => now(),
        ]);
        return redirect()->route('consent-forms.index')->with('success', 'Consent form signed.');
    }
}
