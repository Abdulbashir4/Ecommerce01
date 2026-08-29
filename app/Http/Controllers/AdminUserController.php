<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with('roles')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim($request->string('q'));
                $q->where(fn ($x) => $x->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%"));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('role'), fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('roles.slug', $request->string('role'))))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => null,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateUser($request);
        $this->authorizeRoleAssignment($data['roles'] ?? []);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'is_admin' => false,
                'status' => $data['status'],
                'force_password_change' => (bool) ($data['force_password_change'] ?? false),
            ]);
            $user->roles()->sync($data['roles'] ?? []);
            return $user;
        });

        $this->audit('user.created', $user, ['roles' => $data['roles'] ?? []]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $user->load('roles');
        return view('admin.users.form', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateUser($request, $user);
        $this->authorizeRoleAssignment($data['roles'] ?? []);

        if ($user->id === $request->user()->id && $data['status'] !== 'active') {
            return back()->withErrors(['status' => 'You cannot deactivate your own account.'])->withInput();
        }

        DB::transaction(function () use ($data, $user) {
            $user->update([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'status' => $data['status'],
                'force_password_change' => (bool) ($data['force_password_change'] ?? false),
            ]);

            if (!empty($data['password'])) {
                $user->update(['password' => Hash::make($data['password'])]);
            }

            $user->roles()->sync($data['roles'] ?? []);
        });

        $this->audit('user.updated', $user, ['roles' => $data['roles'] ?? [], 'password_changed' => !empty($data['password'])]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($user->isSuperAdmin()) {
            return back()->withErrors(['user' => 'A Super Admin cannot be deleted from this screen.']);
        }

        $id = $user->id;
        $name = $user->name;
        $user->delete();

        $this->audit('user.deleted', null, ['deleted_user_id' => $id, 'deleted_user_name' => $name]);

        return back()->with('success', 'User deleted successfully.');
    }

    public function status(Request $request, User $user)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['active', 'inactive', 'blocked'])]]);

        if ($user->id === $request->user()->id && $data['status'] !== 'active') {
            return back()->withErrors(['status' => 'You cannot disable your own account.']);
        }

        $user->update(['status' => $data['status']]);
        $this->audit('user.status_changed', $user, ['status' => $data['status']]);

        return back()->with('success', 'User status updated.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'force_password_change' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
            'force_password_change' => $request->boolean('force_password_change'),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        $this->audit('user.password_reset', $user, ['force_password_change' => $request->boolean('force_password_change')]);

        return back()->with('success', 'Password reset successfully. The password itself is never stored or displayed in plain text.');
    }

    private function authorizeRoleAssignment(array $roleIds): void
    {
        if ($roleIds && !auth()->user()->hasPermission('users.roles.assign')) {
            abort(403, 'You do not have permission to assign roles.');
        }
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50', Rule::unique('users', 'phone')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['active', 'inactive', 'blocked'])],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'force_password_change' => ['nullable', 'boolean'],
        ]);
    }

    private function audit(string $action, ?User $target, array $metadata = []): void
    {
        AdminAuditLog::create([
            'actor_user_id' => auth()->id(),
            'target_user_id' => $target?->id,
            'action' => $action,
            'subject_type' => $target ? User::class : null,
            'subject_id' => $target?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }
}
