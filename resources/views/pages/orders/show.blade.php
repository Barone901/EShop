{{--
    Detail objednavky.
    JS: formulare zrusenia/zmazania maju data-confirm (page-actions.js) - nemen.
--}}
@php
    $currentIndex = $order->progressStep();
    $steps = \App\Enums\OrderStatus::progressSteps();
    $lastIndex = count($steps) - 1;
    $cancelled = $order->isCancelled();
@endphp

<x-layouts.default-layout title="Order {{ $order->order_number }}">

    {{-- Drobcekova navigacia --}}
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-subtle">
            <li><a href="{{ route('dashboard') }}" class="hover:text-brand-600">My account</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('orders.index') }}" class="hover:text-brand-600">My Orders</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" class="num text-text">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <x-ui.flash-messages />

    {{-- Hlavicka objednavky --}}
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-line pb-6">
        <div>
            <span class="mb-4 block h-[3px] w-12 bg-brand-600" aria-hidden="true"></span>
            <p class="eyebrow">Order</p>
            <h1 class="num mt-2 text-3xl normal-case tracking-normal md:text-4xl">{{ $order->order_number }}</h1>
            <p class="mt-1 text-muted">Created on <span class="num">{{ $order->created_at->format('M d, Y H:i') }}</span></p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-shop.order-status-badge :order="$order" size="lg" />

            @if($order->isCancellable())
                <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" data-confirm="Are you sure you want to cancel this order?">
                    @csrf
                    <x-button variant="danger" size="sm" type="submit">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Cancel Order
                    </x-button>
                </form>
            @endif

            @if($order->isDeletable())
                <form action="{{ route('orders.destroy', $order->order_number) }}" method="POST" data-confirm="Are you sure you want to permanently delete this order?">
                    @csrf
                    @method('DELETE')
                    <x-button variant="danger" size="sm" type="submit">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete Order
                    </x-button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8">
        <div class="space-y-6 lg:col-span-2">

            {{-- Priebeh objednavky --}}
            <section class="card p-5 md:p-6" aria-labelledby="order-progress">
                <h2 id="order-progress" class="mb-6 text-xl">Order Status</h2>

                <ol class="flex items-start">
                    @foreach($steps as $index => $status)
                        @php $state = $cancelled ? 'upcoming' : ($index < $currentIndex ? 'done' : ($index === $currentIndex ? 'current' : 'upcoming')); @endphp
                        <li class="relative flex flex-1 flex-col items-center text-center" @if($state === 'current') aria-current="step" @endif>
                            {{-- Spojnica k dalsiemu kroku --}}
                            @if($index < $lastIndex)
                                <span @class([
                                    'absolute left-1/2 top-4 h-[3px] w-full sm:top-5',
                                    'bg-brand-600' => !$cancelled && $index < $currentIndex,
                                    'bg-line' => $cancelled || $index >= $currentIndex,
                                ]) aria-hidden="true"></span>
                            @endif

                            <span @class([
                                'num relative z-10 grid size-8 place-items-center rounded-xs text-xs sm:size-10 sm:text-sm',
                                'bg-brand-600 text-white' => $state === 'done' || $state === 'current',
                                'ring-4 ring-brand-50' => $state === 'current',
                                'border border-line-strong bg-white text-subtle' => $state === 'upcoming',
                            ])>
                                @if($state === 'done')
                                    <svg class="size-4 sm:size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span class="sr-only">Completed:</span>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </span>

                            <span @class([
                                'mt-2 px-1 font-display text-xs font-semibold uppercase tracking-wide sm:text-sm',
                                'text-text' => $state !== 'upcoming',
                                'text-subtle' => $state === 'upcoming',
                            ])>{{ $status->getLabel() }}</span>
                        </li>
                    @endforeach
                </ol>

                @if($cancelled)
                    <p class="mt-6 flex items-center gap-2 border-l-3 border-danger-700 bg-danger-50 px-4 py-3 text-sm font-medium text-danger-700" role="status">
                        <span aria-hidden="true">⚠</span> Order has been cancelled
                    </p>
                @endif
            </section>

            {{-- Polozky --}}
            <section class="card overflow-hidden" aria-labelledby="order-items">
                <h2 id="order-items" class="border-b border-line px-5 py-4 text-xl md:px-6">Order Items</h2>
                <ul class="divide-y divide-line">
                    @foreach($order->items as $item)
                        <li class="flex items-center gap-4 p-5 md:px-6">
                            <div class="size-16 shrink-0 overflow-hidden rounded-xs border border-line bg-tile sm:size-20">
                                @if($item->product?->image_src)
                                    <img src="{{ $item->product->image_src }}" alt="" class="h-full w-full object-contain p-1 mix-blend-multiply">
                                @else
                                    <span class="flex h-full items-center justify-center text-line-strong" aria-hidden="true">
                                        <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-width="1.5" d="M4 16l4.6-4.6a2 2 0 012.8 0L16 16m-2-2l1.6-1.6a2 2 0 012.8 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-sans text-base font-semibold normal-case tracking-normal">{{ $item->product_name }}</h3>
                                <p class="num mt-1 text-sm text-subtle">Unit price: {{ $item->formatted_price }}</p>
                                <p class="num text-sm text-subtle">Quantity: {{ $item->quantity }}</p>
                            </div>
                            <span class="num shrink-0 text-lg">{{ $item->formatted_subtotal }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        {{-- Bocny panel --}}
        <aside class="space-y-6">
            <section class="card border-t-3 border-t-brand-600 p-5 md:p-6" aria-labelledby="order-summary">
                <h2 id="order-summary" class="mb-4 text-xl">Summary</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd class="num">{{ $order->formatted_subtotal }}</dd></div>
                    <div class="flex justify-between"><dt class="text-muted">Shipping</dt><dd class="num">{{ $order->formatted_shipping_cost }}</dd></div>
                    <div class="flex items-baseline justify-between border-t border-line pt-3">
                        <dt class="font-display text-lg font-semibold uppercase tracking-wide">Total</dt>
                        <dd class="num text-2xl">{{ $order->formatted_total }}</dd>
                    </div>
                </dl>
            </section>

            <section class="card p-5 md:p-6" aria-labelledby="order-payment">
                <h2 id="order-payment" class="mb-4 text-xl">Payment</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-muted">Payment Method</dt><dd class="text-right font-medium">{{ $order->payment_method->getLabel() }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-muted">Payment Status</dt><dd class="text-right font-medium">{{ $order->payment_status->getLabel() }}</dd></div>
                </dl>
            </section>

            <section class="card p-5 text-sm md:p-6" aria-labelledby="order-shipping">
                <h2 id="order-shipping" class="mb-4 text-xl">Shipping Address</h2>
                <x-shop.order-address :address="$order->shippingAddress" />
            </section>

            @if($order->note)
                <section class="card p-5 md:p-6" aria-labelledby="order-note">
                    <h2 id="order-note" class="mb-3 text-xl">Note</h2>
                    <p class="whitespace-pre-line text-sm text-muted">{{ $order->note }}</p>
                </section>
            @endif
        </aside>
    </div>

    <x-button :href="route('orders.index')" variant="ghost" class="-ml-2 mt-8">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to My Orders
    </x-button>

</x-layouts.default-layout>
