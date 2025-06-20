<?php

namespace App\Livewire\SuperAdmin\Tenants;

use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TenantEdit extends Component
{
    public Tenant $tenant;
    public $website_name;
    public $owner_name;
    public $owner_email;
    public $domain;
    public $password;
    public $password_confirmation;
    public $status = 'active';

    public function mount(Tenant $tenant)
    {
        $this->authorize('update', $tenant);
        $tenant->load('tenantOwner');
        // Vul de component met de bestaande gegevens van de tenant
        $this->tenant = $tenant;
        $this->website_name = $tenant->website_name;
        $this->domain = $tenant->domains->first()->domain; // neem het eerste gekoppelde domein
        $this->owner_name = $tenant->tenantOwner->name;
        $this->owner_email = $tenant->tenantOwner->email;
        $this->status = $tenant->status;
    }

    protected function rules()
    {
        return [
            'website_name' => 'required|min:3',
            'owner_name' => 'required|min:3',
            // E-mailadres moet uniek zijn binnen deze tenant, behalve voor huidige eigenaar
            'owner_email' => ['required', 'email', Rule::unique('users', 'email')->where('tenant_id', $this->tenant->id)->ignore($this->tenant->tenantOwner->id)],
            'domain' => 'required|unique:domains,domain,' . $this->tenant->id . ',tenant_id',
            'password' => 'nullable|min:8|confirmed',
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ];
    }

    public function render()
    {
        $this->authorize('update', $this->tenant);
        return view('livewire.super-admin.tenants.tenant-edit');
    }

    public function updateTenant()
    {
        $this->authorize('update', $this->tenant);
        $this->validate();
        $this->domain = trim($this->domain);

        $this->domain = Str::contains($this->domain, '.')
            ? $this->domain
            : Str::slug($this->domain) . '.' . request()->getHost();

        $this->validate();

        DB::transaction(function () {
            $this->tenant->update([
                'website_name' => $this->website_name,
                'status' => $this->status,
            ]);
            // Update het domeinrecord
            $this->tenant->domains->first()->update([
                'domain' => $this->domain,
            ]);
            // Update de eigenaar (admin gebruiker)
            $this->tenant->tenantOwner->update([
                'name' => $this->owner_name,
                'email' => $this->owner_email,
                // Alleen wachtwoord aanpassen als er een nieuw is opgegeven
                'password' => $this->password ? Hash::make($this->password) : $this->tenant->tenantOwner->password,
            ]);
        });

        session()->flash('message', 'Tenant succesvol bijgewerkt.');
        session()->flash('message_type', 'success');
        return $this->redirect(route('tenants.index'), true);
    }
}
