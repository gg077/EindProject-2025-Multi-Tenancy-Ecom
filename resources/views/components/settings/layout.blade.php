<div class="flex items-start max-md:flex-col">
    <div class="mr-10 w-full pb-4 md:w-[220px]">
        @if(auth()->user()->isAdmin() && auth()->user()->tenant_id)
            <flux:navlist>
                <flux:navlist.item :href="route('admin.settings.profile')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
                <flux:navlist.item :href="route('admin.settings.password')" wire:navigate>{{ __('Password') }}</flux:navlist.item>
                <flux:navlist.item :href="route('admin.settings.appearance')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
            </flux:navlist>
        @else
            <flux:navlist>
                <flux:navlist.item :href="route('settings.profile')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
                <flux:navlist.item :href="route('settings.password')" wire:navigate>{{ __('Password') }}</flux:navlist.item>
                <flux:navlist.item :href="route('settings.appearance')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
            </flux:navlist>
        @endif
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
