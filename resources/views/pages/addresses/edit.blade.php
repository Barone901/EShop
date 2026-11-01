{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
<x-layouts.default-layout title="Edit {{ $address->type_label }} Address">

    @include('pages.addresses.partials.breadcrumb', ['current' => "Edit {$address->type_label} Address"])

    <x-ui.page-header
        eyebrow="My addresses"
        title="Edit {{ $address->type_label }} Address"
        subtitle="Update your address details."
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
                'submitLabel' => 'Save Changes',
            ])
        </form>
    </x-ui.card>

</x-layouts.default-layout>
