> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Http/Controllers/Api/` – controllery pre AJAX

**Teória.** Pri asynchrónnej komunikácii nevracia controller HTML, ale **JSON**. JavaScript
([`resources/js/services/`](../../../../resources/js/services/README.md)) pošle `fetch` požiadavku,
controller odpovie dátami a JS prekreslí časť stránky bez jej obnovenia. Vrstvy pod controllerom
(Form Request, Service, Model) sú **tie isté ako pri webových stránkach** – mení sa len tvar odpovede.

Routy sú vo [`web.php`](../../../../routes/web.php) pod prefixom `/api` (nie v `api.php`), aby mali
**session a CSRF** – košík je uložený v session a POST požiadavky musia mať CSRF token (posiela ho
[`api.js`](../../../../resources/js/services/api.js) z `<meta name="csrf-token">`). Limit `throttle:60,1`.

## Triedy

| Controller | Endpoint | Vstup (Form Request) | Volá | Odpoveď |
|---|---|---|---|---|
| [`CartController`](CartController.php) | `GET /api/cart` | – | [`CartService::getCart`](../../../Services/CartService.php) | JSON košíka |
| | `POST /api/cart/add` | [`AddCartItemRequest`](../../Requests/Api/AddCartItemRequest.php) | `CartService::addProduct` | `{success, message, cart}` |
| | `POST /api/cart/update` | [`UpdateCartItemRequest`](../../Requests/Api/UpdateCartItemRequest.php) | `CartService::updateQuantity` | `{success, message, cart}` |
| | `POST /api/cart/remove` | [`RemoveCartItemRequest`](../../Requests/Api/RemoveCartItemRequest.php) | `CartService::removeProduct` | `{success, message, cart}` |
| | `POST /api/cart/clear` | – | `CartService::clear` | `{success, message, cart}` |
| [`ProductApiController`](ProductApiController.php) | `GET /api/products/search?q=` | [`ProductSearchRequest`](../../Requests/Api/ProductSearchRequest.php) | [`ProductService::search`](../../../Services/ProductService.php) | pole [`ProductResource`](../../Resources/ProductResource.php) |
| | `GET /api/products/{slug}` | – (route model binding) | model [`Product`](../../../Models/Product.php), neviditeľný = 404 | [`ProductDetailResource`](../../Resources/ProductDetailResource.php) |

`CartController::respond()` je spoločná metóda: vykoná akciu, pri
[`CartException`](../../../Exceptions/README.md) vráti `{success:false, message}` so stavom z výnimky
(napr. 422 „Not enough items in stock.“), inak `{success:true, message, cart}`.

## Kto volá tieto endpointy

| Endpoint | JS modul | Spúšťač v HTML |
|---|---|---|
| `/api/cart/*` | [`cart.js`](../../../../resources/js/modules/cart.js) cez [`cart-service.js`](../../../../resources/js/services/cart-service.js) | `[data-add-to-cart]`, `[data-update-cart]`, `[data-remove-from-cart]`, `[data-clear-cart]`, `[data-cart-quantity]` |
| `/api/products/search` | [`search.js`](../../../../resources/js/modules/search.js) cez [`product-service.js`](../../../../resources/js/services/product-service.js) | písanie do `#search-input` v hlavičke |
| `/api/products/{slug}` | [`quickview.js`](../../../../resources/js/modules/quickview.js) | `[data-quick-view]` na karte produktu |

Pri chybe validácie vráti Laravel automaticky JSON `422 {message, errors}` – `api.js` z neho vytvorí
`Error` a modul zobrazí hlášku cez Toast.
