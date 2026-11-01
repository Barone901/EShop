> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Http/Requests/Api/` – validácia AJAX požiadaviek

Rovnaký princíp ako [`Requests/`](../README.md), ale volajúci je JavaScript: pri chybe Laravel vráti
**JSON 422** `{message, errors}` namiesto presmerovania. [`api.js`](../../../../resources/js/services/api.js)
z odpovede vytvorí chybu a modul zobrazí `message` v Toaste. Jeden request = jedna akcia (konvencia Laravelu).

| Request | Endpoint → controller | Pravidlá |
|---|---|---|
| [`AddCartItemRequest`](AddCartItemRequest.php) | `POST /api/cart/add` → [`CartController::add`](../../Controllers/Api/CartController.php) | `product_id` povinné celé číslo, existuje v `products`; `quantity` 1 – `shop.max_cart_quantity` |
| [`UpdateCartItemRequest`](UpdateCartItemRequest.php) | `POST /api/cart/update` → `CartController::update` | `product_id` celé číslo; `quantity` 0 – max (0 = odstrániť) |
| [`RemoveCartItemRequest`](RemoveCartItemRequest.php) | `POST /api/cart/remove` → `CartController::remove` | `product_id` celé číslo |
| [`ProductSearchRequest`](ProductSearchRequest.php) | `GET /api/products/search` → [`ProductApiController::search`](../../Controllers/Api/ProductApiController.php) | `q` reťazec max. 255 znakov |

Limit množstva je v [`config/shop.php`](../../../../config/README.md); ten istý limit kontroluje aj
`cart.js` (`MAX_QUANTITY`), takže klient aj server pustia rovnaké hodnoty.
Či je produkt na sklade, sa nekontroluje tu, ale v [`CartService`](../../../Services/CartService.php)
(je to biznis pravidlo, nie formát vstupu) → [`CartException`](../../../Exceptions/README.md).
