<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentCallLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CallManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'avoid-back-history']);
    }

    /* ══════════════════════════════════════════════════════════════════════
       INDEX  —  GET /call-manager
       Shows three tabs: Today | Tomorrow | Day After Tomorrow
    ══════════════════════════════════════════════════════════════════════ */
    public function index()
    {
        abort_unless(auth()->user()->can('call-manager.view'), 403);

        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $dayAfter = Carbon::today()->addDays(2);

        $load = ['appointmentService', 'doctor:id,name', 'callLogs.calledBy'];

        $todayAppts    = Appointment::with($load)->whereDate('date', $today)->latest('date')->get();
        $tomorrowAppts = Appointment::with($load)->whereDate('date', $tomorrow)->latest('date')->get();
        $dayAfterAppts = Appointment::with($load)->whereDate('date', $dayAfter)->latest('date')->get();

        return view('admin.call-manager.index', compact(
            'todayAppts', 'tomorrowAppts', 'dayAfterAppts',
            'today', 'tomorrow', 'dayAfter'
        ));
    }

    /* ══════════════════════════════════════════════════════════════════════
       GET NOTES  —  GET /call-manager/notes/{appointment}  (AJAX)
    ══════════════════════════════════════════════════════════════════════ */
    public function getNotes(Appointment $appointment)
    {
        abort_unless(auth()->user()->can('call-manager.view'), 403);

        $notes = $appointment->callLogs()
            ->with('calledBy:id,name')
            ->latest()
            ->get()
            ->map(fn ($log) => [
                'id'          => $log->id,
                'call_type'   => $log->call_type,
                'call_status' => $log->call_status,
                'notes'       => $log->notes,
                'call_at'     => $log->call_at?->format('d M Y, H:i'),
                'called_by'   => $log->calledBy?->name ?? 'Unknown',
                'created_at'  => $log->created_at->format('d M Y, H:i'),
            ]);

        return response()->json([
            'success'     => true,
            'appointment' => [
                'id'     => $appointment->id,
                'name'   => $appointment->name,
                'phone'  => $appointment->phone,
                'date'   => Carbon::parse($appointment->date)->format('d M Y'),
                'serial' => $appointment->serial_series ?? ('#' . $appointment->id),
            ],
            'notes' => $notes,
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════════
       SAVE NOTE  —  POST /call-manager/notes  (AJAX)
    ══════════════════════════════════════════════════════════════════════ */
    public function saveNote(Request $request)
    {
        abort_unless(auth()->user()->can('call-manager.notes'), 403);

        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'call_type'      => 'required|in:reminder,follow_up,reschedule,other',
            'call_status'    => 'required|in:answered,no_answer,busy,scheduled',
            'notes'          => 'nullable|string|max:2000',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);

        $log = AppointmentCallLog::create([
            'appointment_id' => $appointment->id,
            'patient_id'     => $appointment->user_id ?? $appointment->id,
            'called_by'      => auth()->id(),
            'call_type'      => $request->call_type,
            'call_status'    => $request->call_status,
            'notes'          => $request->notes,
            'call_at'        => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note saved successfully.',
            'note'    => [
                'id'          => $log->id,
                'call_type'   => $log->call_type,
                'call_status' => $log->call_status,
                'notes'       => $log->notes,
                'call_at'     => $log->call_at->format('d M Y, H:i'),
                'called_by'   => auth()->user()->name,
                'created_at'  => $log->created_at->format('d M Y, H:i'),
            ],
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════════
       UPDATE NOTE  —  PUT /call-manager/notes/{log}  (AJAX)
    ══════════════════════════════════════════════════════════════════════ */
    public function updateNote(Request $request, AppointmentCallLog $log)
    {
        abort_unless(auth()->user()->can('call-manager.notes'), 403);

        $request->validate([
            'call_type'   => 'required|in:reminder,follow_up,reschedule,other',
            'call_status' => 'required|in:answered,no_answer,busy,scheduled',
            'notes'       => 'nullable|string|max:2000',
        ]);

        $log->update([
            'call_type'   => $request->call_type,
            'call_status' => $request->call_status,
            'notes'       => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note updated successfully.',
        ]);
    }
}
