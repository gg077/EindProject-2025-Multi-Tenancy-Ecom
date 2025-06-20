<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();
        
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $this->email]);

        // validatie user bestaat en wachtwoord klopt
        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        // login user als hij niet al ingelogd is
        if (tenant('id') === $user->tenant_id) {
            Auth::login($user, $this->remember); // Validatie en controle & laravel logt de gebruiker in
            Session::regenerate();
            $this->redirectIntended(default: Auth::user()->getRedirectRoute());
        } elseif ($user->tenant_id) {
            // user is tenant user, dus impersonate
            $tenant = $user->tenant;
            // genereer impersonate token
            $token = tenancy()->impersonate(
                $tenant,
                $user->id,
                $user->isAdmin() ? '/admin/dashboard' : '/',
                'web'
            );

            // Redirect naar tenant domein met impersonate token
            $domain = $tenant->domains?->first()?->domain;
            $currentScheme = request()->getScheme();
            Session::regenerate(); // regenereer sessie
            $this->redirectIntended("{$currentScheme}://{$domain}/impersonate/{$token->token}");
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}
