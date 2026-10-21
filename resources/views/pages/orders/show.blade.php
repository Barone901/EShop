<x-layouts.default-layout title="Order {{ $order->order_number }}">

    <div>

        {{-- Flash Messages --}}
        <x-ui.flash-messages />

        {{-- Order Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Order {{ $order->order_number }}</h1>
                <p class="text-gray-600 mt-1">Created on {{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <x-shop.order-status-badge :order="$order" size="lg" />
                @if($order->isCancellable())
                    <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" data-confirm="Are you sure you want to cancel this order?">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel Order
                        </button>
                    </form>
                @endif
                @if($order->isDeletable())
                    <form action="{{ route('orders.destroy', $order->order_number) }}" method="POST" data-confirm="Are you sure you want to permanently delete this order?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Order
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Order Progress --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Order Status</h2>
                    <div>
                        @php
                            $currentIndex = $order->progressStep();
                            $lastIndex = count(\App\Models\Order::PROGRESS_STATUSES) - 1;
                        @endphp
                        <div class="flex items-center justify-between">
                            @foreach(\App\Models\Order::PROGRESS_STATUSES as $index => $status)
                                <div class="flex flex-col items-center relative {{ $index < $lastIndex ? 'flex-1' : '' }}">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold
                                        {{ $index <= $currentIndex ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}
                                        {{ $order->isCancelled() ? 'bg-red-500 text-white' : '' }}">
                                        @if($index < $currentIndex)
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <span class="text-[11px] sm:text-xs mt-2 text-center {{ $index <= $currentIndex ? 'text-indigo-400 font-medium' : 'text-gray-500' }}">
                                        {{ \App\Models\Order::STATUSES[$status] }}
                                    </span>
                                    @if($index < $lastIndex)
                                        <div class="absolute top-4 sm:top-5 left-full w-full h-0.5 {{ $index < $currentIndex ? 'bg-indigo-600' : 'bg-gray-200' }}" style="transform: translateX(-50%);"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($order->isCancelled())
                            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg text-center">
                                <span class="text-red-700 font-medium">Order has been cancelled</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <div class="p-6 flex items-center gap-4">
                                @if($item->product?->image_src)
                                    <img src="{{ $item->product->image_src }}"
                                         alt="{{ $item->product_name }}"
                                         class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $item->product_name }}</h4>
                                    <p class="text-gray-500 text-sm">Unit price: {{ $item->formatted_price }}</p>
                                    <p class="text-gray-500 text-sm">Quantity: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900">{{ $item->formatted_subtotal }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Order Summary --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium text-gray-900">{{ $order->formatted_shipping_cost }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200">
                            <span class="text-gray-900">Total</span>
                            <span class="text-indigo-400">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method</span>
                            <span class="font-medium text-gray-900">{{ $order->payment_method_text }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Status</span>
                            <span class="font-medium text-gray-900">{{ $order->payment_status_text }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h2>
                    <div class="text-sm text-gray-600">
                        <x-shop.order-address :address="$order->shippingAddress" />
                    </div>
                </div>

                {{-- Order Note --}}
                @if($order->note)
                    <div class="card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Note</h2>
                        <p class="text-sm text-gray-600">{{ $order->note }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Back to Orders --}}
        <div class="mt-8 text-center">
            <a href="{{ route('orders.index') }}" class="font-medium text-white underline-offset-4 hover:underline">
                ← Back to My Orders
            </a>
        </div>
    </div>

</x-layouts.default-layout>

