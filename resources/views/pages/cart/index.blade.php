{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Kosik. Obsah #cart-page-content renderuje server (x-shop.cart-page), cart.js ho po nacitani
    stiahne cez /api/cart a vlozi - ID nemen.
--}}
<x-layouts.default-layout :title="__('Cart')">

    <x-shop.checkout-steps :current="1" />

    <x-ui.page-header :title="__('Shopping Cart')" :subtitle="__('Review your items and proceed to checkout.')" />

    {{-- Obsah kosika (HTML zo servera, vklada cart.js) --}}
    <div id="cart-page-content">
        <x-ui.loading :text="__('Loading cart...')" />
    </div>

</x-layouts.default-layout>
