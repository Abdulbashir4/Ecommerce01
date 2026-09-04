<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminRoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount('users')->with('permissions')->orderBy('is_system', 'desc')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.roles.form', [
            'role' => null,
            'permissions' => Permission::orderBy('group')->orderBy('name')->get()->groupBy('group'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRole($request);
        $role = DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'is_system' => false,
            ]);
            $role->permissions()->sync($data['permissions'] ?? []);
            return $role;
        });

        $this->audit('role.created', $role, ['permissions' => $data['permissions'] ?? []]);
        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.form', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::orderBy('group')->orderBy('name')->get()->groupBy('group'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->slug === 'super-admin') {
            return back()->withErrors(['role' => 'Super Admin permissions are always full access.']);
        }

        $data = $this->validateRole($request, $role);
        DB::transaction(function () use ($data, $role) {
            $role->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
            ]);
            $role->permissions()->sync($data['permissions'] ?? []);
        });

        $this->audit('role.updated', null, ['role_id' => $role->id, 'slug' => $role->slug, 'permissions' => $data['permissions'] ?? []]);
        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system || in_array($role->slug, ['super-admin', 'admin', 'customer'], true)) {
            return back()->withErrors(['role' => 'System roles cannot be deleted.']);
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'This role is assigned to users. Reassign those users first.']);
        }

        $role->delete();
        $this->audit('role.deleted', null, ['role_id' => $role->id, 'slug' => $role->slug]);
        return back()->with('success', 'Role deleted successfully.');
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('roles', 'slug')->ignore($role?->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);
    }

    private function audit(string $action, ?Role $target, array $metadata = []): void
    {
        AdminAuditLog::create([
            'actor_user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => Role::class,
            'subject_id' => $target?->id ?? ($metadata['role_id'] ?? null),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }
}
