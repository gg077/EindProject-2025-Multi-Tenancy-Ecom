<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\SuperAdmin\Roles\CreateRole;
use App\Livewire\SuperAdmin\Roles\EditRole;
use App\Livewire\SuperAdmin\Roles\ShowRoles;
use App\Livewire\SuperAdmin\Tenants\TenantCreate;
use App\Livewire\SuperAdmin\Tenants\TenantEdit;
use App\Livewire\SuperAdmin\Tenants\TenantList;
use App\Livewire\SuperAdmin\Users\ShowUsers;
use App\Livewire\SuperAdmin\Users\CreateUser;
use App\Livewire\SuperAdmin\Users\EditUser;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\TenantRegister;

foreach (config('tenancy.central_domains') as $domain) {

    Route::domain($domain)->group(function () {
        Route::get('tenant/register', TenantRegister::class)->name('tenant.register');
        Route::get('/', function () {
            return view('welcome');
        })->name('home');

        Route::view('dashboard', 'dashboard')
            ->middleware(['auth', 'verified'])
            ->name('dashboard');

        Route::middleware(['auth'])->group(function () {
            Route::redirect('settings', 'settings/profile');
            Route::get('settings/profile', Profile::class)->name('settings.profile');
            Route::get('settings/password', Password::class)->name('settings.password');
            Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

            Route::get('/users', ShowUsers::class)->name('users.index');
            Route::get('/users/create', CreateUser::class)->name('users.create');
            Route::get('/users/{user}/edit', EditUser::class)->name('users.edit');

            // Role routes
            Route::get('/roles', ShowRoles::class)->name('roles.index');
            Route::get('/roles/create', CreateRole::class)->name('roles.create');
            Route::get('/roles/{role}/edit', EditRole::class)->name('roles.edit');

            Route::get('/tenants', TenantList::class)->name('tenants.index');
            Route::get('/tenants/create', TenantCreate::class)->name('tenants.create');
            Route::get('/tenants/{tenant}/edit', TenantEdit::class)->name('tenants.edit');
        });

        require __DIR__ . '/auth-central.php';
    });
}
