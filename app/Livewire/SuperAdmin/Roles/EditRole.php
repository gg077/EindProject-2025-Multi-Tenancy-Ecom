<?php

namespace App\Livewire\SuperAdmin\Roles;

use Livewire\Component;
use App\Models\Role;
use Illuminate\Validation\Rule;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;


class EditRole extends Component
{
    public Role $role;
    public $name = '';
    public $icon = '';
    public $selectedIcon = null;
    public $showIconPicker = false;
    public $search = '';
    public $modules = [];
    public $selectedPermissions = [];

    public function rules()
    {
        return [
            'name' => ['required', 'min:3', Rule::unique('roles')->where('tenant_id', tenant('id'))->ignore($this->role->id)],
            'icon' => 'required'
        ];
    }

    public function mount(Role $role)
    {
        $this->authorize('update', $role); // Policy check
        $this->role = $role;
        // Initieer formwaarden
        $this->name = $role->name;
        $this->icon = $role->icon ?? 'eye';
        $this->selectedIcon = [
            'name' => $role->icon ?? 'eye',
            'path' => $this->getIconPath($role->icon ?? 'eye')
        ];
        // Haal modules met permissies op (alleen user_type 2 = Super SuperAdmin)
        $this->modules = Module::where('user_type', 2)
            ->with('permissions')
            ->get()
            ->map(function ($module) {
                return [
                    'module' => $module->name,
                    'permissions' => $module->permissions->map(function ($perm) {
                        return ['id' => $perm->id, 'name' => $perm->name];
                    })->toArray(),
                ];
            })->toArray();
        // Haal alle permissies van de rol op
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
    }

    // Toon/verberg het icon picker venster
    public function toggleIconPicker()
    {
        $this->showIconPicker = !$this->showIconPicker;
    }

    // Bij het kiezen van een icoon
    public function selectIcon($name, $path)
    {
        $this->icon = $name;
        $this->selectedIcon = [
            'name' => $name,
            'path' => $path
        ];
        $this->showIconPicker = false;
    }

    // Retourneert kleurklasse voor gekozen icoon
    public function getIconColorClass()
    {
        return match ($this->icon) {
            'shield-check' => 'text-red-500',
            'pencil' => 'text-blue-500',
            'eye' => 'text-green-500',
            'document' => 'text-amber-500',
            default => 'text-gray-500'
        };
    }

    // Geeft de SVG path terug van het icoon
    public function getIconPath($iconName)
    {
        $icons = $this->availableIcons;
        return $icons[$iconName]['path'] ?? $icons['eye']['path'];
    }

    // Beschikbare iconen ophalen (max 30, eventueel gefilterd op zoekterm)
    public function getAvailableIconsProperty()
    {
        try {
            $icons = json_decode(file_get_contents(base_path('node_modules/@heroicons/react/24/outline/index.js')), true);
        } catch (\Exception $e) {
            // Als laden mislukt: gebruik fallback icons
            return [
                'eye' => ['path' => 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                'shield-check' => ['path' => 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z'],
                'pencil' => ['path' => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10'],
                'document' => ['path' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                'key' => ['path' => 'M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z']
            ];
        }

        // Filter iconen op zoekterm
        if (!empty($this->search)) {
            $icons = array_filter($icons, function ($name) {
                return str_contains(strtolower($name), strtolower($this->search));
            }, ARRAY_FILTER_USE_KEY);
        }

        // Maximaal 30 resultaten
        return array_slice($icons, 0, 30, true);
    }

    public function save()
    {
        $this->authorize('update', $this->role); // Policy check
        $validated = $this->validate(); // Validatie

        DB::transaction(function () use ($validated) { // Role aanmaken & permissies koppelen
            $this->role->update($validated);

            // Koppel permissies opnieuw
            if (!empty($this->selectedPermissions)) {
                $permissions = Permission::whereIn('id', $this->selectedPermissions)
                    ->whereHas('module', function ($query) {
                        // Enkel permissies van modules met user_type 2
                        $query->where('user_type', 2);
                    })
                    ->get();
                $this->role->syncPermissions($permissions);
            } else {
                // Geen permissies geselecteerd? Alles loskoppelen
                $this->role->syncPermissions([]);
            }
        });

        session()->flash('message', __('Rol succesvol bijgewerkt.'));
        session()->flash('message_type', 'success');

        return $this->redirect(route('roles.index'), true);
    }

    public function render()
    {
        $this->authorize('update', $this->role);
        return view('livewire.super-admin.roles.edit-role');
    }
}
