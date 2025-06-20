<div class="w-full max-w-md p-6 bg-white text-black rounded shadow">

<form wire:submit.prevent="register" novalidate>
        <h2 class="text-2xl mb-4 text-center text-black">Registreer je eigen subdomein</h2>

        <div class="mb-4">
            <label for="tenant_name" class="text-black">Bedrijfsnaam</label>
            <input wire:model.defer="tenant_name" id="tenant_name" class="w-full border p-2" />
            @error('tenant_name') <p class="text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="tenant_domain" class="text-black">Kies je domein</label>
            <input wire:model.defer="tenant_domain" id="tenant_domain"
                   placeholder="bv. pdfstore"
                   class="w-full border p-2" />
            @error('tenant_domain') <p class="text-red-600">{{ $message }}</p> @enderror
            <small class="text-gray-600 block mt-1">
                 “pdfstore” → pdfstore.digimarket.be
            </small>
        </div>

        <div class="mb-4">
            <label for="name" class="text-black">Jouw naam</label>
            <input wire:model.defer="name" id="name" class="w-full border p-2" />
            @error('name') <p class="text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="text-black">E-mail</label>
            <input wire:model.defer="email" type="email" id="email" class="w-full border p-2" />
            @error('email') <p class="text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="text-black">Wachtwoord</label>
            <input wire:model.defer="password" type="password" id="password" class="w-full border p-2" />
            @error('password') <p class="text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="text-black">Bevestig wachtwoord</label>
            <input wire:model.defer="password_confirmation"
                   type="password" id="password_confirmation"
                   class="w-full border p-2" />
        </div>

        <button type="submit" class="w-full px-4 py-2 font-bold bg-gray-400 hover:bg-gray-500 text-white rounded">
            Registreer en maak tenant
        </button>
    </form>
</div>
