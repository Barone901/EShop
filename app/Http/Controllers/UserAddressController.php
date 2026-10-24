<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Controller pre spravu adries pouzivatela (dorucenie a fakturacia).
 * Vlastnictvo adresy kontroluje UserAddressPolicy.
 */
class UserAddressController extends Controller
{
    /**
     * Zobrazi zoznam vsetkych adries pouzivatela.
     */
    public function index(Request $request): View
    {
        $addresses = $request->user()->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('pages.addresses.index', [
            'shippingAddresses' => $addresses->where('type', UserAddress::TYPE_SHIPPING),
            'billingAddresses' => $addresses->where('type', UserAddress::TYPE_BILLING),
        ]);
    }

    /**
     * Zobrazi formular pre vytvorenie novej adresy (predvyplneny udajmi z profilu).
     */
    public function create(Request $request): View
    {
        $type = in_array($request->query('type'), array_keys(UserAddress::TYPES), true)
            ? $request->query('type')
            : UserAddress::TYPE_SHIPPING;

        $user = $request->user();

        return view('pages.addresses.create', [
            'type' => $type,
            'values' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'country' => UserAddress::DEFAULT_COUNTRY,
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
            ->with('success', "{$address->type_label} address has been successfully added.");
    }

    /**
     * Zobrazi formular pre upravu existujucej adresy.
     */
    public function edit(UserAddress $address): View
    {
        Gate::authorize('update', $address);

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
        Gate::authorize('update', $address);

        $address->update([
            ...$request->addressData(),
            'is_default' => $request->wantsDefault(),
        ]);

        if ($request->wantsDefault()) {
            $address->makeDefault();
        }

        return redirect()->route('addresses.index')
            ->with('success', "{$address->type_label} address has been successfully updated.");
    }

    /**
     * Vymaze adresu.
     */
    public function destroy(UserAddress $address): RedirectResponse
    {
        Gate::authorize('delete', $address);

        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', "{$address->type_label} address has been successfully deleted.");
    }

    /**
     * Nastavi adresu ako predvolenu pre dany typ.
     */
    public function setDefault(UserAddress $address): RedirectResponse
    {
        Gate::authorize('update', $address);

        $address->makeDefault();

        return redirect()->route('addresses.index')
            ->with('success', "{$address->type_label} address has been set as default.");
    }
}
