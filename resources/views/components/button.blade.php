@props(['variant' => 'primary'])

@php
// Rovnaky tvar ako ostatne tlacidla na webe (rounded-lg, text-sm), focus stav je v app.css
$baseClasses = 'inline-flex items-center px-4 py-2 border rounded-lg font-semibold text-sm transition-colors duration-150';

// Primarne tlacidlo v indigo, rovnako ako ostatne tlacidla v sekcii uctu
$variantClasses = match($variant) {
    'secondary' => 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-25',
    'danger' => 'bg-red-600 border-transparent text-white hover:bg-red-500',
    default => 'bg-indigo-600 border-transparent text-white hover:bg-indigo-700',
};

$defaultType = $variant === 'secondary' ? 'button' : 'submit';
@endphp

<button {{ $attributes->merge(['type' => $defaultType, 'class' => $baseClasses . ' ' . $variantClasses]) }}>
    {{ $slot }}
</button>

