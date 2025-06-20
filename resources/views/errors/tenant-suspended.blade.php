<x-layouts.auth.simple>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-red-600 mb-4">Tenant Suspended</h2>
                <p class="text-gray-600 mb-4">
                    @if($tenant)
                        The account for <strong>{{ $tenant->website_name }}</strong> has been suspended.
                    @else
                        This tenant account has been suspended.
                    @endif
                </p>
                <p class="text-gray-600 mb-6">
                    Please contact support for assistance in reactivating your account. Or try later.
                </p>
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Return to Home
                </a>
            </div>
        </div>
    </div>
</x-layouts.auth.simple>
