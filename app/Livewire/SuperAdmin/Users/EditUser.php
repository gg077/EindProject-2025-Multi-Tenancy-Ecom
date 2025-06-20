<?php

namespace App\Livewire\SuperAdmin\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Role;

class EditUser extends Component
{
    public User $user;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;

    public function mount(User $user)
    {
        $this->authorize('update', $user);
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        // Haal de naam van de eerste rol op, of een gegokte rolnaam als er geen is
        $this->role = $user->roles->first()?->name ?? $user->guessRoleName();
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                // Uniek binnen de huidige tenant (behalve voor huidige user)
                Rule::unique('users')->where('tenant_id', tenant('id'))->ignore($this->user->id),
                // Email mag niet ook al bestaan bij een andere tenant
                Rule::unique('users')->where('tenant_id', '!=', tenant('id'))->ignore($this->user->email, 'email')
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string',
                // Custom validatie: controleer of de rol bestaat
                function ($attribute, $value, $fail) {
                if (!Role::where('name', $this->role)->exists()) {
                    $fail('De rol is ongeldig.');
                }
            }],
        ];
    }

    public function update()
    {
        // Check of de gebruiker de juiste permissions heeft
        $this->authorize('update', $this->user);

        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => 1,
        ]);

        if ($this->password) {
            $this->user->update([
                'password' => Hash::make($this->password)
            ]);
        }

        // Update de rol
        $this->user->syncRoles($this->role);

        session()->flash('message', 'Gebruiker succesvol bijgewerkt.');
        session()->flash('message_type', 'success');

        return $this->redirectRoute('users.index');
    }

    public function render()
    {
        return view('livewire.super-admin.users.edit-user', [
            // Voeg een lege optie toe voor placeholder
            'roles' => ['' => 'Selecteer rol'] + Role::pluck('name', 'name')->toArray()
        ]);
    }
}
