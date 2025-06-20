<?php

namespace App\Livewire\Admin\Roles;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class ShowRoles extends Component
{
    use WithPagination;

    public function render()
    {
        $this->authorize('viewAny', Role::class);

        return view('livewire.admin.roles.show-roles', [
            'roles' => Role::withCount('users', 'permissions')->paginate(10) // Tel hoeveel users en permissions elke rol heeft
        ]);
    }

    public function delete($roleId)
    {
        // Zoek de rol op, maar alleen als er GEEN gebruikers aan gekoppeld zijn
        $role = Role::whereDoesntHave('users')->findOrFail($roleId);
        $this->authorize('delete', $role);
        $role->delete();
    }
}
