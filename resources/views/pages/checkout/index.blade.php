<x-layouts.default-layout title="Checkout">

    <div>

        {{-- Page Header --}}
        <x-ui.page-header title="Checkout" subtitle="Fill in your shipping and billing details to complete your order." />

        {{-- Guest Info Banner --}}
        @if($isGuest)
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>You are checking out as a guest. <a href="{{ route('login') }}" class="font-semibold underline">Log in</a> or
                        <a href="{{ route('register') }}" class="font-semibold underline">create an account</a> to save your addresses and track orders.</span>
                </div>
            </div>
        @endif

        {{-- Flash Messages --}}
        <x-ui.flash-messages :types="['error']" />

        <form action="{{ route('orders.store') }}" method="POST" id="checkout-form"
              data-default-country="{{ \App\Models\OrderAddress::DEFAULT_COUNTRY }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Checkout Form --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- SHIPPING ADDRESS --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-400" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Shipping Address
                        </h2>

                        {{-- Saved Shipping Addresses Selection (only for authenticated users) --}}
                        @if(!$isGuest && $shippingAddresses->isNotEmpty())
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <label for="shipping-address-select" class="block text-sm font-medium text-gray-700 mb-2">Use saved shipping address</label>
                                <select id="shipping-address-select"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white">
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.input name="shipping_name" label="Full Name" required placeholder="John Doe" maxlength="255"
                                          :pattern="$patterns['shipping_name']"
                                          :value="$defaultShippingAddress?->full_name ?? $user?->full_name" />

                            <x-form.input name="shipping_email" type="email" label="Email" required placeholder="john@example.com" maxlength="255"
                                          :value="$defaultShippingAddress?->email ?? $user?->email" />

                            <x-form.input name="shipping_phone" type="tel" label="Phone" required placeholder="+421 XXX XXX XXX" maxlength="20"
                                          :pattern="$patterns['shipping_phone']"
                                          :value="$defaultShippingAddress?->phone" />

                            <x-form.input name="shipping_postal_code" label="Postal Code" required placeholder="XXX XX" maxlength="10"
                                          :pattern="$patterns['shipping_postal_code']"
                                          :value="$defaultShippingAddress?->postal_code" />

                            <x-form.input name="shipping_city" label="City" required placeholder="Bratislava" maxlength="100"
                                          :pattern="$patterns['shipping_city']"
                                          :value="$defaultShippingAddress?->city" />

                            <x-form.input name="shipping_country" label="Country" maxlength="100"
                                          :value="$defaultShippingAddress?->country ?? \App\Models\OrderAddress::DEFAULT_COUNTRY" />

                            <x-form.input name="shipping_address" label="Street Address" required placeholder="Main Street 123" maxlength="255"
                                          class="md:col-span-2" :value="$defaultShippingAddress?->street" />
                        </div>
                    </div>

                    {{-- BILLING ADDRESS --}}
                    <div class="card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-green-600" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Billing Address
                            </h2>
                            <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                                <input type="checkbox" id="same-as-shipping" class="mr-2 h-4 w-4 text-indigo-600 rounded">
                                Same as shipping
                            </label>
                        </div>

                        {{-- Saved Billing Addresses Selection (only for authenticated users) --}}
                        @if(!$isGuest && $billingAddresses->isNotEmpty())
                            <div class="mb-6 pb-6 border-b border-gray-200" id="billing-address-selector">
                                <label for="billing-address-select" class="block text-sm font-medium text-gray-700 mb-2">Use saved billing address</label>
                                <select id="billing-address-select"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white">
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.input name="billing_name" label="Full Name" required placeholder="John Doe" maxlength="255"
                                              :pattern="$patterns['billing_name']"
                                              :value="$defaultBillingAddress?->full_name ?? $user?->full_name" />

                                <x-form.input name="billing_email" type="email" label="Email" required placeholder="john@example.com" maxlength="255"
                                              :value="$defaultBillingAddress?->email ?? $user?->email" />

                                <x-form.input name="billing_phone" type="tel" label="Phone" placeholder="+421 XXX XXX XXX" maxlength="20"
                                              :pattern="$patterns['billing_phone']"
                                              :value="$defaultBillingAddress?->phone" />

                                <x-form.input name="billing_postal_code" label="Postal Code" required placeholder="XXX XX" maxlength="10"
                                              :pattern="$patterns['billing_postal_code']"
                                              :value="$defaultBillingAddress?->postal_code" />

                                <x-form.input name="billing_city" label="City" required placeholder="Bratislava" maxlength="100"
                                              :pattern="$patterns['billing_city']"
                                              :value="$defaultBillingAddress?->city" />

                                <x-form.input name="billing_country" label="Country" maxlength="100"
                                              :value="$defaultBillingAddress?->country ?? \App\Models\OrderAddress::DEFAULT_COUNTRY" />

                                <x-form.input name="billing_address" label="Street Address" required placeholder="Main Street 123" maxlength="255"
                                              class="md:col-span-2" :value="$defaultBillingAddress?->street" />
                            </div>

                            {{-- Company Info (optional) --}}
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-medium text-gray-700 mb-4">Company Information (optional)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-form.input name="billing_company_name" label="Company Name" placeholder="Your Company Ltd." maxlength="255"
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
                            </div>
                        </div>
                    </div>

                    {{-- ORDER NOTES & PAYMENT --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        {{-- Note --}}
                        <x-form.input name="note" label="Order Notes" textarea rows="3" class="mb-6" maxlength="1000"
                                      placeholder="Special requests, delivery instructions..." />

                        {{-- Payment Method --}}
                        <div class="pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
                            <label class="flex items-center p-4 border-2 border-indigo-500 bg-indigo-500/10 rounded-lg cursor-pointer">
                                <input type="radio" name="payment_method" value="cod" checked class="h-5 w-5 text-indigo-600">
                                <div class="ml-4">
                                    <span class="font-medium text-gray-900">Cash on Delivery</span>
                                    <p class="text-sm text-gray-500">Pay when you receive the package (+{{ $cart['formatted_shipping_cost'] }})</p>
                                </div>
                                <span class="ml-auto font-semibold text-indigo-400">{{ $cart['formatted_shipping_cost'] }}</span>
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <div class="mt-8">
                            <button type="submit" id="submit-order-btn"
                                    class="w-full bg-indigo-600 text-white py-4 px-6 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                                <span id="submit-text">Place Order</span>
                                <span id="submit-loading" class="hidden">
                                    <svg class="animate-spin inline-block w-5 h-5 mr-2" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="card bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-32">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Order Summary</h2>

                        {{-- Cart Items --}}
                        <div class="space-y-4 max-h-80 overflow-y-auto">
                            @foreach($cart['items'] as $item)
                                <div class="flex items-center gap-4 pb-4 border-b border-gray-100 last:border-0">
                                    @if($item['image_url'])
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</h4>
                                        <p class="text-sm text-gray-500">{{ $item['quantity'] }}x {{ $item['formatted_price'] }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $item['formatted_subtotal'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Totals --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium text-gray-900">{{ $cart['formatted_total'] }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping (COD)</span>
                                <span class="font-medium text-gray-900">{{ $cart['formatted_shipping_cost'] }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200">
                                <span class="text-gray-900">Total</span>
                                <span class="text-indigo-400">{{ $cart['formatted_grand_total'] }}</span>
                            </div>
                        </div>

                        {{-- Back to cart --}}
                        <a href="{{ route('cart.index') }}" class="mt-6 block text-center text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
                            ← Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

</x-layouts.default-layout>

