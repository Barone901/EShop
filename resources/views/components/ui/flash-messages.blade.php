@props(['types' => ['success', 'error']])

@php
    $styles = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
    ];
@endphp

@foreach($types as $type)
    @if(session($type))
        <div class="mb-6 p-4 border rounded-lg {{ $styles[$type] }}" role="{{ $type === 'error' ? 'alert' : 'status' }}">
            {{ session($type) }}
        </div>
    @endif
@endforeach
