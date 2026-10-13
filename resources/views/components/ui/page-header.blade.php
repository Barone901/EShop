@props(['title', 'subtitle' => null])

<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight">{{ $title }}</h1>
    @if($subtitle)
        <p class="mt-2 text-gray-600">{{ $subtitle }}</p>
    @endif
</div>

