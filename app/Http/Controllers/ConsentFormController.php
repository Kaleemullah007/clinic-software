<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ConsentForm;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ConsentFormController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('consent-forms.view');

        if ($request->ajax()) {
            $query = ConsentForm::with('appointment', 'patient', 'creator')->latest('consent_forms.created_at');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('patient_name', function (ConsentForm $f) {
                    return e($f->patient->name ?? '—');
                })
                ->addColumn('signed_badge', function (ConsentForm $f) {
                    return $f->signed
                        ? '<span class="badge bg-success">Signed</span>'
                        : '<span class="badge bg-warning text-dark">Pending</span>';
                })
                ->addColumn('signed_at_fmt', function (ConsentForm $f) {
                    return optional($f->signed_at)?->format('d M Y') ?? '—';
                })
                ->addColumn('action', function (ConsentForm $f) {
                    $html = '';

                    if (!$f->signed) {
                        $html .= '<a href="' . route('consent-form.sign', $f->id) . '" class="btn btn-sm btn-success me-1" title="Get Signature"><i class="bi bi-pen"></i></a>';
                    }

                    if (auth()->user()->can('consent-forms.edit')) {
                        $html .= '<a href="' . route('consent-forms.edit', $f->id) . '" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>';
                    }

                    if (auth()->user()->can('consent-forms.delete')) {
                        $html .= '<form action="' . route('consent-forms.destroy', $f->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete?\')">'
                               . csrf_field() . method_field('DELETE')
                               . '<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>';
                    }

                    return $html;
                })
                ->rawColumns(['signed_badge', 'action'])
                ->make(true);
        }

        return view('admin.consent-forms.index');
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
