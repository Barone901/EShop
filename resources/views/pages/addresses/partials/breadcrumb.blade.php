{{-- [AI] Vygenerovane s pomocou AI (Claude) pri redizajne. --}}
{{-- Drobcekova navigacia pre formulare adries. Premenna: $current (text aktualnej stranky). --}}
<nav aria-label="{{ __('Breadcrumb') }}" class="mb-6">
    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-subtle">
        <li><a href="{{ route('dashboard') }}" class="hover:text-brand-600">{{ __('My account') }}</a></li>
        <li aria-hidden="true">/</li>
        <li><a href="{{ route('addresses.index') }}" class="hover:text-brand-600">{{ __('My Addresses') }}</a></li>
        <li aria-hidden="true">/</li>
        <li aria-current="page" class="text-text">{{ $current }}</li>
    </ol>
</nav>
