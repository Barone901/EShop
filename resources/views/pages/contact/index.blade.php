<x-layouts.default-layout title="Contact Us">

    <div>
        {{-- Page Header --}}
        <x-ui.page-header title="Contact Us" subtitle="Have a question? We'd love to hear from you." />

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Contact Form --}}
            <div class="lg:col-span-2">
                <div class="card bg-white rounded-xl border p-6 lg:p-8">
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-500" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <p class="text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-6" data-validate>
                        @csrf

                        <div class="grid md:grid-cols-2 gap-6">
                            <x-form.input name="name" label="Your Name" required maxlength="255" :pattern="$patterns['name']" :value="auth()->user()?->name" />

                            <x-form.input name="email" type="email" label="Email Address" required maxlength="255" :value="auth()->user()?->email" />
                        </div>

                        <x-form.input name="subject" label="Subject" required maxlength="255" />

                        <x-form.input name="message" label="Message" required minlength="10" maxlength="5000" textarea rows="6" input-class="resize-none" />

                        {{-- Submit Button --}}
                        <div>
                            <button
                                type="submit"
                                class="w-full sm:w-auto px-8 py-3 bg-black text-white rounded-lg hover:opacity-90 transition-opacity font-medium"
                            >
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="space-y-6">
                {{-- Contact Details --}}
                <div class="bg-white rounded-xl border p-6">
                    <h3 class="font-semibold text-lg mb-4">Get in Touch</h3>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-600" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium">Address</h4>
                                <p class="text-sm text-gray-600">123 Store Street<br>City, Country 12345</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-600" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium">Email</h4>
                                <a href="mailto:info@eshop.com" class="text-sm text-gray-600 hover:underline">info@eshop.com</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-600" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium">Phone</h4>
                                <a href="tel:+421123456789" class="text-sm text-gray-600 hover:underline">+421 123 456 789</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Business Hours --}}
                <div class="card bg-white rounded-xl border p-6">
                    <h3 class="font-semibold text-lg mb-4">Business Hours</h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Monday - Friday</span>
                            <span class="font-medium">9:00 - 18:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Saturday</span>
                            <span class="font-medium">10:00 - 14:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sunday</span>
                            <span class="font-medium text-red-400">Closed</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.default-layout>

