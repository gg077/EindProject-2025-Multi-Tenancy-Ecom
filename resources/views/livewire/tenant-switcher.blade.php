<div>
        <!-- Toon alleen als er tenants zijn -->
    @if($this->tenants->count() > 0)
        <flux:menu.submenu heading="{{ __('Switch Tenant') }}" icon="building-office">
            @foreach($this->tenants as $tenant)
                <flux:menu.item wire:confirm="Are you sure you want to switch to: {{ $tenant->website_name }}" wire:click="switchTenant('{{ $tenant->id }}')"> {{ $tenant->website_name }}</flux:menu.item>
            @endforeach
        </flux:menu.submenu>

        <flux:menu.separator />
    @endif
</div>
