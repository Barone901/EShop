{{-- O nas. Texty su povodne; cisla vo faktoch su prevzate z textu stranky. --}}
@php
    $facts = [
        ['1999', 'Founded'],
        ['25+', 'Years of experience'],
        ['60', 'Professionals in the team'],
        ['SK · CZ · PL', 'Sales representatives'],
    ];

    $sections = [
        ['Our History', 'The company was founded in 1999 by Mr. Andrej Horváth as BGS Slovakia s.r.o., initially distributing tools from the German company BGS Technic. Through steady growth and customer trust, we expanded our supplier network to fully serve auto repair shops, tire services, and hand tool retailers. In 2008, we launched our own brand – <strong>Triumf Professional Tools</strong> – and grew to a team of 60 professionals.'],
        ['What We Offer', 'Today, we provide comprehensive hand tools and specialized equipment for automotive repairs. Beyond products, we offer personalized consultations and custom solutions. Our metalworking workshop can quickly manufacture special fixtures, and we create custom foam inserts for workshop carts and cases.'],
        ['Our Team & Reach', 'Our team of technicians demonstrates and presents tools directly at customer locations. We have representatives across Slovakia, Czech Republic, and Poland serving both retail and automotive service networks. Our showroom is located at Kollárova 88, Martin, Slovakia.'],
    ];

    $milestones = [
        ['2009', "Became exclusive distributor for Pichler (Austria) – one of Europe's finest tool brands with 30+ years of tradition"],
        ['2010', 'Expanded to Baltic countries (Lithuania, Latvia, Estonia)'],
        ['2012', 'Partnership with Wallmek (Sweden) for hydraulic and mechanical chassis equipment'],
        ['2013', 'Acquired Dutch company Beargrip, expanding into manufacturing. Now exporting to Australia, New Zealand, Colombia, Chile, Venezuela, Canada, and UAE'],
        ['2015', 'Partnered with Dieselland for diagnostic equipment, presenting at Automechanika Moscow'],
    ];
@endphp

<x-layouts.default-layout title="About Us">

    <x-ui.page-header eyebrow="Company" title="About Us" subtitle="Professional tools for professionals since 1999" />

    {{-- Uvod + fakty --}}
    <div class="grid gap-8 lg:grid-cols-12 lg:items-start">
        <p class="text-lg leading-relaxed text-muted md:text-xl lg:col-span-7">
            Allow us to introduce <strong class="font-semibold text-text">Triumf Professional Tools s.r.o.</strong> – a company with over 25 years of experience in professional hand tools and automotive equipment.
        </p>

        <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-xs border border-line bg-line lg:col-span-5">
            @foreach($facts as [$value, $label])
                <div class="bg-white p-4">
                    <dt class="eyebrow">{{ $label }}</dt>
                    <dd class="num mt-1 text-2xl text-text md:text-3xl">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </div>

    {{-- Historia, ponuka, tim --}}
    <div class="mt-12 grid gap-4 md:grid-cols-3 md:gap-6">
        @foreach($sections as $index => [$heading, $text])
            <section class="card border-t-3 border-t-brand-600 p-6" aria-labelledby="about-section-{{ $index }}">
                <p class="eyebrow mb-2">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                <h2 id="about-section-{{ $index }}" class="mb-3 text-2xl">{{ $heading }}</h2>
                <p class="leading-relaxed text-muted [&_strong]:font-semibold [&_strong]:text-text">{!! $text !!}</p>
            </section>
        @endforeach
    </div>

    {{-- Partnerstva: casova os --}}
    <section class="mt-16" aria-labelledby="about-milestones">
        <x-ui.section-header heading-id="about-milestones" eyebrow="Milestones" title="Partnerships & Expansion" />

        <ol class="relative ml-2 border-l-2 border-line md:ml-0">
            @foreach($milestones as [$year, $text])
                <li class="relative pb-8 pl-8 last:pb-0 md:grid md:grid-cols-[6rem_1fr] md:gap-6 md:pl-10">
                    <span class="absolute -left-[7px] top-1.5 size-3 rounded-full bg-brand-600 ring-4 ring-paper" aria-hidden="true"></span>
                    <p class="num text-xl text-brand-600">{{ $year }}</p>
                    <p class="mt-1 leading-relaxed text-muted md:mt-0">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Citat (tmavy blok) --}}
    <figure class="theme-dark mt-16 border-l-3 border-brand-500 bg-ink-900 px-6 py-8 text-steel-100 md:px-10 md:py-10">
        <blockquote class="font-display text-2xl font-semibold uppercase leading-snug tracking-wide text-white md:text-3xl">
            "Our team prefers personal meetings directly at your location, where we can demonstrate why Triumf Professional Tools is the partner you can rely on for long-term success."
        </blockquote>
        <figcaption class="mt-5 text-steel-300">— <span class="font-semibold text-steel-100">Andrej Horváth</span>, CEO</figcaption>
    </figure>

</x-layouts.default-layout>
