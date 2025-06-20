<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TenantSettingsForm extends Component
{
    use WithFileUploads;

    // Step Management
    public $currentStep = 1;
    public $totalSteps = 3;

    // Model Properties
    public Tenant $tenant;
    public $settings;

    // Step 1: Basic Info
    public $website_name = '';

    public $vat_number = '';

    public $vat_percentage = 0;

    public $website_logo;

    public $website_description = '';

    public $logoPreview;

    // Step 2: Address
    public $address_line_1 = '';

    public $address_line_2 = '';

    public $city = '';

    public $state = '';

    public $postal_code = '';

    public $country = '';

    // Step 3: Stripe (Optional)
    public $stripe_publishable_key = '';

    public $stripe_secret_key = '';

    public function mount()
    {
        $this->authorize('update', tenant());
        $this->tenant = tenant();
        $this->fillFromDatabase();
    }

    protected function fillFromDatabase()
    {
        // vul de basisgegevens in
        $this->website_name = $this->tenant->website_name;
        $this->vat_number = $this->tenant->vat_number;
        $this->vat_percentage = $this->tenant->vat_percentage ?? 0;
        $this->website_description = $this->tenant->website_description;
        if ($this->tenant->website_logo) {
            $this->logoPreview = Storage::disk('public')->url($this->tenant->website_logo);
        }

        // vull adresgegevens
        $address = $this->tenant->address;
        if ($address) {
            $this->address_line_1 = $address->address_line_1;
            $this->address_line_2 = $address->address_line_2;
            $this->city = $address->city;
            $this->state = $address->state;
            $this->postal_code = $address->postal_code;
            $this->country = $address->country;
        }

        // vull betalingsgegevens
        $this->stripe_publishable_key = $this->tenant->stripe_publishable_key;
        $this->stripe_secret_key = $this->tenant->stripe_secret_key;
    }

    #[Computed]
    public function isCurrentStepOptional(): bool
    {
        return $this->currentStep >= 3;
    }

    #[Computed]
    public function isLastStep(): bool
    {
        return $this->currentStep === $this->totalSteps;
    }

    public function updatedWebsiteLogo()
    {
        $this->validate([
            'website_logo' => $this->tenant->website_logo ? 'nullable|image|max:1024|mimes:jpg,jpeg,png' : 'required|image|max:1024|mimes:jpg,jpeg,png',
        ]);

        $this->logoPreview = $this->website_logo->temporaryUrl();
    }

    public function saveCurrentStep()
    {
        $this->authorize('update', tenant());
        if (!$this->isCurrentStepOptional()) {
            $this->validateStep($this->currentStep);
        }

        try {
            DB::beginTransaction();

            match ($this->currentStep) {
                1 => $this->saveBasicInfo(),
                2 => $this->saveAddress(),
                3 => $this->saveStripeSettings(),
                default => null,
            };

            DB::commit();

            $this->dispatch('settings-saved');
            session()->flash('message', 'Settings saved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while saving settings. Please try again.');
        }
    }

    public function nextStep()
    {
        $this->authorize('update', tenant());
        if ($this->currentStep < $this->totalSteps) {
            if (!$this->isCurrentStepOptional()) {
                $this->validateStep($this->currentStep);
            }

            // Save current step before moving to next
            $this->saveCurrentStep();

            $this->currentStep++;
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    public function skipStep()
    {
        if ($this->isCurrentStepOptional()) {
            if($this->isLastStep()) {
                return $this->saveStep();
            }

            $this->currentStep++;
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    protected function validateStep($step)
    {
        if ($this->isCurrentStepOptional()) {
            return true;
        }

        $rules = match ($step) {
            1 => [
                'website_name' => 'required|min:3',
                'vat_number' => 'required|nullable|string|max:20',
                'vat_percentage' => 'required|numeric|min:0|max:100',
                'website_logo' => $this->tenant->website_logo ? 'nullable|image|max:1024|mimes:jpg,jpeg,png' : 'required|image|max:1024|mimes:jpg,jpeg,png',
                'website_description' => 'nullable',
            ],
            2 => [
                'address_line_1' => 'required',
                'city' => 'required',
                'state' => 'nullable',
                'postal_code' => 'required',
                'country' => 'required',
            ],
            default => [],
        };

        $this->validate($rules);
    }

    protected function saveBasicInfo()
    {
        $this->authorize('update', tenant());
        $logoPath = $this->tenant->website_logo;

        if ($this->website_logo) {
            // Delete old logo if exists
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            // Store new logo
            $logoPath = $this->website_logo->store('tenant-logos', 'public');
        }

        $this->tenant->update([
            'website_name' => $this->website_name,
            'vat_number' => $this->vat_number,
            'vat_percentage' => $this->vat_percentage,
            'website_logo' => $logoPath,
            'website_description' => $this->website_description,
        ]);
    }

    protected function saveAddress()
    {
        $this->authorize('update', tenant());
        $this->tenant->address()->updateOrCreate(
            ['tenant_id' => $this->tenant->id],
            [
                'address_line_1' => $this->address_line_1,
                'address_line_2' => $this->address_line_2,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
            ]
        );
    }

    protected function saveStripeSettings()
    {
        $this->authorize('update', tenant());
        if ($this->stripe_publishable_key || $this->stripe_secret_key) {
            $this->tenant->update([
                'stripe_publishable_key' => $this->stripe_publishable_key,
                'stripe_secret_key' => $this->stripe_secret_key,
            ]);
        }
    }

    public function saveStep()
    {
        if (!$this->isLastStep()) {
            return;
        }

        if (!$this->isCurrentStepOptional()) {
            $this->validateStep($this->currentStep);
        }

        try {
            DB::beginTransaction();

            // Save all steps
            $this->saveBasicInfo();
            $this->saveAddress();
            $this->saveStripeSettings();

            // check if the required fields are saved in the database
            if (!$this->tenant->website_name || !$this->tenant->website_logo || !$this->tenant->address) {
                throw new \Exception('Please fill in all required fields.');
            }
            // Mark setup as completed
            $this->tenant->update([
                'is_setup_completed' => true
            ]);

            DB::commit();

            $this->dispatch('settings-saved');
            session()->flash('message', 'Setup completed successfully.');

            return $this->redirect(route('admin.dashboard'), true);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while saving settings. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.admin.tenant-settings-form');
    }
}