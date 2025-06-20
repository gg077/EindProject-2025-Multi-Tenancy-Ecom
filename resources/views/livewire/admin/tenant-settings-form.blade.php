<div class="">
    <div class="mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <!-- Progress Steps -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex justify-center space-x-8 px-6" aria-label="Progress">
                    @foreach(['Basic Information', 'Address', 'Stripe Setup'] as $index => $step)
                        <button
                            wire:click="$set('currentStep', {{ $index + 1 }})"
                            @if($currentStep !== ($index + 1) && !tenant('is_setup_completed')) disabled @endif
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $currentStep === ($index + 1) ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }} disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ $step }}
                        </button>
                    @endforeach
                </nav>
            </div>

            <!-- Step Content -->
            <div class="p-6">
                @if (session()->has('message'))
                    <div class="mt-4 p-4 rounded-md bg-green-50 dark:bg-green-900">
                        <p class="text-sm text-green-700 dark:text-green-200">{{ session('message') }}</p>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mt-4 p-4 rounded-md bg-red-50 dark:bg-red-900">
                        <p class="text-sm text-red-700 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                @endif
                <form wire:submit="saveStep">
                    <!-- Step 1: Basic Information -->
                    @if($currentStep === 1)
                        <div class="space-y-6">
                            <div>
                                <x-label>Website Logo</x-label>
                                <div class="mt-2 flex items-center gap-x-3">
                                    @if($logoPreview)
                                        <img src="{{ $logoPreview }}" alt="Logo Preview" class="h-20 w-auto object-contain dark:border dark:border-gray-700 rounded">
                                    @endif
                                    <x-input type="file" wire:model="website_logo" accept="image/*" />
                                </div>
                                @error('website_logo')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-label>Website Name</x-label>
                                <x-input type="text" wire:model="website_name" class="w-full" />
                                @error('website_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-label>BTW nr (VAT Number)</x-label>
                                <x-input type="text" wire:model="vat_number" class="w-full" />
                                @error('vat_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <x-label>VAT On Products (%)</x-label>
                                <x-input type="number" wire:model="vat_percentage" class="w-full" min="0" max="100" step="0.01" />
                                @error('vat_percentage')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-label>Website Description</x-label>
                                <x-textarea wire:model="website_description" rows="3" class="w-full" />
                                @error('website_description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Step 2: Address -->
                    @if($currentStep === 2)
                        <div class="space-y-6">
                            <div>
                                <x-label>Address Line 1</x-label>
                                <x-input type="text" wire:model="address_line_1" class="w-full" />
                                @error('address_line_1')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-label>Address Line 2</x-label>
                                <x-input type="text" wire:model="address_line_2" class="w-full" />
                                @error('address_line_2')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-label>City</x-label>
                                    <x-input type="text" wire:model="city" class="w-full" />
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <x-label>State</x-label>
                                    <x-input type="text" wire:model="state" class="w-full" />
                                    @error('state')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-label>Postal Code</x-label>
                                    <x-input type="text" wire:model="postal_code" class="w-full" />
                                    @error('postal_code')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <x-label>Country</x-label>
                                    <x-input type="text" wire:model="country" class="w-full" />
                                    @error('country')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Step 3: Stripe Settings -->
                    @if($currentStep === 3)
                        <div class="space-y-6">
                            <div>
                                <x-label>Stripe Publishable Key</x-label>
                                <x-input type="text" wire:model="stripe_publishable_key" class="w-full" />
                                @error('stripe_publishable_key')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-label>Stripe Secret Key</x-label>
                                <x-input type="password" wire:model="stripe_secret_key" class="w-full" />
                                @error('stripe_secret_key')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                This step is optional. You can skip it if you don't want to use Stripe.
                            </p>
                        </div>
                    @endif

                    <!-- Navigation Buttons -->
                    <div class="mt-6 flex justify-between">
                        @if($currentStep > 1)
                            <button
                                type="button"
                                wire:click="previousStep"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Previous
                            </button>
                        @else
                            <div></div>
                        @endif

                        <div class="flex space-x-3">
                            @if($this->isCurrentStepOptional())
                                <button
                                    type="button"
                                    wire:click="skipStep"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    Skip
                                </button>
                            @endif

                            @if(!$this->isLastStep())
                                <button
                                    type="button"
                                    wire:click="saveCurrentStep"
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"">
                                    <span wire:loading.remove wire:target="saveCurrentStep">Save</span>
                                    <span wire:loading wire:target="saveCurrentStep">Saving...</span>
                                </button>
                            @endif

                            @if($this->isLastStep())
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove>Save All</span>
                                    <span wire:loading>Saving...</span>
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="nextStep"
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="nextStep">Next</span>
                                    <span wire:loading wire:target="nextStep">Processing...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Handle step changes
    $wire.on('step-changed', ({ step }) => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>
@endscript
