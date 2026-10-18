@props(['text' => 'Loading...'])

<div class="flex items-center justify-center py-16">
    <div class="text-center">
        <div class="animate-spin w-10 h-10 border-2 border-gray-300 border-t-black rounded-full mx-auto mb-4"></div>
        <p class="text-gray-500">{{ $text }}</p>
    </div>
</div>

