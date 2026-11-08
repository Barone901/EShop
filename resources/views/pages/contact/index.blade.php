{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Kontakt. Formular: data-validate (form-validator.js), action contact.send (throttle 5/min) - nemen.
    Kontaktne udaje su povodne (pozor: nezhoduju sa s udajmi v hlavicke/paticke).
--}}
@php
    $contacts = [
        [__('Address'), '123 Store Street<br>City, Country 12345', null, 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        [__('Email'), $contactEmail, 'mailto:' . $contactEmail, 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        [__('Phone'), '+421 123 456 789', 'tel:+421123456789', 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
    ];

    $hours = [
        [__('Monday - Friday'), '9:00 - 18:00'],
        [__('Saturday'), '10:00 - 14:00'],
        [__('Sunday'), __('Closed')],
    ];
@endphp

<x-layouts.default-layout :title="__('Contact Us')">

    <x-ui.page-header :eyebrow="__('Support')" :title="__('Contact Us')" :subtitle="__('Have a question? We\'d love to hear from you.')" />

    <div class="grid gap-6 lg:grid-cols-3 lg:gap-8">
        {{-- Formular --}}
        <x-ui.card padding="lg" class="lg:col-span-2">
            <h2 class="mb-1 text-2xl">{{ __('Send us a message') }}</h2>
            <p class="mb-6 text-sm text-muted">{{ __('All fields are required.') }}</p>

            <x-ui.flash-messages :types="['success']" />

            <form action="{{ route('contact.send') }}" method="POST" class="space-y-5" data-validate>
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <x-form.input name="name" :label="__('Your Name')" required maxlength="255" autocomplete="name" :pattern="$patterns['name']" :value="$user?->name" />

                    <x-form.input name="email" type="email" :label="__('Email Address')" required maxlength="255" autocomplete="email" :value="$user?->email" />
                </div>

                <x-form.input name="subject" :label="__('Subject')" required maxlength="255" />

                <x-form.input name="message" :label="__('Message')" required minlength="10" maxlength="5000" textarea rows="6" input-class="resize-y min-h-36" />

                <div class="border-t border-line pt-5">
                    <x-button size="lg" class="w-full sm:w-auto">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ __('Send Message') }}
                    </x-button>
                </div>
            </form>
        </x-ui.card>

        {{-- Kontaktne udaje --}}
        <aside class="space-y-6">
            <section class="theme-dark border-l-3 border-brand-500 bg-ink-900 p-6 text-steel-100" aria-labelledby="contact-details">
                <h2 id="contact-details" class="mb-5 text-2xl text-white">{{ __('Get in Touch') }}</h2>

                <ul class="space-y-5">
                    @foreach($contacts as [$label, $value, $href, $iconPath])
                        <li class="flex items-start gap-4">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xs border border-ink-700 text-brand-400" aria-hidden="true">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-width="2" d="{{ $iconPath }}"/></svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="eyebrow text-steel-300">{{ $label }}</h3>
                                @if($href)
                                    <a href="{{ $href }}" class="mt-0.5 inline-flex min-h-8 items-center break-all font-medium text-steel-100 hover:text-brand-400">{{ $value }}</a>
                                @else
                                    <p class="mt-0.5 font-medium">{!! $value !!}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            <x-ui.card padding="md">
                <h2 class="mb-4 text-xl">{{ __('Business Hours') }}</h2>
                <dl class="divide-y divide-line text-sm">
                    @foreach($hours as [$days, $time])
                        <div class="flex justify-between gap-4 py-2.5 first:pt-0 last:pb-0">
                            <dt class="text-muted">{{ $days }}</dt>
                            <dd @class(['num', 'text-subtle' => $time === 'Closed', 'text-text' => $time !== 'Closed'])>{{ $time }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-ui.card>
        </aside>
    </div>
</x-layouts.default-layout>
