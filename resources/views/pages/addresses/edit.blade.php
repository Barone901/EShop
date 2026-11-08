{{-- [AI] Upravene s pomocou AI (Claude). --}}
<x-layouts.default-layout title="Edit {{ $address->type_label }} Address">

    @include('pages.addresses.partials.breadcrumb', ['current' => "Edit {$address->type_label} Address"])

    <x-ui.page-header
        :eyebrow="__('My addresses')"
        title="Edit {{ $address->type_label }} Address"
        :subtitle="__('Update your address details.')"
    />

    <x-ui.card padding="lg" class="max-w-3xl">
        <form action="{{ route('addresses.update', $address) }}" method="POST" data-validate>
            @csrf
            @method('PUT')

            @include('pages.addresses.partials.form', [
                'type' => $address->type,
                'values' => $address->only([
                    'first_name', 'last_name', 'phone', 'email', 'street', 'city',
                    'postal_code', 'country', 'company_name', 'ico', 'dic', 'ic_dph', 'is_default',
                ]),
                'submitLabel' => __('Save Changes'),
            ])
        </form>
    </x-ui.card>

</x-layouts.default-layout>
