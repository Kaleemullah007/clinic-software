<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Database\Seeders\PermissionSeeder;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'avoid-back-history']);
        $this->middleware('permission:permissions.view')->only(['index']);
        $this->middleware('permission:permissions.create')->only(['create', 'store']);
        $this->middleware('permission:permissions.edit')->only(['edit', 'update']);
        $this->middleware('permission:permissions.delete')->only(['destroy']);
    }

    public function index()
    {
        $permissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $modules = array_keys(PermissionSeeder::$modules);
        return view('admin.permissions.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'module' => 'required|string|max:50',
            'action' => 'required|string|max:50',
        ]);

        $name = strtolower(trim($request->module)) . '.' . strtolower(trim($request->action));

        if (Permission::where('name', $name)->exists()) {
            return back()->withErrors(['action' => "Permission '{$name}' already exists."])->withInput();
        }

        Permission::create(['name' => $name, 'guard_name' => 'web']);

        return redirect()->route('permission.index')
            ->with('message', "Permission '{$name}' created successfully.");
    }

    public function edit(Permission $permission)
    {
        $modules = array_keys(PermissionSeeder::$modules);
        [$module, $action] = array_pad(explode('.', $permission->name, 2), 2, '');
        return view('admin.permissions.edit', compact('permission', 'modules', 'module', 'action'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'module' => 'required|string|max:50',
            'action' => 'required|string|max:50',
        ]);

        $name = strtolower(trim($request->module)) . '.' . strtolower(trim($request->action));

        if (Permission::where('name', $name)->where('id', '!=', $permission->id)->exists()) {
            return back()->withErrors(['action' => "Permission '{$name}' already exists."])->withInput();
        }

        $permission->update(['name' => $name]);

        return redirect()->route('permission.index')
            ->with('message', "Permission updated to '{$name}'.");
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permission.index')
            ->with('message', 'Permission deleted successfully.');
    }
}
