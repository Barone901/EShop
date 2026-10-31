@php($typeLabel = $type->label())

<x-layouts.default-layout title="New {{ $typeLabel }} Address">

    <div>

        {{-- Page Header --}}
        <x-ui.page-header
            title="New {{ $typeLabel }} Address"
            subtitle="Fill in the details for your new address."
        />

        <div class="max-w-2xl mx-auto">
            <div class="card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form action="{{ route('addresses.store') }}" method="POST" data-validate>
                    @csrf
                    <input type="hidden" name="type" value="{{ $type->value }}">

                    @include('pages.addresses.partials.form', [
                        'type' => $type,
                        'values' => $values,
                        'submitLabel' => 'Save Address',
                    ])
                </form>
            </div>
        </div>
    </div>

</x-layouts.default-layout>
