{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Spoločné polia formulára adresy (create + edit).
    Premenné: $type (App\Enums\AddressType), $values (predvolené hodnoty), $submitLabel,
    $patterns (vzory polí z UserAddressRequest::PATTERNS)
--}}
<fieldset>
    <legend class="sr-only">{{ __('Address') }}</legend>
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <x-form.input name="first_name" :label="__('First Name')" required maxlength="100" autocomplete="given-name" :pattern="$patterns['first_name']" :value="$values['first_name'] ?? null" />

        <x-form.input name="last_name" :label="__('Last Name')" required maxlength="100" autocomplete="family-name" :pattern="$patterns['last_name']" :value="$values['last_name'] ?? null" />

        <x-form.input name="phone" type="tel" :label="__('Phone')" placeholder="+1 XXX XXX XXXX" maxlength="20" autocomplete="tel" :pattern="$patterns['phone']" :value="$values['phone'] ?? null" />

        <x-form.input name="email" type="email" :label="__('Email')" maxlength="255" autocomplete="email" :value="$values['email'] ?? null" />

        <x-form.input name="street" :label="__('Street Address')" required maxlength="255" autocomplete="street-address" class="md:col-span-2" :value="$values['street'] ?? null" />

        <x-form.input name="city" :label="__('City')" required maxlength="100" autocomplete="address-level2" :pattern="$patterns['city']" :value="$values['city'] ?? null" />

        <x-form.input name="postal_code" :label="__('Postal Code')" required placeholder="XXX XX" maxlength="10" autocomplete="postal-code" :pattern="$patterns['postal_code']" :value="$values['postal_code'] ?? null" />

        <x-form.input name="country" :label="__('Country')" maxlength="100" autocomplete="country-name" class="md:col-span-2" :value="$values['country'] ?? null" />
    </div>
</fieldset>

@if($type === \App\Enums\AddressType::Billing)
    {{-- Firemne udaje --}}
    <fieldset class="mt-8 border-t border-line pt-6">
        <legend class="sr-only">{{ __('Company Details') }}</legend>
        <p class="mb-4 flex items-center gap-2">
            <span class="font-display text-lg font-semibold uppercase tracking-wide">{{ __('Company Details') }}</span>
            <x-ui.badge>{{ __('Optional') }}</x-ui.badge>
        </p>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-form.input name="company_name" :label="__('Company Name')" maxlength="255" autocomplete="organization" class="md:col-span-2" :value="$values['company_name'] ?? null" />

            <x-form.input name="ico" :label="__('Company ID')" maxlength="20" :pattern="$patterns['ico']" :value="$values['ico'] ?? null" />

            <x-form.input name="dic" :label="__('Tax ID')" maxlength="20" :pattern="$patterns['dic']" :value="$values['dic'] ?? null" />

            <x-form.input name="ic_dph" :label="__('VAT ID')" maxlength="20" class="md:col-span-2" :pattern="$patterns['ic_dph']" :value="$values['ic_dph'] ?? null" />
        </div>
    </fieldset>
@endif

{{-- Predvolena adresa --}}
<label class="mt-6 inline-flex min-h-11 cursor-pointer items-center gap-3 rounded-xs border border-line px-3 hover:border-line-strong">
    <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $values['is_default'] ?? false)) class="size-5">
    <span class="text-sm">{{ __('Set as default address') }}</span>
</label>

{{-- Tlacidla --}}
<div class="mt-8 flex flex-col gap-3 border-t border-line pt-6 sm:flex-row sm:items-center">
    <x-button size="lg">{{ $submitLabel }}</x-button>
    <x-button :href="route('addresses.index')" variant="secondary" size="lg">{{ __('Cancel') }}</x-button>
</div>
