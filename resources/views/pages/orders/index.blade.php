{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{-- Zoznam objednavok prihlaseneho pouzivatela. --}}
<x-layouts.default-layout :title="__('My Orders')">

    <x-ui.page-header :eyebrow="__('My account')" :title="__('My Orders')" :subtitle="__('View and track all your orders.')">
        <x-slot name="actions">
            <x-button :href="route('dashboard')" variant="ghost">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ __('Back to Dashboard') }}
            </x-button>
        </x-slot>
    </x-ui.page-header>

    <x-ui.flash-messages />

    @if($orders->isEmpty())
        <x-ui.card padding="lg" class="text-center">
            <span class="mx-auto mb-5 grid size-16 place-items-center rounded-xs bg-tile text-line-strong" aria-hidden="true">
                <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </span>
            <h2 class="text-2xl">{{ __('No orders yet') }}</h2>
            <p class="mt-2 text-muted">{{ __('You haven\'t placed any orders yet. Start shopping!') }}</p>
            <x-button :href="route('home')" class="mt-6">{{ __('Start Shopping') }}</x-button>
        </x-ui.card>
    @else
        {{-- Desktop: tabulka --}}
        <div class="card hidden overflow-hidden md:block">
            <table class="min-w-full text-sm">
                <caption class="sr-only">{{ __('Your orders') }}</caption>
                <thead class="border-b border-line bg-paper">
                    <tr>
                        <th scope="col" class="eyebrow px-6 py-3 text-left">{{ __('Order Number') }}</th>
                        <th scope="col" class="eyebrow px-4 py-3 text-left">{{ __('Date') }}</th>
                        <th scope="col" class="eyebrow px-4 py-3 text-left">{{ __('Status') }}</th>
                        <th scope="col" class="eyebrow px-4 py-3 text-right">{{ __('Items') }}</th>
                        <th scope="col" class="eyebrow px-4 py-3 text-right">{{ __('Total') }}</th>
                        <th scope="col" class="px-6 py-3"><span class="sr-only">{{ __('Action') }}</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach($orders as $order)
                        <tr class="transition-colors hover:bg-paper">
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('orders.show', $order) }}" class="num text-brand-600 hover:text-brand-700 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="num whitespace-nowrap px-4 py-4 text-subtle">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-4 py-4"><x-shop.order-status-badge :order="$order" /></td>
                            <td class="num whitespace-nowrap px-4 py-4 text-right text-muted">
                                {{ trans_choice(':count item|:count items', $order->items_count) }}
                            </td>
                            <td class="num whitespace-nowrap px-4 py-4 text-right text-base">{{ $order->formatted_total }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a href="{{ route('orders.show', $order) }}"
                                   class="inline-flex min-h-11 items-center gap-1 font-medium text-brand-600 hover:text-brand-700"
                                   aria-label="{{ __('View details of order :value', ['value' => $order->order_number]) }}">
                                    {{ __('View Details') }}
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobil: karty (cela karta je odkaz na detail) --}}
        <ul class="space-y-3 md:hidden">
            @foreach($orders as $order)
                <li>
                    <a href="{{ route('orders.show', $order) }}" class="card block p-4 transition-colors hover:border-brand-600">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="num text-brand-600">{{ $order->order_number }}</p>
                                <p class="num mt-0.5 text-xs text-subtle">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <x-shop.order-status-badge :order="$order" />
                        </div>
                        <div class="mt-3 flex items-baseline justify-between border-t border-line pt-3">
                            <span class="text-sm text-muted">{{ trans_choice(':count item|:count items', $order->items_count) }}</span>
                            <span class="num text-lg">{{ $order->formatted_total }}</span>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

</x-layouts.default-layout>
