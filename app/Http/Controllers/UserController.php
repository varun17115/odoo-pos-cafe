<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:cashier,chef',
            'password' => ['required', Password::min(8)],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        // Prevent editing other admins
        if ($user->isAdmin() && $user->id !== auth()->id()) {
            return back()->with('error', 'Cannot edit another admin.');
        }

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,'.$user->id,
            'role'     => 'required|in:admin,cashier,chef',
            'password' => ['nullable', Password::min(8)],
        ]);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
            ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
        ]);

        return back()->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }
        $user->delete();
        return back()->with('success', 'User deleted.');
    }
}
