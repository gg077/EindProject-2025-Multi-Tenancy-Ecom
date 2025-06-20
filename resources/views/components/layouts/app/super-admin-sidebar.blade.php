<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="border-r border-transparent bg-gradient-to-b from-indigo-950 to-gray-900 dark:from-zinc-950 dark:to-zinc-900 text-white">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <div class="flex justify-center items-center h-10">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" wire:navigate>
            <img src="{{ asset('web/img/Digimarket-logo2.png') }}" alt="Logo" class="h-8 w-auto">
            <span class="text-lg font-semibold text-white dark:text-white">Digimarket</span>
        </a>
    </div>

    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Platform')" class="grid">
            <flux:navlist.item 
                icon="home" 
                :href="route('dashboard')" 
                :current="request()->routeIs('dashboard')" 
                wire:navigate 
                class="!text-white data-current:text-(--color-accent-content)! my-3!"
                icon-class="!text-white data-current:text-(--color-accent-content)!"
            >
                {{ __('Dashboard') }}
            </flux:navlist.item>

            @can('viewAny', \App\Models\User::class)
                <flux:navlist.item
                    icon="users"
                    :href="route('users.index')"
                    :current="request()->routeIs('users.index')"
                    wire:navigate
                    class="!text-white data-current:text-(--color-accent-content)! my-3!"
                    icon-class="!text-white data-current:text-(--color-accent-content)!"
                >
                    {{ __('Gebruikers') }}
                </flux:navlist.item>
            @endcan

            @can('viewAny', \App\Models\Role::class)
                <flux:navlist.item
                    icon="key"
                    :href="route('roles.index')"
                    :current="request()->routeIs('roles.index')"
                    wire:navigate
                    class="!text-white data-current:text-(--color-accent-content)! my-3!"
                    icon-class="!text-white data-current:text-(--color-accent-content)!"
                >
                    {{ __('Rollen') }}
                </flux:navlist.item>
            @endcan

            @can('viewAny', \App\Models\Tenant::class)
                <flux:navlist.item
                    icon="rectangle-stack"
                    :href="route('tenants.index')"
                    :current="request()->routeIs('tenants.index')"
                    wire:navigate
                    class="!text-white data-current:text-(--color-accent-content)! my-3!"
                    icon-class="!text-white data-current:text-(--color-accent-content)!"
                >
                    {{ __('Tenants') }}
                </flux:navlist.item>
            @endcan
        
        </flux:navlist.group>
    </flux:navlist>

    <flux:spacer />

    <!-- Desktop User Menu -->
    <flux:dropdown position="bottom" align="start">
        <flux:profile
            :name="auth()->user()->name"
            :initials="auth()->user()->initials()"
            icon-trailing="chevrons-up-down"
        />

        <flux:menu class="w-[220px]">
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                        <div class="grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <livewire:tenant-switcher />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<!-- Mobile User Menu -->
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:spacer />

    <flux:dropdown position="top" align="end">
        <flux:profile
            :initials="auth()->user()->initials()"
            icon-trailing="chevron-down"
        />

        <flux:menu>
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                        <div class="grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <livewire:tenant-switcher />

            <flux:menu.separator />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>

{{ $slot }}

@fluxScripts
@livewireChartsScripts
</body>
</html>
