@if (tenant('id') && config('WEBSITE_LOGO'))
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md">
        <img src="{{ Storage::disk('public')->url(config('WEBSITE_LOGO')) }}" alt="Logo"
             class="h-8 w-8 rounded-full" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-none font-semibold">{{ config('WEBSITE_NAME') }}</span>
    </div>
@else
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
        <img src="{{ asset('web/img/Digimarket-logo2.png') }}" alt="Logo" class="h-8 w-auto">
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-none font-semibold">{{config('app.name')}}</span>
    </div>
@endif
