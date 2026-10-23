<footer class="site-footer border-t">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid gap-8 md:grid-cols-3 text-sm text-gray-600">
            <div>
                <div class="font-semibold text-gray-900">Shopping</div>
            </div>

            <div>
                <div class="font-semibold text-gray-900">Orders</div>
                <ul class="mt-2 space-y-2">
                    <li><a class="hover:text-white" href="{{ route('products.index') }}">Products</a></li>
                    <li><a class="hover:text-white" href="#">How it works</a></li>
                    <li><a class="hover:text-white" href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div>
                <div class="font-semibold text-gray-900">About Us</div>
                <ul class="mt-2 space-y-2">
                    <li>Email: info@triumf.sk</li>
                    <li>Phone: +421 912 345 678</li>
                </ul>
            </div>
        </div>

        <div class="mt-10 text-xs text-gray-500">
            © {{ date('Y') }} Eshop. All rights reserved.
        </div>
    </div>
</footer>
