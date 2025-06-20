<?php

namespace App\Livewire\SuperAdmin\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;

class ShowRoles extends Component
{
    use WithPagination;

    public function render()
    {
        $this->authorize('viewAny', Role::class); // policy check

        // Alle roles ophalen zonder tenant_id, met aantal users en permissions per role
        return view('livewire.super-admin.roles.show-roles', [
            'roles' => Role::whereNull('tenant_id')->withCount('users', 'permissions')->paginate(10)
        ]);
    }

    public function delete($roleId)
    {
        // Zoek de rol op basis van ID, alleen als:
        // - Ze geen tenant_id heeft (dus globale rol)
        // - Ze geen gekoppelde gebruikers heeft
        $role = Role::whereNull('tenant_id')->
        whereDoesntHave('users')->
        findOrFail($roleId);

        $this->authorize('delete', $role); // policy check
        $role->delete(); // verwijderen
    }
}
