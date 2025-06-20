<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class ShowUsers extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showDeleted = false;
    public $message = '';
    public $showMessage = false;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedUsers = [];
    public $selectAll = false;
    public $editingUserId = null;
    public $editingName = '';
    public $editingEmail = '';
    public $editingPassword = '';
    public $editingPasswordConfirmation = '';

    public function updatingSearch()
    {
        $this->resetPage(); // Reset naar eerste pagina bij zoeken
    }

    public function sortBy($field)
    {
        // Als op hetzelfde veld wordt geklikt: richting omwisselen
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Nieuw veld om op te sorteren, begin met oplopend
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete(); // Soft delete (verplaatst naar prullenbak)
        session()->flash('message', 'Gebruiker verwijderd.');
        session()->flash('message_type', 'error');
    }

    public function forceDelete($userId)
    {
        $user = User::onlyTrashed()->findOrFail($userId); // Vind gebruiker in de prullenbak
        $this->authorize('forceDelete', $user);

        $user->forceDelete(); // Permanent verwijderen uit database
        session()->flash('message', 'Gebruiker permanent verwijderd.');
        session()->flash('message_type', 'error');
    }

    public function restore($userId)
    {
        $user = User::onlyTrashed()->findOrFail($userId); // Zoek soft deleted user
        $this->authorize('restore', $user);

        $user->restore(); // Herstellen
        session()->flash('message', 'Gebruiker hersteld.');
        session()->flash('message_type', 'success');
    }

    public function showMessage($message)
    {
        $this->message = $message; // Stel boodschap in
        $this->showMessage = true; // Toon boodschap
        $this->dispatch('message-shown'); // Stuur Livewire event naar frontend
    }

    public function hideMessage()
    {
        $this->showMessage = false;
    }

    public function bulkDelete()
    {
        $users = User::whereIn('id', $this->selectedUsers)->where('id', '!=', auth()->id())->get();
        $count = 0;

        foreach ($users as $user) {
            if($user->isTenantSuperAdmin()) {
                // Superadmin mag niet verwijderd worden
                session()->flash('message', 'Je kunt de superadmin niet verwijderen. Verwijder ' . $count . '/' . count($this->selectedUsers) . ' gebruikers.');
                session()->flash('message_type', 'error');
                return;
            }
            $this->authorize('delete', $user); // policy check
            $user->delete(); // Soft delete
            $count++;
        }

        $this->selectedUsers = []; // Reset selectie
        $this->selectAll = false;
        session()->flash('message', $count . ' gebruiker(s) verwijderd.');
        session()->flash('message_type', 'error');
    }

    public function bulkRestore()
    {
        $count = count($this->selectedUsers);
        User::whereIn('id', $this->selectedUsers)->onlyTrashed()->chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->authorize('restore', $user);
                $user->restore(); // Herstel elk gebruiker individueel
            }
        });

        $this->selectedUsers = [];
        $this->selectAll = false;
        session()->flash('message', $count . ' gebruiker(s) hersteld.');
        session()->flash('message_type', 'success');
    }

    public function bulkForceDelete()
    {
        $count = count($this->selectedUsers);
        User::whereIn('id', $this->selectedUsers)->where('id', '!=', auth()->id())->onlyTrashed()->chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->authorize('forceDelete', $user);
                $user->forceDelete();
            }
        });
        $this->selectedUsers = [];
        $this->selectAll = false;
        session()->flash('message', $count . ' gebruiker(s) permanent verwijderd.');
        session()->flash('message_type', 'error');
    }

    public function updatedSelectAll($value)
    {
        // Alles selecteren of deselecteren

        if ($value) {
            $this->selectedUsers = $this->visibleUsersQuery()->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function toggleSelect($userId)
    {
        // Gebruiker selecteren of deselecteren
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        } else {
            $this->selectedUsers[] = $userId;
        }

        // Check of alles geselecteerd is
        $this->selectAll = count($this->selectedUsers) === $this->visibleUsersQuery()->count();
    }

    private function visibleUsersQuery()
    {
        // Genereer query op basis van filters, zoekterm, soft deletes en sortering
        return User::search($this->search)
            ->when($this->showDeleted, fn($q) => $q->onlyTrashed())
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $this->authorize('viewAny', User::class); // policy check

        return view('livewire.admin.users.show-users', [
            'users' => $this->visibleUsersQuery()->with('roles')->paginate($this->perPage)
        ]);
    }

    /**
     * Start inline editing voor een gebruiker
     */
    public function startEditing($userId)
    {
        // Begin met inline bewerken van een gebruiker

        $user = User::find($userId);
        $this->editingUserId = $userId;
        $this->editingName = $user->name;
        $this->editingEmail = $user->email;
        $this->editingPassword = '';
        $this->editingPasswordConfirmation = '';
    }

    /**
     * Stop inline editing
     */
    public function cancelEditing()
    {
        $this->resetEditing(); // Stop inline editing
    }

    /**
     * Reset alle editing properties
     */
    private function resetEditing()
    {
        // Reset alle eigenschappen van inline editing
        $this->editingUserId = null;
        $this->editingName = '';
        $this->editingEmail = '';
        $this->editingPassword = '';
        $this->editingPasswordConfirmation = '';
    }

    /**
     * Update een gebruiker via inline editing
     */
    public function updateInline()
    {
        $user = User::findOrFail($this->editingUserId);

        // Check of de gebruiker de juiste permissies heeft
        $this->authorize('update', $user);

        // Validate de input
        $this->validate([
            'editingName' => 'required|string|max:255',
            'editingEmail' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where('tenant_id', tenant('id'))->ignore($user->id),
                Rule::unique('users', 'email')->where('tenant_id', '!=', tenant('id'))->ignore($user->email, 'email'),
            ],
            'editingPassword' => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($user) {
            // Update naam en e-mail
            $user->update([
                'name' => $this->editingName,
                'email' => $this->editingEmail,
            ]);

            if ($this->editingPassword) {
                // Optioneel nieuw wachtwoord instellen
                $user->update([
                    'password' => Hash::make($this->editingPassword)
                ]);
            }
        });

        session()->flash('message', 'Gebruiker succesvol bijgewerkt.');
        session()->flash('message_type', 'success');

        $this->resetEditing(); // Stop met bewerken
    }

    // on update showDeleted, reset page and search and reset selected users
    public function updatedShowDeleted()
    {
        // Als "toon verwijderde" aangepast wordt, reset filters
        $this->resetPage();
        $this->search = '';
        $this->selectedUsers = [];
    }
}
