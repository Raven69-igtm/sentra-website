<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SuperadminRoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        $permissions = Permission::all()->groupBy('group');

        return view('backend.superadmin.role.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('backend.superadmin.role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|alpha_dash|unique:roles,name',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => Str::slug($validated['name'], '_'),
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        ActivityLog::record('data', "Membuat role '{$role->display_name}' ({$role->name}).", 'role', "role-{$role->id}");

        return redirect()->route('superadmin.roles.index')
            ->with('success', "Role '{$role->display_name}' berhasil dibuat.");
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all()->groupBy('group');
        $assignedPermissionIds = $role->permissions->pluck('id')->toArray();

        return view('backend.superadmin.role.edit', compact('role', 'permissions', 'assignedPermissionIds'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->display_name = $validated['display_name'];
        $role->description = $validated['description'] ?? null;
        $role->save();

        $role->permissions()->sync($validated['permissions'] ?? []);

        ActivityLog::record('data', "Memperbarui role '{$role->display_name}' ({$role->name}) beserta hak aksesnya.", 'role', "role-{$role->id}");

        return redirect()->route('superadmin.roles.index')
            ->with('success', "Role '{$role->display_name}' dan hak akses berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->is_system) {
            return back()->with('error', 'Role sistem bawaan tidak dapat dihapus.');
        }

        if ($role->users_count > 0) {
            return back()->with('error', "Role tidak dapat dihapus karena masih digunakan oleh {$role->users_count} pengguna.");
        }

        $roleName = $role->display_name;
        $role->permissions()->detach();
        $role->delete();

        ActivityLog::record('data', "Menghapus role '{$roleName}'.", 'role', "role-{$id}");

        return redirect()->route('superadmin.roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}
