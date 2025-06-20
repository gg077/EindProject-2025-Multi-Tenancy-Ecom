@if(!auth()->user()->tenant_id)
    <x-layouts.app.super-admin-sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.super-admin-sidebar>
@elseif(auth()->user()->isAdmin())
    <x-layouts.app.admin-sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.admin-sidebar>
@else
    <x-layouts.app.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.sidebar>
@endif
