<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'avoid-back-history']);
        $this->middleware('permission:users.view')->only(['index']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.edit')->only(['edit', 'update', 'showProfile', 'updateProfile']);
        $this->middleware('permission:users.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with('roles')
                ->whereNotIn('id', [1, 13, 17, 64]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('roles_html', function (User $user) {
                    if ($user->roles->isEmpty()) {
                        return '<span class="text-muted small">No role</span>';
                    }
                    return $user->roles->map(fn ($r) =>
                        '<span class="badge" style="background:#fce4ec;color:#B1083C;font-size:12px;border:1px solid #B1083C;">'
                        . ucfirst(str_replace('-', ' ', $r->name)) . '</span>'
                    )->implode(' ');
                })
                ->addColumn('status_badge', function (User $u) {
                    $label  = $u->status ? 'Active' : 'Inactive';
                    $class  = $u->status ? 'bg-success' : 'bg-danger';
                    $url    = route('users.toggle-status', $u->id);
                    return '<button type="button"
                                class="badge border-0 btn-toggle-status"
                                style="cursor:pointer;font-size:.8rem;padding:5px 10px"
                                data-id="' . $u->id . '"
                                data-name="' . e($u->name) . '"
                                data-status="' . $u->status . '"
                                data-url="' . $url . '">'
                        . '<span class="badge ' . $class . ' w-100">' . $label . '</span>'
                        . '</button>';
                })
                ->addColumn('action', function (User $user) {
                    $edit = $del = $actAs = '';
                    if (auth()->user()->can('users.edit')) {
                        $edit = '<a href="' . route('users.edit', $user->id) . '" class="btn btn-sm btn-outline-theme me-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                 </a>';
                    }
                    if (auth()->user()->can('users.delete') && !$user->hasRole('super-admin')) {
                        $del = '<form action="' . route('users.destroy', $user->id) . '" method="POST" class="d-inline"
                                    onsubmit="return confirm(\'Delete ' . addslashes($user->name) . '?\')">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                               </form>';
                    }
                    // Act As — superadmin only, not on other superadmin rows
                    if (auth()->user()->isSuperAdmin() && !$user->hasRole('super-admin')) {
                        $actAs = '<a href="' . route('users.act-as', $user->id) . '"
                                     class="btn btn-sm ms-1 text-white"
                                     style="background:#B1083C;border:none"
                                     title="Act as ' . e($user->name) . '">
                                     <i class="bi bi-person-fill-gear me-1"></i>Act As
                                  </a>';
                    }
                    return $edit . $del . $actAs;
                })
                ->rawColumns(['roles_html', 'status_badge', 'action'])
                ->make(true);
        }

        return view('admin.users.index');
    }

    public function create()
    {
        $clinics     = Clinic::all();
        $roles       = Role::all();
        $modules     = PermissionSeeder::$modules;
        $permissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);
        $countries   = Country::active()->orderBy('name')->get(['id', 'name']);
        return view('admin.users.create', compact('clinics', 'roles', 'modules', 'permissions', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'role'       => $request->spatie_role ?? '',
            'status'     => $request->has('status') ? 1 : 0,
            'phone'      => $request->phone,
            'clinic_id'  => $request->clinic_id   ?: null,
            'country_id' => $request->country_id ?: null,
            'state_id'   => $request->state_id   ?: null,
            'city_id'    => $request->city_id     ?: null,
        ]);

        if ($request->spatie_role) {
            $user->syncRoles([$request->spatie_role]);
        }

        if ($request->has('direct_permissions')) {
            $user->syncPermissions($request->direct_permissions);
        }

        return redirect()->route('users.index')->with('message', 'User created successfully.');
    }

    public function show(User $User) {}

    public function edit(User $User)
    {
        $clinics           = Clinic::all();
        $roles             = Role::all();
        $modules           = PermissionSeeder::$modules;
        $permissions       = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);
        $userRoles         = $User->roles->pluck('name')->toArray();
        $directPermissions = $User->getDirectPermissions()->pluck('name')->toArray();
        $countries         = Country::active()->orderBy('name')->get(['id', 'name']);
        return view('admin.users.edit', compact(
            'User', 'clinics', 'roles', 'modules', 'permissions', 'userRoles', 'directPermissions', 'countries'
        ));
    }

    public function update(Request $request, User $User)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $User->id,
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'status'     => $request->has('status') ? 1 : 0,
            'phone'      => $request->phone,
            'clinic_id'  => $request->clinic_id   ?: null,
            'country_id' => $request->country_id ?: null,
            'state_id'   => $request->state_id   ?: null,
            'city_id'    => $request->city_id     ?: null,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $User->update($data);

        if ($request->spatie_role) {
            $User->syncRoles([$request->spatie_role]);
        } else {
            $User->syncRoles([]);
        }

        $User->syncPermissions($request->direct_permissions ?? []);

        return redirect()->route('users.index')->with('message', 'User updated successfully.');
    }

    public function destroy(User $User)
    {
        if ($User->hasRole('super-admin')) {
            return redirect()->back()->with('message', 'Super Admin cannot be deleted.');
        }
        $User->delete();
        return redirect()->route('users.index')->with('message', 'User deleted successfully.');
    }

    public function showProfile(User $User)
    {
        return view('admin.users.update-profile', compact('User'));
    }

    public function updateProfile(Request $request)
    {
        $id   = Auth::user()->id;
        $user = User::findOrFail($id);
        $pass = $user->password;

        if ($request->filled('password_confirmation')) {
            $pass = Hash::make($request->password_confirmation);
        }

        $filename = $user->avatar;

        if ($request->hasFile('avatar')) {
            $image    = $request->file('avatar');
            $filename = rand(11111111, 99999999) . $image->getClientOriginalName();
            Image::make($image->getRealPath())->resize(128, 128)
                ->save(public_path('/images/avatar/' . $filename));
            $old = public_path('/images/avatar/' . $user->avatar);
            if (file_exists($old) && $user->avatar !== 'no_avatar.png') {
                @unlink($old);
            }
        }

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $pass,
            'avatar'   => $filename,
        ]);

        return redirect()->back()->with('message', 'Profile updated successfully.');
    }

    /* ══════════════════════════════════════════════════════════════════
       TOGGLE STATUS — activate / deactivate a user
    ══════════════════════════════════════════════════════════════════ */
    public function toggleStatus(User $user)
    {
        $this->authorize('users.edit');
        $user->update(['status' => $user->status ? 0 : 1]);
        return response()->json([
            'status'  => 'success',
            'active'  => (bool) $user->status,
            'message' => $user->name . ' has been ' . ($user->status ? 'activated' : 'deactivated') . '.',
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════
       ACT AS — superadmin impersonates another user
    ══════════════════════════════════════════════════════════════════ */
    public function actAs(User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        abort_if($user->hasRole('super-admin'), 403, 'Cannot impersonate another superadmin.');

        // Store the original superadmin's ID in session
        session(['impersonating_id' => auth()->id()]);

        // Switch to the target user (bypasses all normal auth)
        Auth::loginUsingId($user->id);

        return redirect()->route('dashboard')
            ->with('success', 'You are now acting as ' . $user->name);
    }

    /* ── Stop acting — return to original superadmin account ─────── */
    public function stopActing()
    {
        $originalId = session('impersonating_id');
        abort_unless($originalId, 403, 'Not currently impersonating.');

        // Restore the original superadmin
        Auth::loginUsingId($originalId);
        session()->forget('impersonating_id');

        return redirect()->route('users.index')
            ->with('success', 'You have returned to your account.');
    }
}
