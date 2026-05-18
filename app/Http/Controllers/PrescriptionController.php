<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
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
            $query = Prescription::with(['patient:id,name', 'doctor:id,name'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('patient_col', fn (Prescription $p) =>
                    $p->patient ? e($p->patient->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('doctor_col', fn (Prescription $p) =>
                    $p->doctor ? e($p->doctor->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('date_col', fn (Prescription $p) =>
                    $p->created_at?->format('d M Y') ?? '—'
                )
                ->addColumn('action', function (Prescription $p) {
                    $view = route('prescription.show', $p->id)
                        ? '<a href="' . route('prescription.show', $p->id) . '" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-eye"></i></a>'
                        : '';
                    $del = auth()->user()->can('delete', $p)
                        ? '<form action="' . route('prescription.destroy', $p->id) . '" method="POST" class="d-inline"
                               onsubmit="return confirm(\'Delete?\')">
                               ' . csrf_field() . method_field('DELETE') . '
                               <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button>
                           </form>'
                        : '';
                    return $view . $del;
                })
                ->rawColumns(['patient_col', 'doctor_col', 'action'])
                ->make(true);
        }

        return view('admin.prescription.index');
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
        //
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
