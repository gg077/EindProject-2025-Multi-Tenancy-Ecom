<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        @if (session()->has('message'))
            <x-ui.flash-message
                :message="session('message')"
                :type="session('message_type', 'success')"
            />
        @endif

        <div class="md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Nieuwe Rol') }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Maak een nieuwe rol aan voor het systeem.') }}
                    </p>
                </div>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <form wire:submit="save">
                    <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Naam') }}</label>
                                <input type="text"
                                    wire:model="name"
                                    id="name"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                <x-ui.forms.error error="name" />
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Icoon') }}</label>
                                <div class="relative">
                                    <button
                                        type="button"
                                        wire:click="toggleIconPicker"
                                        class="w-full flex items-center gap-2 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-left focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        @if($selectedIcon)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 {{ $this->getIconColorClass() }}">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $selectedIcon['path'] }}" />
                                            </svg>
                                            <span class="text-gray-900 dark:text-white">{{ Str::title(str_replace('-', ' ', $selectedIcon['name'])) }}</span>
                                        @else
                                            <span class="text-gray-500">{{ __('Selecteer een icoon') }}</span>
                                        @endif
                                        <svg class="ml-auto h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    @if($showIconPicker)
                                        <div class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 rounded-md shadow-lg">
                                            <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                                <input
                                                    type="text"
                                                    wire:model.live="search"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white"
                                                    placeholder="{{ __('Zoek een icoon...') }}"
                                                >
                                            </div>
                                            <div class="max-h-60 overflow-y-auto p-2">
                                                <div class="grid grid-cols-6 gap-2">
                                                    @foreach($this->availableIcons as $name => $icon)
                                                        <button
                                                            type="button"
                                                            wire:key="icon-{{ $name }}"
                                                            wire:click="selectIcon('{{ $name }}', '{{ $icon['path'] }}')"
                                                            class="flex flex-col items-center justify-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 {{ $icon === $selectedIcon ? 'bg-indigo-50 dark:bg-indigo-900/20 ring-2 ring-indigo-500' : '' }}"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 {{ $this->getIconColorClass() }}">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon['path'] }}" />
                                                            </svg>
                                                            <span class="mt-1 text-xs text-gray-600 dark:text-gray-400 truncate w-full text-center">
                                                                {{ Str::title(str_replace('-', ' ', $name)) }}
                                                            </span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <x-ui.forms.error error="icon" />
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Role Permissions') }}
                                </label>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    {{ __('Module') }}
                                                </th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    {{ __('Permissions') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($modules as $module)
                                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition duration-150">
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ $module['module'] }}
                                                    </td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        @foreach($module['permissions'] as $permission)
                                                            <label class="inline-flex items-center mr-3">
                                                                <input
                                                                    type="checkbox"
                                                                    wire:model="selectedPermissions"
                                                                    value="{{ $permission['id'] }}"
                                                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                                >
                                                                <span class="ml-2">{{ explode(' ', $permission['name'])[0] }}</span>
                                                            </label>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-800 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition dark:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Opslaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
