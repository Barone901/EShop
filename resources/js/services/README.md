> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/js/services/` – komunikácia so serverom (AJAX)

Jediné miesto, kde JS volá server. Moduly nevolajú `fetch` priamo, ale tieto služby – adresy
endpointov a spracovanie chýb sú tak na jednom mieste. Teória: [`resources/js/README.md`](../README.md).

> Pozor na rovnaký názov: `CartService` v JS (tu) len posiela požiadavky; logiku košíka má
> [`app/Services/CartService.php`](../../../app/Services/CartService.php) na serveri.

| Súbor | Metódy | Endpoint (server) | Volajú |
|---|---|---|---|
| [`api.js`](api.js) `ApiService` | `request()`, `get(endpoint, params, options)`, `post(endpoint, data, options)` | prefix `/api`; hlavičky `Accept: application/json`, `X-CSRF-TOKEN` (z `<meta name="csrf-token">` v layoute), `X-Requested-With` | `cart-service.js`, `product-service.js` |
| [`cart-service.js`](cart-service.js) `CartService` | `getCart()`, `addProduct()`, `updateQuantity()`, `removeProduct()`, `clear()` | `GET /api/cart`, `POST /api/cart/add\|update\|remove\|clear` → [`Api/CartController`](../../../app/Http/Controllers/Api/README.md) | [`cart.js`](../modules/cart.js) |
| [`product-service.js`](product-service.js) `ProductService` | `search(query, {signal})`, `getDetail(slug)` | `GET /api/products/search?q=`, `GET /api/products/{slug}` → [`ProductApiController`](../../../app/Http/Controllers/Api/ProductApiController.php) | [`search.js`](../modules/search.js), [`quickview.js`](../modules/quickview.js) |
| [`index.js`](index.js) | spoločný export `{Api, Product, Cart}` | – | [`eshop.js`](../eshop.js) (`window.Eshop.Services`) |

**Spracovanie chýb v `api.js`:** odpoveď, ktorá nie je `2xx` alebo nie je platný JSON, sa zmení na
`Error` s `message` zo servera (napr. „Not enough items in stock.“), pri `419` (vypršaná session)
na „Your session has expired…“. Modul ju zachytí v `try/catch` a zobrazí cez [`toast.js`](../modules/toast.js).
