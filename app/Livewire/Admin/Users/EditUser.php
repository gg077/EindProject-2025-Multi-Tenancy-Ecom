<?php

namespace App\Livewire\Admin\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

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
                // Moet uniek zijn binnen dezelfde tenant (behalve bij huidige gebruiker)
                Rule::unique('users')->where('tenant_id', tenant('id'))->ignore($this->user->id),
                // Dubbele beveiliging: voorkomt dat een email ook in andere tenants conflicteert
                Rule::unique('users')->where('tenant_id', '!=', tenant('id'))->ignore($this->user->email, 'email')
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string',
                // Rol moet bestaan of default role zijn
                function ($attribute, $value, $fail) {
                if (!Role::where('name', $this->role)->exists() && !in_array($this->role, [config('tenant.default_role')])) {
                    $fail('De rol is ongeldig.');
                }
            }],
        ];
    }

    public function update()
    {
        $this->authorize('update', $this->user);

        $this->validate();

        $isTenantSuperAdmin = $this->user->isTenantSuperAdmin(); // Check of gebruiker tenant super admin is

        DB::beginTransaction(); // Begin transactie
        try{
            // Update naam, email, is_admin op basis van rol
            $this->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'is_admin' => ($this->role != config('tenant.default_role') ? 1 : 0),
            ]);
            // Als wachtwoord is opgegeven, versleutel en sla op
            if ($this->password) {
                $this->user->update([
                    'password' => Hash::make($this->password)
                ]);
            }

            // Rol toewijzen tenzij het om een superadmin gaat
            if(!in_array($this->role, [config('tenant.default_role')]) && !$isTenantSuperAdmin) {
                $this->user->syncRoles($this->role);
            }
            // Verwijder rol als het nu een standaardrol moet zijn
            if($this->role == config('tenant.default_role') && !$isTenantSuperAdmin && $this->user->roles->first()?->name) {
                $this->user->removeRole($this->user->roles->first()?->name); // Verwijder huidige rol
            }

            DB::commit(); // Alles goed gegaan, commit
        }catch (\Exception $e){
            DB::rollBack(); // Bij fout: rollback
            session()->flash('message', 'Er is een fout opgetreden bij het bijwerken van de gebruiker: ');
            session()->flash('message_type', 'error');
            return;
        }

        session()->flash('message', 'Gebruiker succesvol bijgewerkt.');
        session()->flash('message_type', 'success');

        return $this->redirectRoute('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.users.edit-user', [
            // Voor dropdown selectie van rollen: default rol + alle rollen
            'roles' => ['' => 'Selecteer rol', config('tenant.default_role') => config('tenant.default_role')] + Role::pluck('name', 'name')->toArray()
        ]);
    }
}
