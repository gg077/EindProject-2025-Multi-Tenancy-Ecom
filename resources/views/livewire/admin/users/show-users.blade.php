{{-- 📁 resources/views/livewire/users/show-users.blade.php --}}

<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-4">
            <h2 class="text-2xl font-semibold dark:text-white">Gebruikersoverzicht</h2>
            <label class="inline-flex items-center">
                <input type="checkbox" wire:model.live="showDeleted" class="form-checkbox h-4 w-4 text-blue-600 dark:bg-gray-700 dark:border-gray-600">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Toon verwijderde gebruikers</span>
            </label>
        </div>

        <div class="flex items-center space-x-4">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Zoek gebruikers..."
                    class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <x-ui.flash-message
            :message="session('message')"
            :type="session('message_type', 'success')"
        />
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <!-- Export knoppen -->
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-end">
                @can('create', App\Models\User::class)
                    <a wire:navigate href="{{ route('admin.users.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition dark:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Nieuwe gebruiker
                    </a>
                @endcan
            </div>
        </div>

        <!-- Bulk acties toolbar -->
        @if(count($selectedUsers) > 0)
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ count($selectedUsers) }} gebruikers geselecteerd
                        </span>
                        <div class="flex space-x-2">
                            @if(!$showDeleted && auth()->user()->can('delete user'))
                                <button wire:click="bulkDelete" wire:confirm="Weet je zeker dat je de geselecteerde gebruikers wilt verwijderen?" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800">
                                    Verwijderen
                                </button>
                            @endcan

                            @if($showDeleted)
                                @can('restore user')
                                    <button wire:click="bulkRestore" wire:confirm="Weet je zeker dat je de geselecteerde gebruikers wilt herstellen?" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800">
                                        Herstellen
                                    </button>
                                @endcan
                                @can('forceDelete user')
                                        <button wire:click="bulkForceDelete" wire:confirm="Weet je zeker dat je de geselecteerde gebruikers permanent wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt." class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-800 hover:bg-red-900 dark:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-800 dark:focus:ring-offset-gray-800">
                                            Permanent verwijderen
                                        </button>
                                @endcan
                            @endif
                        </div>
                    </div>
                    <button wire:click="$set('selectedUsers', [])" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        Selectie opheffen
                    </button>
                </div>
            </div>
        @endif

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model.live="selectAll" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                            <span class="ml-2">Selecteer</span>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <button wire:click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-white group">
                            <span>Naam</span>
                            <div class="flex flex-col">
                                <svg class="w-3 h-3 {{ $sortField === 'name' && $sortDirection === 'asc' ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <svg class="w-3 h-3 {{ $sortField === 'name' && $sortDirection === 'desc' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            @if($sortField === 'name')
                                <span class="text-blue-500 text-xs ml-1">({{ $sortDirection === 'asc' ? 'oplopend' : 'aflopend' }})</span>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortBy('email')" class="flex items-center space-x-1 hover:text-gray-700 group">
                            <span>E-mailadres</span>
                            <div class="flex flex-col">
                                <svg class="w-3 h-3 {{ $sortField === 'email' && $sortDirection === 'asc' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <svg class="w-3 h-3 {{ $sortField === 'email' && $sortDirection === 'desc' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            @if($sortField === 'email')
                                <span class="text-blue-500 text-xs ml-1">({{ $sortDirection === 'asc' ? 'oplopend' : 'aflopend' }})</span>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-gray-700 group">
                            <span>Geregistreerd op</span>
                            <div class="flex flex-col">
                                <svg class="w-3 h-3 {{ $sortField === 'created_at' && $sortDirection === 'asc' ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <svg class="w-3 h-3 {{ $sortField === 'created_at' && $sortDirection === 'desc' ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            @if($sortField === 'created_at')
                                <span class="text-blue-500 dark:text-blue-400 text-xs ml-1">({{ $sortDirection === 'asc' ? 'oplopend' : 'aflopend' }})</span>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rollen</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acties</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($users as $user)
                    <tr class="{{ $user->trashed() ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox"
                                   wire:model.live="selectedUsers"
                                   value="{{ $user->id }}"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            @if($editingUserId === $user->id)
                                <input type="text" wire:model="editingName" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                @error('editingName') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                            @else
                                {{ $user->name }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            @if($editingUserId === $user->id)
                                <input type="email" wire:model="editingEmail" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                @error('editingEmail') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                            @else
                                {{ $user->email }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $user->created_at->format('d-m-Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($role->name === 'super-admin')
                                            bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100
                                        @elseif($role->name === 'editor')
                                            bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                        @else
                                            bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @empty
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium dark:bg-gray-700 dark:text-gray-300">
                                        {{ $user->guessRoleName() }}
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @if($editingUserId === $user->id)
                                    <!-- Inline editing knoppen -->
                                    <button wire:click="updateInline" class="text-green-600 hover:text-green-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    <button wire:click="cancelEditing" class="text-gray-600 hover:text-gray-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @elseif($user->deleted_at)
                                    <!-- Herstel knop voor verwijderde gebruikers -->
                                    @canAny(['restore', 'forceDelete'], $user)
                                        <div class="absolute" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-blue-600 hover:text-blue-900 focus:outline-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>

                                            <div x-show="open"
                                                 @click.away="open = false"
                                                 class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1" role="menu">
                                                    @can('restore', $user)
                                                        <!-- Inline edit optie -->
                                                        <button wire:click="restore({{ $user->id }})"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2"
                                                                role="menuitem">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                            </svg>
                                                            <span>Herstellen</span>
                                                        </button>
                                                    @endcan

                                                    @if(auth()->id() != $user->id && auth()->user()->can('forceDelete', $user))
                                                        <!-- Verwijder optie -->
                                                        <button wire:click="forceDelete({{ $user->id }})"
                                                                wire:confirm="Weet u zeker dat u deze gebruiker definitief wilt verwijderen?"
                                                                class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100 flex items-center space-x-2"
                                                                role="menuitem">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            <span>Verwijderen</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                @else
                                    <!-- Dropdown menu voor edit opties -->
                                    <div class="absolute" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-blue-600 hover:text-blue-900 focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </button>

                                        <div x-show="open"
                                             @click.away="open = false"
                                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1" role="menu">
                                                @can('update', $user)
                                                    <!-- Inline edit optie -->
                                                    <button wire:click="startEditing({{ $user->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2"
                                                            role="menuitem">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        <span>Snel bewerken</span>
                                                    </button>

                                                    <!-- Volledige edit optie -->
                                                    <a wire:navigate href="{{ route('admin.users.edit', $user) }}"
                                                       class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2"
                                                       role="menuitem">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <span>Volledig bewerken</span>
                                                    </a>
                                                @endcan

                                                @if(auth()->id() != $user->id && auth()->user()->can('delete', $user))
                                                    <!-- Verwijder optie -->
                                                    <button wire:click="delete({{ $user->id }})"
                                                            wire:confirm="Weet je zeker dat je deze gebruiker wilt verwijderen?"
                                                            class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100 flex items-center space-x-2"
                                                            role="menuitem">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        <span>Verwijderen</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Geen gebruikers gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
