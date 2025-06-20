<?php

namespace App\Livewire\Admin\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateUser extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role;

    public function mount()
    {
        $this->authorize('create', User::class);
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->where('tenant_id', tenant('id'))->where('deleted_at', null)], // Email moet uniek zijn binnen dezelfde tenant en niet soft-deleted
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string',
                // Custom validatie: rol moet bestaan óf gelijk zijn aan de default role van tenant
                function ($attribute, $value, $fail) {
                if (!Role::where('name', $this->role)->exists() && !in_array($this->role, [config('tenant.default_role')])) {
                    $fail('De rol is ongeldig.');
                }
            }],
        ];
    }

    public function save()
    {
        $this->validate();

        // Maak nieuwe gebruiker aan
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_admin' => $this->role != config('tenant.default_role') ? 1 : 0,
        ]);

        // Ken de rol toe, maar alleen als het geen standaard tenant-role is
        if($this->role != config('tenant.default_role'))
            $user->assignRole($this->role); // Spatie Laravel Permission

        session()->flash('message', 'Gebruiker succesvol aangemaakt.');
        session()->flash('message_type', 'success');

        return $this->redirect(route('admin.users.index'), true);
    }

    public function render()
    {
        return view('livewire.admin.users.create-user', [
            'roles' => ['' => 'Selecteer rol', config('tenant.default_role') => config('tenant.default_role')] + Role::pluck('name', 'name')->toArray(),
        ]);
    }
}
