<div>
    <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-500 dark:text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5V6a3 3 0 013-3h1.5M21 16.5v1.5a3 3 0 01-3 3h-1.5M3 16.5v1.5a3 3 0 003 3h1.5m12-18H18a3 3 0 00-3 3v1.5M21 7.5V6a3 3 0 00-3-3h-1.5M12 9v6m3-3H9" />
                    </svg>
                    <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">{{ __('Categorieën') }}</h1>
                </div>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ __('Beheer alle categorieën voor je posts.') }}
                </p>
            </div>
            <!-- Controleer via policy of de gebruiker nieuwe categorieën mag aanmaken -->
            @can('create', App\Models\Category::class)
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <a wire:navigate href="{{ route('admin.categories.create') }}" class="bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition group inline-flex items-center gap-x-2 px-3.5 py-2.5 text-sm shadow-sm dark:bg-indigo-700 dark:hover:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 -ml-0.5 group-hover:scale-110 transition-transform">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ __('Nieuwe Categorie') }}
                    </a>
                </div>
            @endcan
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle">
                    <div class="overflow-hidden shadow-sm sm:rounded-lg bg-white dark:bg-gray-800">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">{{ __('Naam') }}</th>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">{{ __('Products') }}</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">{{ __('Acties') }}</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
{{--                            Loop door alle categorieën. Als lijst leeg is, springt naar @empty--}}
                            @forelse($categories as $category)
{{--                                Rij in de tabel voor één categorie met lichte hover-effecten--}}
                                <tr wire:key="{{ $category->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition duration-150">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                            </svg>
                                            {{ $category->name }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
{{--                                        Aantal producten dat bij deze categorie hoort--}}
                                        {{ $category->products_count }}
                                    </td>

                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex justify-end gap-2">
{{--                                            Mag de gebruiker deze categorie bewerken?--}}
                                            @can('update', $category)
                                                <a wire:navigate href="{{ route('admin.categories.edit', $category) }}"
                                                   class="p-1 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                                   title="{{ __('Bewerken') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                                    </svg>
                                                </a>
                                            @endcan
{{--                                            Alleen tonen als de categorie leeg is én gebruiker mag verwijderen--}}
                                        @if($category->products_count === 0 && auth()->user()->can('delete', $category))
                                                <button wire:click="delete({{ $category->id }})"
                                                        wire:confirm="Weet u zeker dat u deze categorie wilt verwijderen?"
                                                        class="p-1 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                                        title="{{ __('Verwijderen') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
{{--Als er geen categorieën zijn in de lijst--}}
                            @empty
                                <tr>
                                    <td colspan="2" class="px-3 py-8 text-sm text-gray-500 dark:text-gray-300 text-center">
                                        {{ __('Geen categorieën gevonden') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
{{--vanaf 11 categories word dit getoont--}}
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>
</div>
