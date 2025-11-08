<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthSessionController extends Controller
{
    public function __construct(protected ApiClient $api)
    {
    }

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $response = $this->api->post('/api/auth/login', $credentials);

        if ($response->failed()) {
            return back()
                ->withErrors(['email' => 'Invalid credentials provided.'])
                ->withInput($request->only('email'));
        }

        $token = $response->json('token');

        if (! $token) {
            return back()
                ->withErrors(['email' => 'Unable to retrieve authentication token.'])
                ->withInput($request->only('email'));
        }

        $request->session()->put('api_token', $token);
        $request->session()->regenerate();

        return redirect('/jobs')
            ->with('status', 'Logged in successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        if ($token = $request->session()->get('api_token')) {
            $this->api->post('/api/auth/logout', [], $token);
        }

        $request->session()->forget('api_token');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('status', 'Logged out successfully.');
    }
}


