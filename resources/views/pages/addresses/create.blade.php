{{-- [AI] Upravene s pomocou AI (Claude). --}}
@php($typeLabel = $type->label())

<x-layouts.default-layout title="New {{ $typeLabel }} Address">

    @include('pages.addresses.partials.breadcrumb', ['current' => "New {$typeLabel} Address"])

    <x-ui.page-header
        eyebrow="My addresses"
        title="New {{ $typeLabel }} Address"
        subtitle="Fill in the details for your new address."
    />

    <x-ui.card padding="lg" class="max-w-3xl">
        <form action="{{ route('addresses.store') }}" method="POST" data-validate>
            @csrf
            <input type="hidden" name="type" value="{{ $type->value }}">

            @include('pages.addresses.partials.form', [
                'type' => $type,
                'values' => $values,
                'submitLabel' => 'Save Address',
            ])
        </form>
    </x-ui.card>

</x-layouts.default-layout>
