<div>
    <!-- STATISTIEKKAARTEN (Totaal, Actief, Inactief) -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6 pt-8">
        <!-- Totale aantal tenants -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Tenants</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($tenantStats['total']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <!-- Aantal actieve tenants -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Tenants</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($tenantStats['active']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <!-- Aantal inactieve tenants -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Inactive Tenants</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($tenantStats['inactive']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <!-- JAAR SELECTIE DROPDOWN -->
    <div class="mb-6">
        <label for="year" class="block text-sm font-medium text-black dark:text-white">Select Year</label>
        <select wire:model.live="selectedYear" id="year" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black dark:text-white">
            <!-- Show last 5 years in dropdown -->
            @for($year = now()->year; $year >= now()->year - 5; $year--)
                <option value="{{ $year }}">{{ $year }}</option>
            @endfor
        </select>
    </div>
    <!-- GRAFIEK: TENANTS PER MAAND -->
    <div class="grid grid-cols-1 gap-6 dark:text-black">
        <div class="bg-white rounded-lg shadow p-6 h-96">

            <!-- Toon grafiek alleen als hij klaar is met laden -->
            <div wire:loading.remove wire:target="selectedYear" class="h-full dark:text-black">
                <livewire:livewire-line-chart
                    key="{{ $tenantsPerMonthChart->reactiveKey() }}"
                    :line-chart-model="$tenantsPerMonthChart"
                />
            </div>
            <!-- Laadindicator bij jaarswitch -->
            <div wire:loading wire:target="selectedYear" class="flex items-center justify-center h-full">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
            </div>
        </div>
    </div>
</div>
