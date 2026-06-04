<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;

class StaffIdCardController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('staff-id-cards.view'), 403);

        $clinics = auth()->user()->isSuperAdmin()
            ? Clinic::orderBy('name')->get()
            : Clinic::where('id', auth()->user()->clinic_id)->get();

        // Distinct roles from users table
        $roles = User::whereNotNull('role')
            ->where('role', '!=', '')
            ->distinct()
            ->orderBy('role')
            ->pluck('role');

        return view('admin.staff-id-cards.index', compact('clinics', 'roles'));
    }

    /**
     * AJAX: fetch users by role + optional clinic.
     */
    public function getUsers(Request $request)
    {
        abort_unless(auth()->user()->can('staff-id-cards.view'), 403);

        $query = User::where('status', true)
            ->where('role', $request->role)
            ->with('clinic:id,name');

        // Super admin can filter by clinic; non-super-admin is locked to their clinic
        if (auth()->user()->isSuperAdmin()) {
            if ($request->filled('clinic_id')) {
                $query->where('clinic_id', $request->clinic_id);
            }
        } else {
            $query->where('clinic_id', auth()->user()->clinic_id);
        }

        $users = $query->orderBy('name')
            ->get(['id', 'name', 'role', 'avatar', 'phone', 'clinic_id'])
            ->map(function ($user) {
                return [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'role'     => ucfirst($user->role),
                    'phone'    => $user->phone,
                    'avatar'   => $user->avatar && filter_var($user->avatar, FILTER_VALIDATE_URL)
                                    ? $user->avatar
                                    : ($user->avatar ? asset('storage/' . $user->avatar) : null),
                    'emp_id'   => 'DMD-' . date('Y') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'clinic'   => $user->clinic?->name ?? '',
                ];
            });

        return response()->json($users);
    }
}
