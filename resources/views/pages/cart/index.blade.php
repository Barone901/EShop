<x-layouts.default-layout title="Cart">

    <div>

        {{-- Page Header --}}
        <x-ui.page-header title="Shopping Cart" subtitle="Review your items and proceed to checkout." />

        {{-- Cart Content (rendered via JS) --}}
        <div id="cart-page-content">
            <x-ui.loading text="Loading cart..." />
        </div>
    </div>

</x-layouts.default-layout>

