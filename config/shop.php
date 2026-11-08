<?php

// [AI] Vygenerovane s pomocou AI (Claude).

/**
 * Nastavenia eshopu.
 */
return [

    // Mena cien (ISO 4217) - formatovanie cez Number::currency (App\Support\Price)
    'currency' => 'EUR',

    // Cena dopravy (dobierka) v EUR
    'shipping_cost' => 3.00,

    // Predvolena krajina adries (checkout, ulozene adresy)
    'default_country' => 'Slovakia',

    // Maximalny pocet kusov jedneho produktu v kosiku (Form Request, CartService, atribut max v x-shop.cart-page)
    'max_cart_quantity' => 99,

    // Adresa, na ktoru chodia spravy z kontaktneho formulara (zobrazuje sa aj na stranke Kontakt)
    'contact_email' => env('SHOP_CONTACT_EMAIL', 'info@triumf.sk'),

];
