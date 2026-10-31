@props(['order', 'size' => 'xs'])

@php
    /** @var \App\Models\Order $order */
    /** @var string $size */

    // Plné názvy tried, aby ich Tailwind našiel pri builde
    $colors = [
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'indigo' => 'bg-indigo-100 text-indigo-800',
        'purple' => 'bg-purple-100 text-purple-800',
        'green' => 'bg-green-100 text-green-800',
        'red' => 'bg-red-100 text-red-800',
        'gray' => 'bg-gray-100 text-gray-800',
    ];

    $sizes = [
        'xs' => 'px-2.5 py-0.5 text-xs font-medium',
        'sm' => 'px-2.5 py-0.5 text-sm font-medium',
        'lg' => 'px-4 py-2 text-sm font-semibold',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-full',
    $sizes[$size] ?? $sizes['xs'],
    $colors[$order->status->badgeColor()] ?? $colors['gray'],
]) }}>
    {{ $order->status->getLabel() }}
</span>
