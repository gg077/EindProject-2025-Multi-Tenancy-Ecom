<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantRegister extends Component
{
    public $tenant_name, $tenant_domain, $name, $email, $password, $password_confirmation;

    protected function rules()
    {
        return [
            'tenant_name'   => 'required|string|max:50|unique:tenants,id',
            'tenant_domain' => 'required|string|max:100|unique:domains,domain',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
        ];
    }

    public function register()
    {
        $this->validate();

        $slug  = Str::slug($this->tenant_name);
        $input = trim($this->tenant_domain);

        // Voorbeeld: Als de gebruiker "coolshop" invult en je zit op multi-tenant.be, wordt het resultaat: coolshop.multi-tenant.be
        $host = Str::contains($input, '.')
            ? $input
            : Str::slug($input).'.'. request()->getHost();

        DB::beginTransaction();
        try {
            $tenant = Tenant::create(['id' => $slug, 'slug' => $slug, 'website_name' => $this->tenant_name]);

            $tenant->domains()->create(['domain' => $host]);

            // maak eigenaar gebruiker aan
            $tenant->run(function () use ($tenant) {
                User::create([
                    'name'      => $this->name,
                    'email'     => $this->email,
                    'password'  => Hash::make($this->password),
                    'tenant_id' => $tenant->id,
                    'is_admin'  => true,
                ]);
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while creating the tenant. Please try again.');
            return;
        }

        return redirect(tenant_route($tenant->domains()->first()->domain, 'login'));
    }

    public function render()
    {
        return view('livewire.auth.tenant-register')->layout('components.layouts.auth');

    }
}
