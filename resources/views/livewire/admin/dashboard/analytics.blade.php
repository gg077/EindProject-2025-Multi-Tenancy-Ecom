<div>
    <!-- Statistics Cards Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6 pt-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Sales All Time</dt>
                        <dd class="text-lg font-medium text-gray-900">€ {{ number_format($totalSalesAllTime, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Registered Clients</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($totalRegisteredClients) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Best Sold Products</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            @forelse ($top3BestSoldProducts as $p)
                                {{$p->name}} <br/>
                            @empty
                                <!-- Geen producten beschikbaar -->
                            @endforelse
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <label for="year" class="block text-sm font-medium text-black dark:text-white">Select Year</label>
        <select wire:model.live="selectedYear" id="year" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black dark:text-white">
            <!-- Laatste 5 jaar tonen in dropdown -->
        @for($year = now()->year; $year >= now()->year - 5; $year--)
                <option value="{{ $year }}">{{ $year }}</option>
            @endfor
        </select>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 dark:text-black">
        <div class="bg-white rounded-lg shadow p-6 h-96">
            <div wire:loading.remove wire:target="selectedYear" class="h-full dark:text-black">
{{--               Kaart 1 Laat kolomgrafiek zien met Livewire Charts--}}
                <livewire:livewire-column-chart
                    key="{{ $revenuePerProductChart->reactiveKey() }}"
                    :column-chart-model="$revenuePerProductChart"
                />
            </div>
            <!-- Laadindicator tijdens het laden -->
            <div wire:loading wire:target="selectedYear" class="flex items-center justify-center h-full">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 h-96">
            <div wire:loading.remove wire:target="selectedYear" class="h-full">
                <!-- Kaart 2: Lijngrafiek voor omzet per maand -->
                <livewire:livewire-line-chart
                    key="{{ $revenuePerMonthChart->reactiveKey() }}"
                    :line-chart-model="$revenuePerMonthChart"
                />
            </div>
            <!-- Loader tijdens grafiek-refresh -->
            <div wire:loading wire:target="selectedYear" class="flex items-center justify-center h-full">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
            </div>
        </div>
    </div>
</div>
