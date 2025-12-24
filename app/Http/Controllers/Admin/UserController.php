<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    // ===============================
    // STEP 2: User List
    // ===============================
    public function index()
    {
        $users = User::query()
            ->select('id', 'name', 'email', 'role', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    // ===============================
    // STEP 2.5: Enable / Disable User
    // ===============================
    public function toggleStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'User status updated.');
    }

    // ===============================
    // STEP 3: Create User (Form)
    // ===============================
    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => [
                'admin',
                'user',
                'clerk',
                'director',
            ],
        ]);
    }

    // ===============================
    // STEP 3: Store User
    // ===============================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'role' => ['required', 'in:admin,user,clerk,director'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'status' => 'active',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    // ===============================
    // STEP 3: Edit User (Form)
    // ===============================
    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->only([
                'id',
                'name',
                'email',
                'role',
                'status',
            ]),
            'roles' => [
                'admin',
                'user',
                'clerk',
                'director',
            ],
        ]);
    }

    // ===============================
    // STEP 3: Update User
    // ===============================
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => ['required', 'in:admin,user,clerk,director'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
}
