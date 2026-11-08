{{-- [AI] Vygenerovane s pomocou AI (Claude) pri redizajne. --}}
{{--
    Priebeh nakupu: 1 Cart -> 2 Checkout -> 3 Confirmation.
    current = cislo aktualneho kroku (1-3). Hotove kroky su odkazy len ak maju URL (kosik).
--}}
@props(['current' => 1])

@php
    $steps = [
        1 => [__('Cart'), route('cart.index')],
        2 => [__('Checkout'), null],
        3 => [__('Confirmation'), null],
    ];
@endphp

<nav aria-label="{{ __('Checkout progress') }}" {{ $attributes->class(['mb-6']) }}>
    <ol class="grid grid-cols-3 border border-line bg-white">
        @foreach($steps as $number => [$label, $url])
            @php $state = $number < $current ? 'done' : ($number === $current ? 'current' : 'upcoming'); @endphp
            <li @class([
                'relative flex min-h-12 items-center justify-center gap-2 px-2 text-center',
                'border-l border-line' => $number > 1,
                'bg-brand-50' => $state === 'current',
            ])>
                @if($state === 'current')
                    <span class="absolute inset-x-0 bottom-0 h-[3px] bg-brand-600" aria-hidden="true"></span>
                @endif

                <span @class([
                    'num grid size-6 shrink-0 place-items-center rounded-xs text-xs',
                    'bg-brand-600 text-white' => $state === 'current',
                    'bg-ok-700 text-white' => $state === 'done',
                    'border border-line-strong text-subtle' => $state === 'upcoming',
                ]) aria-hidden="true">
                    @if($state === 'done') ✓ @else {{ $number }} @endif
                </span>

                @php
                    $labelClass = 'font-display text-sm font-semibold uppercase tracking-wider sm:text-base '
                        . ($state === 'upcoming' ? 'text-subtle' : 'text-text');
                @endphp

                @if($state === 'done' && $url)
                    <a href="{{ $url }}" class="{{ $labelClass }} hover:text-brand-600">{{ $label }}</a>
                @else
                    <span class="{{ $labelClass }}" @if($state === 'current') aria-current="step" @endif>{{ $label }}</span>
                @endif

                @if($state === 'done')<span class="sr-only">{{ __('(completed)') }}</span>@endif
            </li>
        @endforeach
    </ol>
</nav>
