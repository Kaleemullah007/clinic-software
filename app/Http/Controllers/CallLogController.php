<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentCallLog;
use App\Models\User;
use Illuminate\Http\Request;

class CallLogController extends Controller
{
    public function index()
    {
        $this->authorize('call-logs.view');
        $logs = AppointmentCallLog::with('appointment', 'patient', 'calledBy')->latest()->paginate(50);
        return view('admin.call-logs.index', compact('logs'));
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
