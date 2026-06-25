<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', Rule::in(['customer', 'cleaner'])],
            'phone' => ['nullable', 'string', 'max:40', 'required_if:role,cleaner'],
            'address' => ['nullable', 'string', 'max:255', 'required_if:role,cleaner'],
            'business_name' => ['nullable', 'string', 'max:255', 'required_if:role,cleaner'],
            'description' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:120', 'required_if:role,cleaner'],
            'turnaround_time' => ['nullable', 'string', 'max:120'],
            'opening_hours' => ['nullable', 'string', 'max:160'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request): User {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => $request->input('role'),
                'phone' => $request->filled('phone') ? $request->input('phone') : null,
                'address' => $request->filled('address') ? $request->input('address') : null,
            ]);

            if ($user->role === 'cleaner') {
                Cleaner::query()->create([
                    'user_id' => $user->id,
                    'business_name' => $request->input('business_name'),
                    'description' => $request->filled('description') ? $request->input('description') : null,
                    'address' => $request->input('address'),
                    'city' => $request->input('city'),
                    'phone' => $request->input('phone'),
                    'turnaround_time' => $request->filled('turnaround_time') ? $request->input('turnaround_time') : null,
                    'opening_hours' => $request->filled('opening_hours') ? $request->input('opening_hours') : null,
                    'is_available' => true,
                    'is_approved' => false,
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route($this->dashboardRouteFor($user));
    }

    private function dashboardRouteFor(User $user): string
    {
        return match ($user->role) {
            'cleaner' => 'vendor.dashboard',
            'admin' => 'admin.dashboard',
            default => 'client.dashboard',
        };
    }
}
