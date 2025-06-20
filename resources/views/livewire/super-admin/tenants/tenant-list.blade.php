<div class="p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Tenant Management</h2>
        @can('create', \App\Models\Tenant::class)
            <!-- Knop om tenant aan te maken -->
            <a wire:navigate href="{{ route('tenants.create') }}" class="bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition dark:bg-blue-700 dark:hover:bg-blue-600 px-4 py-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Tenant
            </a>
        @endcan
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
        <!-- Zoekveld en statusfilter -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, domain, or email" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select wire:model.live="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="resetFilters" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg">
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Tenants Lijst -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Domain</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tenants as $tenant)
                        <!-- Rij per tenant -->
                        <tr>
                            <!-- Bedrijfsnaam -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->website_name }}</div>
                            </td>
                            <!-- Domeinnaam -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <a href="{{ tenant_route($tenant->domains->first()->domain, 'home') }}" target="_blank" class="text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">{{ $tenant->domains->first()->domain }}</a>
                                </div>
                            </td>
                            <!-- Eigenaar -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $tenant->tenantOwner->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $tenant->tenantOwner->email }}</div>
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                            <!-- Aangemaakt op -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $tenant->created_at->format('M d, Y') }}
                            </td>
                            <!-- Statistieken -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <div>Users: {{ $tenant->users_count ?? 0 }}</div>
                                    <div>Products: {{ $tenant->products_count ?? 0 }}</div>
                                    <div>Orders: {{ $tenant->orders_count ?? 0 }}</div>
                                </div>
                            </td>
                            <!-- Acties -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2 items-center">
                                    @canAny(['update', 'delete'], $tenant)
                                        <div class="absolute" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-blue-600 dark:text-blue-500 hover:text-blue-900 dark:hover:text-blue-400 focus:outline-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <!-- Dropdown menu -->
                                            <div x-show="open"
                                                @click.away="open = false"
                                                class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 dark:ring-gray-700 z-50">
                                                <div class="py-1" role="menu">
                                                    @can('update', $tenant)
                                                        <!-- Bewerken -->
                                                        <button wire:navigate href="{{ route('tenants.edit', $tenant->id) }}"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                                            <span class="flex items-center space-x-2">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                                <span>Edit</span>
                                                            </span>
                                                        </button>

                                                        <!-- Activeren of deactiveren -->
                                                        <button wire:click="suspend('{{ $tenant->id }}')"
                                                                wire:confirm="Are you sure you want to {{ $tenant->status === 'active' ? 'suspend' : 'activate' }} ({{ $tenant->website_name }})?"
                                                                @click="open = false"
                                                                class="w-full text-left px-4 py-2 text-sm text-amber-500 dark:text-orange-400 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2"
                                                                >
                                                            <span class="flex items-center space-x-2">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                                </svg>
                                                                <span>
                                                                    {{ $tenant->status === 'active' ? 'Suspend' : 'Activate' }}
                                                                </span>
                                                            </span>
                                                        </button>
                                                    @endcan

                                                    @can('delete', $tenant)
                                                            <!-- Verwijderen -->
                                                        <button wire:click="delete('{{ $tenant->id }}')"
                                                                wire:confirm="Are you sure you want to delete this tenant?"
                                                                @click="open = false"
                                                                class="w-full text-left px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2"
                                                                >
                                                            <span class="flex items-center space-x-2">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                                <span>Delete</span>
                                                            </span>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    @endcanAny
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No tenants found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $tenants->links() }}
        </div>
    </div>
</div>
