<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use harmonic\InertiaTable\Facades\InertiaTable;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class UsersController extends Controller {
    public function index() {
        $model = new User();
        return InertiaTable::index($model);
    }

    public function create() {
        return Inertia::render('Users/Create');
    }

    public function store() {
        $validated = Request::validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')],
            'password' => ['required', 'min:8'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        Auth::user()->create($validated);

        return Redirect::route('users');
    }

    public function edit(User $user) {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'deleted_at' => $user->deleted_at,
            ],
        ]);
    }

    public function update(User $user) {
        Request::validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable'],
        ]);

        $user->update(Request::only('name', 'email'));

        if (Request::get('password')) {
            $user->update(['password' => Hash::make(Request::get('password'))]);
        }

        return Redirect::route('users.edit', $user);
    }

    public function destroy(User $user) {
        $user->delete();

        return Redirect::route('users.edit', $user);
    }

    public function restore(User $user) {
        $user->restore();

        return Redirect::route('users.edit', $user);
    }
}
