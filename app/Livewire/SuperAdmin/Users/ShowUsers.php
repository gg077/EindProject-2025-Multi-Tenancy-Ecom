<?php

namespace App\Livewire\SuperAdmin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
//use Maatwebsite\Excel\Facades\Excel;
//use App\Exports\UsersExport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ShowUsers extends Component
{
    use WithPagination;

    // UI-state en filters
    public $search = '';
    public $perPage = 10;
    public $showDeleted = false;
    public $message = '';
    public $showMessage = false;

    // Sorting
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Gebruikersselectie voor bulkacties
    public $selectedUsers = [];
    public $selectAll = false;

    // Inline editing
    public $editingUserId = null;
    public $editingName = '';
    public $editingEmail = '';
    public $editingPassword = '';
    public $editingPasswordConfirmation = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
        session()->flash('message', 'Gebruiker verwijderd.');
        session()->flash('message_type', 'error');
    }

    public function forceDelete($userId)
    {
        $user = User::ofSuperAdminOnly()->onlyTrashed()->findOrFail($userId); // Zoek verwijderde gebruiker van superadmin
        $this->authorize('forceDelete', $user);

        $user->forceDelete();
        session()->flash('message', 'Gebruiker permanent verwijderd.');
        session()->flash('message_type', 'error');
    }

    public function restore($userId)
    {
        $user = User::ofSuperAdminOnly()->onlyTrashed()->findOrFail($userId); // Zoek verwijderde gebruiker van superadmin
        $this->authorize('restore', $user);

        $user->restore();
        session()->flash('message', 'Gebruiker hersteld.');
        session()->flash('message_type', 'success');
    }

    public function showMessage($message)
    {
        $this->message = $message;
        $this->showMessage = true;
        $this->dispatch('message-shown'); // Livewire Trigger the message-shown event
    }

    public function hideMessage()
    {
        $this->showMessage = false;
    }

    public function bulkDelete()
    {
        $users = User::ofSuperAdminOnly()->
        whereIn('id', $this->selectedUsers)-> // haal gebruikers die geselecteerd zijn
        where('id', '!=', auth()->id())-> // Verwijder jezelf niet
        get();
        $count = 0;

        foreach ($users as $user) {
            // Als een gebruiker een superadmin is, stopt hij direct met verwijderen. Je mag die niet verwijderen.
            if($user->isSuperAdmin()) {
                session()->flash('message', 'Je kunt de superadmin niet verwijderen. Verwijder ' . $count . '/' . count($this->selectedUsers) . ' gebruikers.');
                session()->flash('message_type', 'error');
                return;
            }

            $this->authorize('delete', $user);
            $user->delete();
            $count++;
        }

        $this->selectedUsers = [];
        $this->selectAll = false;
        session()->flash('message', $count . ' gebruiker(s) verwijderd.'); // Toon bericht met aantal verwijderde gebruikers
        session()->flash('message_type', 'error');
    }

    public function bulkRestore()
    {
        $count = count($this->selectedUsers);
        User::ofSuperAdminOnly()->
        whereIn('id', $this->selectedUsers)->onlyTrashed()->chunk(100, function ($users) { // Doet dat in stukjes van 100 tegelijk (met chunk() is een built-in Laravel functie)
            foreach ($users as $user) {
                $this->authorize('restore', $user);
                $user->restore();
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
        User::ofSuperAdminOnly()->whereIn('id', $this->selectedUsers)->onlyTrashed()->chunk(100, function ($users) { // Doet dat in stukjes van 100 tegelijk (met chunk)
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
        // als je alles aanvinkt, dan worden alle zichtbare gebruikers geselecteerd
        if ($value) {
            $this->selectedUsers = $this->visibleUsersQuery()->pluck('id')->toArray();
        } else {
            // Reset selectAll status
            $this->selectedUsers = [];
        }
    }

    public function toggleSelect($userId)
    {
        // Als de gebruiker al geselecteerd is, verwijder je hem uit de selectie.
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]); // vergelijk twee arrays & verwijderd userId uit de selectie
        } else {
            // Als de gebruiker nog niet geselecteerd is, voeg hem toe aan de selectie
            $this->selectedUsers[] = $userId;
        }

        // Daarna wordt $selectAll geüpdatet als alle zichtbare gebruikers geselecteerd zijn.
        $this->selectAll = count($this->selectedUsers) === $this->visibleUsersQuery()->count();
    }

    private function visibleUsersQuery()
    {
        return User::ofSuperAdminOnly()->search($this->search)
            ->when($this->showDeleted, fn($q) => $q->onlyTrashed()) // Of je verwijderde gebruikers wil zien (showDeleted).
            ->orderBy($this->sortField, $this->sortDirection); // sortering
    }

    public function render()
    {
        $this->authorize('viewAny', User::class);

        return view('livewire.super-admin.users.show-users', [
            // Stuurt de gefilterde, gepagineerde gebruikerslijst mee naar de view (inclusief hun rollen).
            'users' => $this->visibleUsersQuery()->with('roles')->paginate($this->perPage)
        ]);
    }

    /**
     * Start inline editing voor een gebruiker
     */
    public function startEditing($userId)
    {
        $user = User::ofSuperAdminOnly()->find($userId);
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
        $this->resetEditing();
    }

    /**
     * Reset alle editing properties
     */
    private function resetEditing()
    {
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

        $user->update([
            'name' => $this->editingName,
            'email' => $this->editingEmail,
        ]);

        if ($this->editingPassword) {
            $user->update([
                'password' => Hash::make($this->editingPassword)
            ]);
        }

        session()->flash('message', 'Gebruiker succesvol bijgewerkt.');
        session()->flash('message_type', 'success');

        $this->resetEditing();
    }
}
