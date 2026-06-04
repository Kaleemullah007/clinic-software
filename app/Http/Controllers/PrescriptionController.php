<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\User;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use Yajra\DataTables\Facades\DataTables;

class PrescriptionController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Prescription::class);
        $this->middleware(['auth','avoid-back-history']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(\Illuminate\Http\Request $request)
    {
        if ($request->ajax()) {
            $query = Prescription::with(['patient:id,name', 'doctor:id,name', 'appointment:id,date,name'])
                ->leftJoin('appointments', 'prescriptions.appointment_id', '=', 'appointments.id')
                ->select('prescriptions.*', 'appointments.date as appt_date')
                ->when($request->filter_patient, fn($q) => $q->where('prescriptions.user_id', $request->filter_patient))
                ->orderByDesc('appointments.date')
                ->orderByDesc('prescriptions.created_at');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('patient_col', function (Prescription $p) {
                    $name = $p->patient ? e($p->patient->name) : e($p->appointment->name ?? '—');
                    $phone = $p->patient->phone ?? '';
                    return '<div class="fw-semibold">' . $name . '</div>'
                         . ($phone ? '<small class="text-muted">' . e($phone) . '</small>' : '');
                })
                ->addColumn('doctor_col', fn (Prescription $p) =>
                    $p->doctor ? e($p->doctor->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('appt_date_col', fn (Prescription $p) =>
                    $p->appt_date
                        ? '<span class="badge bg-light text-dark border">'
                          . \Carbon\Carbon::parse($p->appt_date)->format('d M Y')
                          . '</span>'
                        : '<span class="text-muted">—</span>'
                )
                ->addColumn('action', function (Prescription $p) {
                    $del = auth()->user()->can('delete', $p)
                        ? '<button class="btn btn-sm btn-outline-danger btn-delete-prescription"
                                  data-url="' . route('prescription.destroy', $p->id) . '"
                                  data-token="' . csrf_token() . '">
                               <i class="bi bi-trash3"></i></button>'
                        : '';
                    return $del;
                })
                ->filterColumn('patient_col', fn ($q, $k) =>
                    $q->whereHas('patient', fn ($s) =>
                        $s->where('name', 'like', "%{$k}%")->orWhere('phone', 'like', "%{$k}%")
                    )
                )
                ->rawColumns(['patient_col', 'doctor_col', 'appt_date_col', 'action'])
                ->make(true);
        }

        $patients = User::withCount('prescriptions')
            ->whereNotIn('role', ['doctor', 'admin'])
            ->orderBy('name')
            ->get();

        return view('admin.prescription.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.prescription.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePrescriptionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePrescriptionRequest $request)
    {
        $prescription = Prescription::create($request->validated());

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Prescription saved successfully.',
                'id'      => $prescription->id,
            ]);
        }

        return back()->with('success', 'Prescription saved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Prescription  $prescription
     * @return \Illuminate\Http\Response
     */
    public function show(Prescription $prescription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Prescription  $prescription
     * @return \Illuminate\Http\Response
     */
    public function edit(Prescription $prescription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePrescriptionRequest  $request
     * @param  \App\Models\Prescription  $prescription
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePrescriptionRequest $request, Prescription $prescription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Prescription  $prescription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prescription $prescription)
    {
        $this->authorize('delete', $prescription);
        $prescription->delete();
        return response()->json(['success' => true]);
    }

    /**
     * AJAX: return all prescriptions AND notes for a patient (user_id).
     * Grouped by type so the modal tabs can render them separately.
     */
    public function getPatientRecords(\Illuminate\Http\Request $request, $userId)
    {
        $records = Prescription::with(['doctor:id,name', 'appointment:id,date'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id'         => $r->id,
                    'type'       => $r->type,
                    'medicine'   => $r->medicine,
                    'dosage'     => $r->dosage,
                    'remarks'    => $r->remarks,
                    'doctor'     => $r->doctor?->name ?? '—',
                    'appt_date'  => $r->appointment?->date
                                    ? \Carbon\Carbon::parse($r->appointment->date)->format('d M Y')
                                    : '—',
                    'created_at' => $r->created_at->format('d M Y, H:i'),
                    'can_delete' => auth()->user()->can('delete', $r),
                ];
            });

        return response()->json([
            'prescriptions' => $records->where('type', 'prescription')->values(),
            'notes'         => $records->where('type', 'note')->values(),
        ]);
    }


    public function recordsQuery($request)
    {
        // withoutGlobalScopes()->
        $expenses = Prescription::query();
        $search = $request->search;
        $dates = $request->daterange;

        if($dates != null){
            list($start_date,$end_date) = explode('-',$dates);
           $start_date = changeDateFormat($start_date,'Y-m-d');
           $end_date = changeDateFormat($end_date,'Y-m-d');
            $expenses =$expenses->whereDate('date','>=',$start_date)
            ->whereDate('date','<=',$end_date);
        }
        if($search != null)
            $expenses = $expenses->where('name','like',"%".$search."%");
        return $expenses ;
    }

    public function getExpenses(Request $request)
    {
        $expenses = $this->recordsQuery($request)->get();
        $expenses_html = view('admin.prescription.ajax-prescription',compact('expenses'))->render();
        $pagination_html = view('pages.pagination',compact('expenses'))->render();
        return response()->json(['html'=>$expenses_html,'phtml'=>$pagination_html]);
    }


}
