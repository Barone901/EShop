{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
@props(['status'])

@if ($status)
    <div role="status" {{ $attributes->merge(['class' => 'border-l-3 border-ok-700 bg-ok-50 px-4 py-3 text-sm font-medium text-ok-700']) }}>
        {{ $status }}
    </div>
@endif
