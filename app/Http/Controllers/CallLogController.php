<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentCallLog;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CallLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('call-logs.view');

        if ($request->ajax()) {
            $query = AppointmentCallLog::with('appointment', 'patient', 'calledBy')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('appointment_no', fn(AppointmentCallLog $log) =>
                    $log->appointment->appointment_id ?? '—'
                )
                ->addColumn('patient_name', fn(AppointmentCallLog $log) =>
                    $log->patient->name ?? '—'
                )
                ->addColumn('call_type_badge', fn(AppointmentCallLog $log) =>
                    '<span class="badge bg-info text-dark">' . ucfirst(str_replace('_', ' ', $log->call_type)) . '</span>'
                )
                ->addColumn('call_status_badge', function(AppointmentCallLog $log) {
                    $colors = ['answered' => 'success', 'no_answer' => 'secondary', 'busy' => 'warning', 'scheduled' => 'primary'];
                    $color  = $colors[$log->call_status] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst(str_replace('_', ' ', $log->call_status)) . '</span>';
                })
                ->addColumn('called_by_name', fn(AppointmentCallLog $log) =>
                    $log->calledBy->name ?? '—'
                )
                ->addColumn('call_at_fmt', fn(AppointmentCallLog $log) =>
                    $log->call_at
                        ? \Carbon\Carbon::parse($log->call_at)->format('d M Y H:i')
                        : $log->created_at->format('d M Y H:i')
                )
                ->addColumn('notes_short', fn(AppointmentCallLog $log) =>
                    $log->notes ? \Str::limit($log->notes, 40) : '—'
                )
                ->addColumn('action', function(AppointmentCallLog $log) {
                    $edit = auth()->user()->can('call-logs.edit')
                        ? '<a href="' . route('call-logs.edit', $log->id) . '" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>'
                        : '';
                    $del = auth()->user()->can('call-logs.delete')
                        ? '<form action="' . route('call-logs.destroy', $log->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete?\')">'
                          . csrf_field() . method_field('DELETE')
                          . '<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>'
                        : '';
                    return $edit . $del;
                })
                ->rawColumns(['call_type_badge', 'call_status_badge', 'action'])
                ->make(true);
        }

        return view('admin.call-logs.index');
    }

    public function create()
    {
        $this->authorize('call-logs.create');
        $appointments = Appointment::with('patient')->latest()->limit(100)->get();
        return view('admin.call-logs.create', compact('appointments'));
    }

    public function store(Request $request)
    {
        $this->authorize('call-logs.create');
        $data = $request->validate([
            'appointment_id' => 'nullable|exists:appointments,id',
            'patient_id'     => 'required|exists:users,id',
            'call_type'      => 'required|in:reminder,follow_up,reschedule,other',
            'call_status'    => 'required|in:answered,no_answer,busy,scheduled',
            'notes'          => 'nullable|string',
            'call_at'        => 'nullable|date',
        ]);
        $data['called_by'] = auth()->id();
        AppointmentCallLog::create($data);
        return redirect()->route('call-logs.index')->with('success', 'Call log recorded.');
    }

    public function edit(AppointmentCallLog $callLog)
    {
        $this->authorize('call-logs.edit');
        $appointments = Appointment::with('patient')->latest()->limit(100)->get();
        return view('admin.call-logs.edit', compact('callLog', 'appointments'));
    }

    public function update(Request $request, AppointmentCallLog $callLog)
    {
        $this->authorize('call-logs.edit');
        $data = $request->validate([
            'call_type'   => 'required|in:reminder,follow_up,reschedule,other',
            'call_status' => 'required|in:answered,no_answer,busy,scheduled',
            'notes'       => 'nullable|string',
            'call_at'     => 'nullable|date',
        ]);
        $callLog->update($data);
        return redirect()->route('call-logs.index')->with('success', 'Call log updated.');
    }

    public function destroy(AppointmentCallLog $callLog)
    {
        $this->authorize('call-logs.delete');
        $callLog->delete();
        return redirect()->route('call-logs.index')->with('success', 'Log deleted.');
    }
}
