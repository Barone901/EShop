{{--
    Spoločné polia formulára adresy (create + edit).
    Premenné: $type ('shipping'|'billing'), $values (predvolené hodnoty), $submitLabel,
    $patterns (vzory polí z UserAddressRequest::PATTERNS)
--}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-form.input name="first_name" label="First Name" required maxlength="100" :pattern="$patterns['first_name']" :value="$values['first_name'] ?? null" />

    <x-form.input name="last_name" label="Last Name" required maxlength="100" :pattern="$patterns['last_name']" :value="$values['last_name'] ?? null" />

    <x-form.input name="phone" type="tel" label="Phone" placeholder="+1 XXX XXX XXXX" maxlength="20" :pattern="$patterns['phone']" :value="$values['phone'] ?? null" />

    <x-form.input name="email" type="email" label="Email" maxlength="255" :value="$values['email'] ?? null" />

    <x-form.input name="street" label="Street Address" required maxlength="255" class="md:col-span-2" :value="$values['street'] ?? null" />

    <x-form.input name="city" label="City" required maxlength="100" :pattern="$patterns['city']" :value="$values['city'] ?? null" />

    <x-form.input name="postal_code" label="Postal Code" required placeholder="XXX XX" maxlength="10" :pattern="$patterns['postal_code']" :value="$values['postal_code'] ?? null" />

    <x-form.input name="country" label="Country" maxlength="100" class="md:col-span-2" :value="$values['country'] ?? null" />
</div>

@if($type === 'billing')
    {{-- Company Details --}}
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Details (Optional)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form.input name="company_name" label="Company Name" maxlength="255" class="md:col-span-2" :value="$values['company_name'] ?? null" />

            <x-form.input name="ico" label="Company ID" maxlength="20" :pattern="$patterns['ico']" :value="$values['ico'] ?? null" />

            <x-form.input name="dic" label="Tax ID" maxlength="20" :pattern="$patterns['dic']" :value="$values['dic'] ?? null" />

            <x-form.input name="ic_dph" label="VAT ID" maxlength="20" class="md:col-span-2" :pattern="$patterns['ic_dph']" :value="$values['ic_dph'] ?? null" />
        </div>
    </div>
@endif

{{-- Default Address --}}
<div class="mt-6">
    <label class="flex items-center">
        <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $values['is_default'] ?? false))
               class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
        <span class="ml-3 text-gray-700">Set as default address</span>
    </label>
</div>

{{-- Buttons --}}
<div class="mt-8 flex items-center gap-4">
    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('addresses.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
        Cancel
    </a>
</div>
