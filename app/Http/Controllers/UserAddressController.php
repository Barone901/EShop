<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\AddressType;
use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Controller pre spravu adries pouzivatela (dorucenie a fakturacia).
 * Vlastnictvo adresy kontroluje UserAddressPolicy cez middleware "can" (cudzia adresa = 403).
 */
class UserAddressController extends Controller implements HasMiddleware
{
    /**
     * Autorizacia akcii nad konkretnou adresou (UserAddressPolicy).
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:update,address', only: ['edit', 'update', 'setDefault']),
            new Middleware('can:delete,address', only: ['destroy']),
        ];
    }

    /**
     * Zobrazi zoznam vsetkych adries pouzivatela.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Predvolena adresa prva, potom od najnovsej (User::addressesOfType)
        return view('pages.addresses.index', [
            'shippingAddresses' => $user->addressesOfType(AddressType::Shipping),
            'billingAddresses' => $user->addressesOfType(AddressType::Billing),
        ]);
    }

    /**
     * Zobrazi formular pre vytvorenie novej adresy (predvyplneny udajmi z profilu).
     */
    public function create(Request $request): View
    {
        $type = AddressType::tryFrom($request->string('type')->toString()) ?? AddressType::Shipping;

        $user = $request->user();

        return view('pages.addresses.create', [
            'type' => $type,
            'values' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'country' => config('shop.default_country'),
            ],
            'patterns' => UserAddressRequest::PATTERNS,
        ]);
    }

    /**
     * Ulozi novu adresu a nastavi ako predvolenu ak je pozadovane.
     */
    public function store(UserAddressRequest $request): RedirectResponse
    {
        $address = $request->user()->addresses()->create([
            ...$request->addressData(),
            'type' => $request->validated('type'),
        ]);

        if ($request->wantsDefault()) {
            $address->makeDefault();
        }

        return redirect()->route('addresses.index')
            ->with('success', __(':type address has been successfully added.', ['type' => $address->type_label]));
    }

    /**
     * Zobrazi formular pre upravu existujucej adresy.
     */
    public function edit(UserAddress $address): View
    {
        return view('pages.addresses.edit', [
            'address' => $address,
            'patterns' => UserAddressRequest::PATTERNS,
        ]);
    }

    /**
     * Aktualizuje existujucu adresu.
     */
    public function update(UserAddressRequest $request, UserAddress $address): RedirectResponse
    {
        $address->update([
            ...$request->addressData(),
            'is_default' => $request->wantsDefault(),
        ]);

        if ($request->wantsDefault()) {
            $address->makeDefault();
        }

        return redirect()->route('addresses.index')
            ->with('success', __(':type address has been successfully updated.', ['type' => $address->type_label]));
    }

    /**
     * Vymaze adresu.
     */
    public function destroy(UserAddress $address): RedirectResponse
    {
        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', __(':type address has been successfully deleted.', ['type' => $address->type_label]));
    }

    /**
     * Nastavi adresu ako predvolenu pre dany typ.
     */
    public function setDefault(UserAddress $address): RedirectResponse
    {
        $address->makeDefault();

        return redirect()->route('addresses.index')
            ->with('success', __(':type address has been set as default.', ['type' => $address->type_label]));
    }
}
