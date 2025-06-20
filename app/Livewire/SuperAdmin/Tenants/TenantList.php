<?php

namespace App\Livewire\SuperAdmin\Tenants;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TenantList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    protected $listeners = ['tenantCreated' => '$refresh', 'tenantUpdated' => '$refresh'];

    public function render()
    {
        $this->authorize('viewAny', Tenant::class);
        // Bouw de query op met filters en relaties
        $query = Tenant::query()
            ->with(['domains', 'tenantOwner']) // Laad domein- en eigenaargegevens mee
            ->withCount(['users', 'products', 'orders']) // Tel gebruikers, producten en bestellingen
            ->when($this->search, function ($query) { // Zoekfilter toepassen indien $search ingevuld is
                $query->where('website_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('domains', function ($query) {
                        $query->where('domain', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('users', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            });

        return view('livewire.super-admin.tenants.tenant-list', [
            'tenants' => $query->paginate(10),
        ]);
    }

    public function delete($tenantId)
    {
        // Zoek tenant, gooi 404 als niet gevonden
        $tenant = Tenant::findOrFail($tenantId);

        $this->authorize('delete', $tenant);

        DB::transaction(function () use ($tenant) {
            $tenant->domains()->delete();
            $tenant->users()->delete();
            // Delete the tenant
            $tenant->delete();
        });
        session()->flash('message', 'Tenant deleted successfully.');
    }

    public function suspend($tenantId)
    {
        // Zoek tenant, gooi 404 als niet gevonden
        $tenant = Tenant::findOrFail($tenantId);

        $this->authorize('update', $tenant);
        // Toggle status tussen 'active' en 'suspended'
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        // Update de tenant-status
        $tenant->update(['status' => $newStatus]);
        session()->flash('message', 'Tenant status updated successfully.');
    }

    public function resetFilters()
    {
        // Reset zoekveld en statusfilter en ga terug naar pagina 1
        $this->reset(['search', 'status']);
        $this->resetPage();
    }
}
