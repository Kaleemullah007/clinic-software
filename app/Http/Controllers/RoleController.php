<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Database\Seeders\PermissionSeeder;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'avoid-back-history']);
        $this->middleware('permission:roles.view')->only(['index']);
        $this->middleware('permission:roles.create')->only(['create', 'store']);
        $this->middleware('permission:roles.edit')->only(['edit', 'update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::withCount('permissions', 'users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $modules     = PermissionSeeder::$modules;
        $permissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);
        return view('admin.roles.create', compact('modules', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('role.index')
            ->with('message', "Role '{$role->name}' created successfully.");
    }

    public function edit(Role $role)
    {
        $modules            = PermissionSeeder::$modules;
        $permissions        = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);
        $rolePermissions    = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role', 'modules', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
        ]);

        if ($role->name !== 'super-admin') {
            $role->update(['name' => $request->name]);
        }

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('role.index')
            ->with('message', "Role '{$role->name}' updated successfully.");
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super-admin') {
            return redirect()->route('role.index')
                ->with('message', 'Super Admin role cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('role.index')
            ->with('message', 'Role deleted successfully.');
    }
}
