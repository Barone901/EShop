{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{-- Potvrdenie objednavky (krok 3 nakupu). --}}
<x-layouts.default-layout title="Order Confirmed">

    <x-shop.checkout-steps :current="3" />

    <div class="mx-auto max-w-3xl">

        {{-- Uspech --}}
        <div class="card mb-6 flex flex-col items-start gap-5 border-l-3 border-l-ok-700 p-6 sm:flex-row sm:items-center md:p-8" role="status">
            <span class="grid size-14 shrink-0 place-items-center rounded-xs bg-ok-50 text-ok-700" aria-hidden="true">
                <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            <div>
                <h1 class="text-3xl md:text-4xl">Order Received!</h1>
                <p class="mt-2 text-muted">
                    Thank you for your order. A confirmation has been sent to
                    <strong class="font-semibold text-text">{{ $order->shippingAddress?->email }}</strong>.
                </p>
            </div>
        </div>

        {{-- Detail objednavky --}}
        <div class="card overflow-hidden">
            {{-- Hlavicka: cislo a datum (tmavy pruh) --}}
            <div class="theme-dark flex flex-wrap items-center justify-between gap-4 bg-ink-900 px-6 py-4 text-steel-100">
                <div>
                    <p class="eyebrow text-steel-300">Order Number</p>
                    <p class="num mt-1 text-xl text-white">{{ $order->order_number }}</p>
                </div>
                <div class="sm:text-right">
                    <p class="eyebrow text-steel-300">Date</p>
                    <p class="num mt-1 text-white">{{ $order->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            {{-- Polozky --}}
            <div class="p-6">
                <h2 class="mb-2 text-xl">Order Items</h2>
                <ul class="divide-y divide-line">
                    @foreach($order->items as $item)
                        <li class="flex items-baseline justify-between gap-4 py-3">
                            <span class="min-w-0">
                                <span class="font-medium">{{ $item->product_name }}</span>
                                <span class="num ml-2 text-sm text-subtle">× {{ $item->quantity }}</span>
                            </span>
                            <span class="num shrink-0">{{ $item->formatted_subtotal }}</span>
                        </li>
                    @endforeach
                </ul>

                <dl class="mt-4 space-y-2 border-t border-line pt-4 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-muted">Subtotal</dt>
                        <dd class="num">{{ $order->formatted_subtotal }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-muted">Shipping ({{ $order->payment_method->getLabel() }})</dt>
                        <dd class="num">{{ $order->formatted_shipping_cost }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between border-t border-line pt-3">
                        <dt class="font-display text-lg font-semibold uppercase tracking-wide">Total</dt>
                        <dd class="num text-2xl">{{ $order->formatted_total }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Adresa + platba --}}
            <div class="grid gap-6 border-t border-line bg-paper p-6 sm:grid-cols-2">
                <div>
                    <h2 class="eyebrow mb-2">Shipping Address</h2>
                    <x-shop.order-address :address="$order->shippingAddress" />
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-1">
                    <div>
                        <dt class="eyebrow">Payment Method</dt>
                        <dd class="mt-1 font-medium">{{ $order->payment_method->getLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow">Payment Status</dt>
                        <dd class="mt-1 font-medium">{{ $order->payment_status->getLabel() }}</dd>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <dt class="eyebrow">Order Status</dt>
                        <dd class="mt-1.5"><x-shop.order-status-badge :order="$order" size="sm" /></dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Akcie --}}
        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
            @auth
                <x-button :href="route('orders.index')" size="lg">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="square" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    My Orders
                </x-button>
            @endauth
            <x-button :href="route('home')" variant="secondary" size="lg">
                Continue Shopping
            </x-button>
        </div>
    </div>

</x-layouts.default-layout>
