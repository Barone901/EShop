> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/views/pages/` – stránky eshopu

Jedna šablóna = jedna stránka (routa). Každá je obalená layoutom `<x-layouts.default-layout>` a
skladá sa z [komponentov](../components/README.md). Dáta jej pošle controller – view ich len zobrazí.
Teória: [`resources/views/README.md`](../README.md).

| View | Routa | Controller → dáta | JS na stránke |
|---|---|---|---|
| [`home`](home.blade.php) | `/`, `/category/{slug}` | [`HomeController`, `CategoryController`](../../../app/Http/Controllers/README.md) → `categories`, `banners` | slider |
| [`products/index`](products/index.blade.php) | `/products` | `ProductController::index` → `products` (stránkované), `categories` | radenie (`data-navigate-on-change`), košík, quick view |
| [`products/show`](products/show.blade.php) | `/product/{slug}` | `ProductController::show` → `product` | pridanie do košíka s množstvom (`#quantity`) |
| [`cart/index`](cart/index.blade.php) | `/cart` (`Route::view`) | žiadne – obsah `#cart-page-content` vykreslí [`cart.js`](../../js/modules/cart.js) z **API** | košík (AJAX) |
| [`checkout/index`](checkout/index.blade.php) | `/checkout` | `OrderController::checkout` → `cart`, uložené adresy, `patterns` | [`checkout.js`](../../js/modules/checkout.js), validácia |
| [`checkout/confirmation`](checkout/confirmation.blade.php) | `/orders/confirmation/{n}` | `OrderController::confirmation` → `order` | – |
| [`orders/index`](orders/index.blade.php), [`orders/show`](orders/show.blade.php) | `/orders`, `/orders/{n}` *(auth)* | `OrderController::index/show` → `orders` / `order` | potvrdenie zrušenia/zmazania |
| [`addresses/index`](addresses/index.blade.php), [`create`](addresses/create.blade.php), [`edit`](addresses/edit.blade.php) | `/addresses…` *(auth)* | [`UserAddressController`](../../../app/Http/Controllers/UserAddressController.php) → adresy, `patterns` | validácia, potvrdenie zmazania |
| [`addresses/partials/form`](addresses/partials/form.blade.php), [`breadcrumb`](addresses/partials/breadcrumb.blade.php) | – | spoločný formulár a navigácia pre create aj edit (`@include`) | – |
| [`articles/index`](articles/index.blade.php), [`articles/show`](articles/show.blade.php) | `/pages`, `/pages/{slug}` | [`PageController`](../../../app/Http/Controllers/PageController.php) → `pages` / `page`, `relatedPages` | kopírovanie odkazu |
| [`contact/index`](contact/index.blade.php) | `/contact` | `ContactController::index` → `patterns` | validácia |
| [`about/index`](about/index.blade.php) | `/about` (`Route::view`) | žiadne (statický obsah) | – |

## Príklad: formulár adresy (view ↔ controller)

```
addresses/create.blade.php
  └► @include partials/form  →  <form method=POST action=route('addresses.store') data-validate> @csrf
         <x-form.input name="city" :pattern="$patterns['city']" required>   (vzor z InputPattern)
  submit → form-validator.js (prehliadač) → POST /addresses → UserAddressRequest (server)
         → UserAddressController::store → redirect addresses.index s 'success'
         → addresses/index.blade.php  <x-ui.flash-messages>
```
