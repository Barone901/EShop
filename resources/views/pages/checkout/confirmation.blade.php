<x-layouts.default-layout title="Order Confirmed">

    <div>

        {{-- Success Message --}}
        <div class="max-w-2xl mx-auto text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-4">Order Received!</h1>
            <p class="text-gray-600 text-lg">Thank you for your order. A confirmation has been sent to <strong>{{ $order->shippingAddress?->email }}</strong>.</p>
        </div>

        {{-- Order Details --}}
        <div class="max-w-3xl mx-auto">
            <div class="card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Order Header --}}
                <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-indigo-600 font-medium">Order Number</p>
                            <p class="text-xl font-bold text-indigo-900">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-indigo-600 font-medium">Date</p>
                            <p class="font-semibold text-indigo-900">{{ $order->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Order Items</h3>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div class="flex-1">
                                    <span class="font-medium text-gray-900">{{ $item->product_name }}</span>
                                    <span class="text-gray-500 ml-2">× {{ $item->quantity }}</span>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $item->formatted_subtotal }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totals --}}
                    <div class="mt-6 pt-4 border-t border-gray-200 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping ({{ $order->payment_method->getLabel() }})</span>
                            <span class="text-gray-900">{{ $order->formatted_shipping_cost }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span class="text-gray-900">Total</span>
                            <span class="text-indigo-400">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Info --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">Shipping Address</h3>
                    <div class="text-gray-600">
                        <x-shop.order-address :address="$order->shippingAddress" />
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-wrap gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Payment Method</p>
                            <p class="font-medium text-gray-900">{{ $order->payment_method->getLabel() }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Payment Status</p>
                            <p class="font-medium text-gray-900">{{ $order->payment_status->getLabel() }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Order Status</p>
                            <x-shop.order-status-badge :order="$order" size="sm" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex flex-wrap gap-4 justify-center">
                @auth
                <a href="{{ route('orders.index') }}" class="inline-flex w-full sm:w-auto justify-center items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    My Orders
                </a>
                @endauth
                <a href="{{ route('home') }}" class="inline-flex w-full sm:w-auto justify-center items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>

</x-layouts.default-layout>

