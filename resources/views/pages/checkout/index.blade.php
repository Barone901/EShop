{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Pokladna. JS vazby (checkout.js + form-validator.js) - nemen ID, name ani data-* atributy:
    #checkout-form[data-validate][data-default-country], #shipping-address-select, #billing-address-select
    (option data-name/email/phone/address/city/postal/country/company/ico/dic/icdph), #same-as-shipping,
    #billing-address-selector (JS prepina "hidden"), #billing-fields, polia shipping_* / billing_* (id = name),
    #submit-order-btn, #submit-text, #submit-loading (JS prepina "hidden").
--}}
@php
    // Hlavicka sekcie formulara: cislo kroku + nadpis
    $sectionTitle = 'flex items-center gap-3 text-xl md:text-2xl';
    $stepNumber = 'num grid size-8 shrink-0 place-items-center rounded-xs bg-ink-900 text-sm text-white';
    $selectClasses = 'block min-h-11 w-full py-2.5 pl-3 pr-10 text-base';
@endphp

<x-layouts.default-layout title="Checkout">

    <x-shop.checkout-steps :current="2" />

    <x-ui.page-header title="Checkout" subtitle="Fill in your shipping and billing details to complete your order." />

    {{-- Info pre hosta --}}
    @if($isGuest)
        <div class="mb-6 flex gap-3 border-l-3 border-brand-600 bg-white px-4 py-3 text-sm">
            <svg class="mt-0.5 size-5 shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="square" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>
                You are checking out as a guest.
                <a href="{{ route('login') }}" class="font-semibold text-brand-600 underline underline-offset-2 hover:text-brand-700">Log in</a> or
                <a href="{{ route('register') }}" class="font-semibold text-brand-600 underline underline-offset-2 hover:text-brand-700">create an account</a>
                to save your addresses and track orders.
            </p>
        </div>
    @endif

    <x-ui.flash-messages :types="['error']" />

    <form action="{{ route('orders.store') }}" method="POST" id="checkout-form" data-validate
          data-default-country="{{ config('shop.default_country') }}">
        @csrf

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8">
            <div class="space-y-6 lg:col-span-2">

                {{-- 1. DORUCOVACIA ADRESA --}}
                <x-ui.card padding="lg">
                    <h2 class="{{ $sectionTitle }} mb-6">
                        <span class="{{ $stepNumber }}" aria-hidden="true">1</span>
                        Shipping Address
                    </h2>

                    {{-- Ulozene dorucovacie adresy (len prihlaseny) --}}
                    @if(!$isGuest && $shippingAddresses->isNotEmpty())
                        <div class="mb-6 border-b border-line pb-6">
                            <label for="shipping-address-select" class="mb-1.5 block text-sm font-medium">Use saved shipping address</label>
                            <select id="shipping-address-select" class="{{ $selectClasses }}">
                                <option value="">-- Enter new address --</option>
                                @foreach($shippingAddresses as $address)
                                    <option value="{{ $address->id }}"
                                            data-name="{{ $address->full_name }}"
                                            data-email="{{ $address->email }}"
                                            data-phone="{{ $address->phone }}"
                                            data-address="{{ $address->street }}"
                                            data-city="{{ $address->city }}"
                                            data-postal="{{ $address->postal_code }}"
                                            data-country="{{ $address->country }}"
                                            {{ $address->is_default ? 'selected' : '' }}>
                                        {{ $address->full_name }} - {{ $address->street }}, {{ $address->city }}
                                        @if($address->is_default) (Default) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-form.input name="shipping_name" label="Full Name" required placeholder="John Doe" maxlength="255"
                                      autocomplete="shipping name"
                                      :pattern="$patterns['shipping_name']"
                                      :value="$defaultShippingAddress?->full_name ?? $user?->full_name" />

                        <x-form.input name="shipping_email" type="email" label="Email" required placeholder="john@example.com" maxlength="255"
                                      autocomplete="email"
                                      :value="$defaultShippingAddress?->email ?? $user?->email" />

                        <x-form.input name="shipping_phone" type="tel" label="Phone" required placeholder="+421 XXX XXX XXX" maxlength="20"
                                      autocomplete="shipping tel"
                                      :pattern="$patterns['shipping_phone']"
                                      :value="$defaultShippingAddress?->phone" />

                        <x-form.input name="shipping_postal_code" label="Postal Code" required placeholder="XXX XX" maxlength="10"
                                      autocomplete="shipping postal-code"
                                      :pattern="$patterns['shipping_postal_code']"
                                      :value="$defaultShippingAddress?->postal_code" />

                        <x-form.input name="shipping_city" label="City" required placeholder="Bratislava" maxlength="100"
                                      autocomplete="shipping address-level2"
                                      :pattern="$patterns['shipping_city']"
                                      :value="$defaultShippingAddress?->city" />

                        <x-form.input name="shipping_country" label="Country" maxlength="100"
                                      autocomplete="shipping country-name"
                                      :value="$defaultShippingAddress?->country ?? config('shop.default_country')" />

                        <x-form.input name="shipping_address" label="Street Address" required placeholder="Main Street 123" maxlength="255"
                                      autocomplete="shipping street-address"
                                      class="md:col-span-2" :value="$defaultShippingAddress?->street" />
                    </div>
                </x-ui.card>

                {{-- 2. FAKTURACNA ADRESA --}}
                <x-ui.card padding="lg">
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="{{ $sectionTitle }}">
                            <span class="{{ $stepNumber }}" aria-hidden="true">2</span>
                            Billing Address
                        </h2>
                        <label for="same-as-shipping" class="inline-flex min-h-11 cursor-pointer items-center gap-2.5 rounded-xs border border-line px-3 text-sm hover:border-line-strong">
                            <input type="checkbox" id="same-as-shipping" class="size-5">
                            Same as shipping
                        </label>
                    </div>

                    {{-- Ulozene fakturacne adresy (len prihlaseny) --}}
                    @if(!$isGuest && $billingAddresses->isNotEmpty())
                        <div class="mb-6 border-b border-line pb-6" id="billing-address-selector">
                            <label for="billing-address-select" class="mb-1.5 block text-sm font-medium">Use saved billing address</label>
                            <select id="billing-address-select" class="{{ $selectClasses }}">
                                <option value="">-- Enter new address --</option>
                                @foreach($billingAddresses as $address)
                                    <option value="{{ $address->id }}"
                                            data-name="{{ $address->full_name }}"
                                            data-email="{{ $address->email }}"
                                            data-phone="{{ $address->phone }}"
                                            data-address="{{ $address->street }}"
                                            data-city="{{ $address->city }}"
                                            data-postal="{{ $address->postal_code }}"
                                            data-country="{{ $address->country }}"
                                            data-company="{{ $address->company_name }}"
                                            data-ico="{{ $address->ico }}"
                                            data-dic="{{ $address->dic }}"
                                            data-icdph="{{ $address->ic_dph }}"
                                            {{ $address->is_default ? 'selected' : '' }}>
                                        {{ $address->full_name }}@if($address->company_name) ({{ $address->company_name }})@endif - {{ $address->street }}, {{ $address->city }}
                                        @if($address->is_default) (Default) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div id="billing-fields">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <x-form.input name="billing_name" label="Full Name" required placeholder="John Doe" maxlength="255"
                                          autocomplete="billing name"
                                          :pattern="$patterns['billing_name']"
                                          :value="$defaultBillingAddress?->full_name ?? $user?->full_name" />

                            <x-form.input name="billing_email" type="email" label="Email" required placeholder="john@example.com" maxlength="255"
                                          autocomplete="email"
                                          :value="$defaultBillingAddress?->email ?? $user?->email" />

                            <x-form.input name="billing_phone" type="tel" label="Phone" placeholder="+421 XXX XXX XXX" maxlength="20"
                                          autocomplete="billing tel"
                                          :pattern="$patterns['billing_phone']"
                                          :value="$defaultBillingAddress?->phone" />

                            <x-form.input name="billing_postal_code" label="Postal Code" required placeholder="XXX XX" maxlength="10"
                                          autocomplete="billing postal-code"
                                          :pattern="$patterns['billing_postal_code']"
                                          :value="$defaultBillingAddress?->postal_code" />

                            <x-form.input name="billing_city" label="City" required placeholder="Bratislava" maxlength="100"
                                          autocomplete="billing address-level2"
                                          :pattern="$patterns['billing_city']"
                                          :value="$defaultBillingAddress?->city" />

                            <x-form.input name="billing_country" label="Country" maxlength="100"
                                          autocomplete="billing country-name"
                                          :value="$defaultBillingAddress?->country ?? config('shop.default_country')" />

                            <x-form.input name="billing_address" label="Street Address" required placeholder="Main Street 123" maxlength="255"
                                          autocomplete="billing street-address"
                                          class="md:col-span-2" :value="$defaultBillingAddress?->street" />
                        </div>

                        {{-- Firemne udaje (volitelne) --}}
                        <fieldset class="mt-6 border-t border-line pt-6">
                            <legend class="sr-only">Company Information</legend>
                            <p class="mb-4 flex items-center gap-2">
                                <span class="font-display text-lg font-semibold uppercase tracking-wide">Company Information</span>
                                <x-ui.badge>Optional</x-ui.badge>
                            </p>
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <x-form.input name="billing_company_name" label="Company Name" placeholder="Your Company Ltd." maxlength="255"
                                              autocomplete="billing organization"
                                              class="md:col-span-2" :value="$defaultBillingAddress?->company_name" />

                                <x-form.input name="billing_ico" label="Company ID (IČO)" placeholder="12345678" maxlength="20"
                                              :pattern="$patterns['billing_ico']"
                                              :value="$defaultBillingAddress?->ico" />

                                <x-form.input name="billing_dic" label="Tax ID (DIČ)" placeholder="1234567890" maxlength="20"
                                              :pattern="$patterns['billing_dic']"
                                              :value="$defaultBillingAddress?->dic" />

                                <x-form.input name="billing_ic_dph" label="VAT ID (IČ DPH)" placeholder="SK1234567890" maxlength="20"
                                              :pattern="$patterns['billing_ic_dph']"
                                              :value="$defaultBillingAddress?->ic_dph" />
                            </div>
                        </fieldset>
                    </div>
                </x-ui.card>

                {{-- 3. POZNAMKA A PLATBA --}}
                <x-ui.card padding="lg">
                    <h2 class="{{ $sectionTitle }} mb-6">
                        <span class="{{ $stepNumber }}" aria-hidden="true">3</span>
                        Payment &amp; Notes
                    </h2>

                    <fieldset>
                        <legend class="mb-3 text-sm font-medium">Payment Method</legend>
                        <label class="flex min-h-11 cursor-pointer items-center gap-4 rounded-xs border-2 border-brand-600 bg-brand-50 p-4">
                            <input type="radio" name="payment_method" value="cod" checked class="size-5">
                            <span class="flex-1">
                                <span class="block font-semibold">Cash on Delivery</span>
                                <span class="block text-sm text-muted">Pay when you receive the package (+{{ $cart['formatted_shipping_cost'] }})</span>
                            </span>
                            <span class="num text-brand-600">{{ $cart['formatted_shipping_cost'] }}</span>
                        </label>
                    </fieldset>

                    <x-form.input name="note" label="Order Notes" textarea rows="3" class="mt-6" maxlength="1000"
                                  placeholder="Special requests, delivery instructions..." />

                    {{-- Odoslanie: celkova suma priamo pri tlacidle --}}
                    <div class="mt-8 border-t border-line pt-6">
                        <div class="mb-4 flex items-baseline justify-between">
                            <span class="font-display text-lg font-semibold uppercase tracking-wide">Total to pay</span>
                            <span class="num text-2xl">{{ $cart['formatted_grand_total'] }}</span>
                        </div>

                        <x-button id="submit-order-btn" size="lg" class="w-full">
                            <span id="submit-text">Place Order</span>
                            <span id="submit-loading" class="hidden">
                                <svg class="mr-2 inline-block size-5 animate-spin" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Processing...
                            </span>
                        </x-button>
                    </div>
                </x-ui.card>
            </div>

            {{-- SUHRN OBJEDNAVKY --}}
            <aside class="lg:col-span-1" aria-labelledby="checkout-summary">
                <div class="card border-t-3 border-t-brand-600 p-6 lg:sticky lg:top-36">
                    <h2 id="checkout-summary" class="mb-4 text-xl">Order Summary</h2>

                    <ul class="max-h-80 divide-y divide-line overflow-y-auto">
                        @foreach($cart['items'] as $item)
                            <li class="flex items-center gap-3 py-3">
                                <div class="size-14 shrink-0 overflow-hidden rounded-xs border border-line bg-tile">
                                    @if($item['image_url'])
                                        <img src="{{ $item['image_url'] }}" alt="" class="h-full w-full object-contain p-1 mix-blend-multiply">
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">{{ $item['name'] }}</p>
                                    <p class="num text-xs text-subtle">{{ $item['quantity'] }}× {{ $item['formatted_price'] }}</p>
                                </div>
                                <span class="num text-sm">{{ $item['formatted_subtotal'] }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <dl class="mt-4 space-y-3 border-t border-line pt-4 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-muted">Subtotal</dt>
                            <dd class="num">{{ $cart['formatted_total'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Shipping (COD)</dt>
                            <dd class="num">{{ $cart['formatted_shipping_cost'] }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between border-t border-line pt-3">
                            <dt class="font-display text-lg font-semibold uppercase tracking-wide">Total</dt>
                            <dd class="num text-2xl">{{ $cart['formatted_grand_total'] }}</dd>
                        </div>
                    </dl>

                    <x-button :href="route('cart.index')" variant="ghost" class="mt-4 w-full">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Cart
                    </x-button>
                </div>
            </aside>
        </div>
    </form>

</x-layouts.default-layout>
