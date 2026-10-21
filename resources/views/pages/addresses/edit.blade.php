<x-layouts.default-layout title="Edit {{ $address->type_label }} Address">

    <div>

        {{-- Page Header --}}
        <x-ui.page-header
            title="Edit {{ $address->type_label }} Address"
            subtitle="Update your address details."
        />

        <div class="max-w-2xl mx-auto">
            <div class="card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form action="{{ route('addresses.update', $address) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('pages.addresses.partials.form', [
                        'type' => $address->type,
                        'values' => $address->only([
                            'first_name', 'last_name', 'phone', 'email', 'street', 'city',
                            'postal_code', 'country', 'company_name', 'ico', 'dic', 'ic_dph', 'is_default',
                        ]),
                        'submitLabel' => 'Save Changes',
                    ])
                </form>
            </div>
        </div>
    </div>

</x-layouts.default-layout>

