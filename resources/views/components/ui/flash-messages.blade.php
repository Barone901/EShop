{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
@props(['types' => ['success', 'error']])

@php
    $styles = [
        'success' => 'border-ok-700 bg-ok-50 text-ok-700',
        'error' => 'border-danger-700 bg-danger-50 text-danger-700',
    ];
@endphp

@foreach($types as $type)
    @if(session($type))
        <div class="mb-6 border-l-3 px-4 py-3 text-sm font-medium {{ $styles[$type] }}" role="{{ $type === 'error' ? 'alert' : 'status' }}">
            @if($type === 'error')<span aria-hidden="true">⚠</span>@endif
            {{ session($type) }}
        </div>
    @endif
@endforeach
