> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/views/layouts/` – layout stránky

| Súbor | Úloha | Vkladá sa |
|---|---|---|
| [`default.blade.php`](default.blade.php) | kostra HTML: `<head>` (titulok, `csrf-token` meta pre AJAX, `@vite` CSS a JS), odkaz „preskočiť na obsah“, `<main id="main-content">`; podľa `type` mení obal obsahu (`default` / `app` / `guest` – karta v strede pre prihlásenie) | cez triedu [`DefaultLayout`](../../../app/View/README.md) – `<x-layouts.default-layout>` v každej stránke |
| [`partials/header.blade.php`](partials/header.blade.php) | tmavá hlavička: logo, navigácia, vyhľadávanie (`#search-input` → [`search.js`](../../js/modules/search.js)), košík (`#cart-wrapper` s `data-cart-url`… → [`cart.js`](../../js/modules/cart.js), [`menu.js`](../../js/modules/menu.js)), mobilné menu | `@include` v layoute |
| [`partials/account.blade.php`](partials/account.blade.php) | prihlásený: dropdown účtu (My profile → dashboard, My Orders, Addresses, odhlásenie – POST `logout` s `@csrf`); hosť: odkazy Log in / Register | `@include` v hlavičke |
| [`partials/footer.blade.php`](partials/footer.blade.php) | pätička (`.site-footer` – pravidlo autora v [`app.css`](../../css/README.md)), kontakty, odkazy | `@include` v layoute |

`{{ $slot }}` je miesto, kam sa vloží obsah konkrétnej stránky z [`pages/`](../pages/README.md).
Slot `head` (meta tagy článku) a `fullWidth` (tmavý pás na homepage) sú pomenované sloty.
