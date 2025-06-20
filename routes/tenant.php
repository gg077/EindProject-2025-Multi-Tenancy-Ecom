<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureTenantActive;
use App\Http\Middleware\EnsureTenantSetupCompleted;
use App\Http\Middleware\MustBeAdmin;
use App\Http\Middleware\MustBeBuyer;
use App\Livewire\Admin\Product\CreateProduct;
use App\Livewire\Admin\Product\EditProduct;
use App\Livewire\Admin\Product\ShowProducts;
use App\Livewire\Admin\TenantSettingsForm;
use App\Livewire\Admin\Users\CreateUser;
use App\Livewire\Admin\Users\EditUser;
use App\Livewire\Admin\Users\ShowUsers;
use App\Livewire\Buyer\Orders\OrderDetails;
use App\Livewire\Buyer\Orders\OrderList;
use App\Livewire\Buyer\ProductListing\ProductDetails;
use App\Livewire\Buyer\ProductListing\ProductListing;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    EnsureTenantActive::class,
])->group(function () {
    // Add impersonation route
    Route::get('/impersonate/{token}', function ($token) {
        return UserImpersonation::makeResponse($token);
    })->name('impersonate');

    Route::get('/', ProductListing::class)->name('home');
    Route::get('/products/{slug}', ProductDetails::class)->name('products.show');

    Route::middleware(['auth', MustBeBuyer::class])->group(function () {
        Route::redirect('settings', 'settings/profile');
        Route::get('settings/profile', Profile::class)->name('settings.profile');
        Route::get('settings/password', Password::class)->name('settings.password');
        Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

        Route::get('/checkout', App\Livewire\Buyer\Checkout::class)->name('checkout');
        Route::get('/checkout/success/{order}', App\Livewire\Buyer\CheckoutSuccess::class)->name('checkout.success');
        Route::get('/checkout/cancel/{order}', App\Livewire\Buyer\CheckoutSuccess::class)->name('checkout.cancel');

        Route::get('/orders', OrderList::class)->name('orders.index');
        Route::get('/orders/{order}', OrderDetails::class)->name('orders.show');
        Route::get('/orders/{order}/invoice', [App\Http\Controllers\InvoiceController::class, 'downloadInvoice'])->name('orders.invoice');
    });

    Route::middleware(['auth', MustBeAdmin::class, EnsureTenantSetupCompleted::class])->prefix('admin')->name('admin.')->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::redirect('settings', 'settings/profile');
        Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
        Volt::route('settings/password', 'settings.password')->name('settings.password');
        Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
        Route::get('/users', ShowUsers::class)->name('users.index');
        Route::get('/users/create', CreateUser::class)->name('users.create');
        Route::get('/users/{user}/edit', EditUser::class)->name('users.edit');

        // Role routes
        Route::get('/roles', \App\Livewire\Admin\Roles\ShowRoles::class)->name('roles.index');
        Route::get('/roles/create', \App\Livewire\Admin\Roles\CreateRole::class)->name('roles.create');
        Route::get('/roles/{role}/edit', \App\Livewire\Admin\Roles\EditRole::class)->name('roles.edit');

        // Category routes
        Route::get('/categories', \App\Livewire\Admin\Categories\ShowCategory::class)->name('categories.index');
        Route::get('/categories/create', \App\Livewire\Admin\Categories\CreateCategory::class)->name('categories.create');
        Route::get('/categories/{category}/edit', \App\Livewire\Admin\Categories\EditCategory::class)->name('categories.edit');

        Route::get('/products', ShowProducts::class)->name('products.index');
        Route::get('/products/create', CreateProduct::class)->name('products.create');
        Route::get('/products/{product}/edit', EditProduct::class)->name('products.edit');
        Route::get('/products/{slug}', App\Livewire\Admin\Product\ProductDetails::class)->name('products.show');

        Route::get('/orders', App\Livewire\Admin\Orders\OrderList::class)->name('orders.index');
        Route::get('/orders/{order}', App\Livewire\Admin\Orders\OrderDetails::class)->name('orders.show');
        Route::get('/orders/{order}/invoice', [App\Http\Controllers\InvoiceController::class, 'downloadInvoice'])->name('orders.invoice');

        Route::get('/settings/onboarding', TenantSettingsForm::class)->name('onboarding');
    });

    require __DIR__ . '/auth.php';
});
