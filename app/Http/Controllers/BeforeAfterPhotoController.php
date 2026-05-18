<?php

namespace App\Http\Controllers;

use App\Models\BeforeAfterPhoto;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeforeAfterPhotoController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('before-after-photos.view');

        $photos = BeforeAfterPhoto::with('appointment.patient')
            ->when($request->type, fn($q) => $q->where('photo_type', $request->type))
            ->when($request->appointment_id, fn($q) => $q->where('appointment_id', $request->appointment_id))
            ->latest()
            ->paginate(24);

        $appointments = Appointment::with('patient')->latest()->limit(200)->get();

        return view('admin.before-after-photos.index', compact('photos', 'appointments'));
    }

    public function store(Request $request)
    {
        $this->authorize('before-after-photos.create');

        $data = $request->validate([
            'appointment_id'  => 'required|exists:appointments,id',
            'patient_id'      => 'required|exists:users,id',
            'photo_type'      => 'required|in:before,after',
            'photo'           => 'required|image|max:5120',
            'caption'         => 'nullable|string|max:255',
            'patient_consent' => 'nullable|boolean',
        ]);

        $path = $request->file('photo')->store('before-after', 'public');

        BeforeAfterPhoto::create([
            'appointment_id'  => $data['appointment_id'],
            'patient_id'      => $data['patient_id'],
            'photo_type'      => $data['photo_type'],
            'file_path'       => $path,
            'caption'         => $data['caption'] ?? null,
            'patient_consent' => $request->boolean('patient_consent'),
            'uploaded_by'     => auth()->id(),
        ]);

        return back()->with('success', 'Photo uploaded successfully.');
    }

    public function destroy(BeforeAfterPhoto $beforeAfterPhoto)
    {
        $this->authorize('before-after-photos.delete');
        Storage::disk('public')->delete($beforeAfterPhoto->file_path);
        $beforeAfterPhoto->delete();
        return back()->with('success', 'Photo deleted.');
    }
}
