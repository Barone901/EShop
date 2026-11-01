{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{--
    Kosik. Obsah #cart-page-content vykresluje cart.js (renderCartPage) - ID nemen.
--}}
<x-layouts.default-layout title="Cart">

    <x-shop.checkout-steps :current="1" />

    <x-ui.page-header title="Shopping Cart" subtitle="Review your items and proceed to checkout." />

    {{-- Obsah kosika (vykresluje JS) --}}
    <div id="cart-page-content">
        <x-ui.loading text="Loading cart..." />
    </div>

</x-layouts.default-layout>
