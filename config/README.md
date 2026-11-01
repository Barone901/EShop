> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `config/` – konfigurácia

Konfiguračné súbory vracajú pole nastavení, v kóde sa čítajú cez `config('subor.kluc')`. Hodnoty
závislé od prostredia (heslo DB, URL) sa berú z `.env` cez `env()`.

| Súbor | Pôvod | Obsah |
|---|---|---|
| [`shop.php`](shop.php) | **vlastný** (AI, označený) | `shipping_cost` (cena dopravy), `default_country` (predvolená krajina adries), `max_cart_quantity` (limit kusov jedného produktu v košíku) |
| [`permission.php`](permission.php) | Spatie Laravel Permission | tabuľky a cache rolí a oprávnení |
| `app.php`, `auth.php`, `cache.php`, `database.php`, `filesystems.php`, `logging.php`, `mail.php`, `queue.php`, `services.php`, `session.php` | Laravel (vygenerované) | štandardné nastavenia frameworku |

Kde sa používa `config('shop.*')`:

| Kľúč | Používa |
|---|---|
| `shipping_cost` | [`CartService::getCart`](../app/Services/CartService.php), [`OrderService::createOrder`](../app/Services/OrderService.php), [`pages/home`](../resources/views/pages/README.md) (výhody obchodu) |
| `default_country` | [`UserAddressController::create`](../app/Http/Controllers/UserAddressController.php), checkout (`data-default-country` → [`checkout.js`](../resources/js/modules/checkout.js)) |
| `max_cart_quantity` | [`Api/AddCartItemRequest`, `UpdateCartItemRequest`](../app/Http/Requests/Api/README.md), `CartService`; rovnaký limit `MAX_QUANTITY` v [`cart.js`](../resources/js/modules/cart.js) |
