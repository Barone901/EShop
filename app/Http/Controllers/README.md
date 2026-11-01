> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Http/Controllers/` – webové controllery (C v MVC)

**Teória.** Controller je „dispečer“: prijme požiadavku z routy, odovzdá prácu službe alebo modelu
a rozhodne o odpovedi (HTML view, presmerovanie, JSON). Sám neobsahuje biznis logiku ani SQL a
nevaliduje vstup (to robí [Form Request](../Requests/README.md)). Celý tok: [`app/README.md`](../../README.md).

Konvencie v projekte: závislosti cez konštruktor (`private readonly`), dáta do view poľom,
validácia vo Form Requeste, nenájdené záznamy `?? abort(404)`, presmerovanie `redirect()->route()`.

## Triedy

| Controller | Routy ([`web.php`](../../../routes/web.php)) | Volá | Odpoveď |
|---|---|---|---|
| [`Controller`](Controller.php) | – | – | abstraktný predok všetkých controllerov |
| [`HomeController`](HomeController.php) | `GET /` `index` | [`CategoryService::getParentCategories`](../../Services/CategoryService.php), [`BannerService::getActiveBanners`](../../Services/BannerService.php) | view [`pages/home`](../../../resources/views/pages/README.md) |
| [`CategoryController`](CategoryController.php) | `GET /category/{category:slug}` `show` | rovnaké služby ako Home; neaktívna kategória = 404 | view `pages/home` + `category` |
| [`ProductController`](ProductController.php) | `GET /products` `index`, `GET /product/{product:slug}` `show` | [`ProductService::getProducts`](../../Services/ProductService.php), model [`Category`](../../Models/Category.php); vstup [`ProductIndexRequest`](../Requests/ProductIndexRequest.php) | views `pages/products/index`, `pages/products/show` |
| [`PageController`](PageController.php) | `GET /pages`, `GET /pages/{slug}` | [`PageService`](../../Services/PageService.php); počíta zobrazenie raz za session | views `pages/articles/index`, `show` |
| [`ContactController`](ContactController.php) | `GET /contact`, `POST /contact` (throttle 5/min) | vstup [`ContactRequest`](../Requests/ContactRequest.php) | view `pages/contact/index`, redirect s `success` |
| [`OrderController`](OrderController.php) | `GET /checkout`, `POST /orders`, `GET /orders/confirmation/{n}`, *(auth)* `GET /orders`, `GET /orders/{n}`, `POST /orders/{n}/cancel`, `DELETE /orders/{n}` | [`OrderService`](../../Services/OrderService.php) (vytvorenie, vyhľadanie, zrušenie, zmazanie), [`CartService`](../../Services/CartService.php); vstup [`CheckoutRequest`](../Requests/CheckoutRequest.php); zachytáva [`OrderException`](../../Exceptions/README.md) | views `pages/checkout/*`, `pages/orders/*`, redirecty s `success`/`error` |
| [`UserAddressController`](UserAddressController.php) | *(auth)* resource `addresses` (index, create, store, edit, update, destroy) + `POST /addresses/{a}/default` | model [`UserAddress`](../../Models/UserAddress.php), autorizácia [`UserAddressPolicy`](../../Policies/README.md) cez `Gate::authorize`; vstup [`UserAddressRequest`](../Requests/UserAddressRequest.php) | views `pages/addresses/*`, redirect na `addresses.index` |
| [`DashboardController`](DashboardController.php) | *(auth)* `GET /dashboard` (invokable) | vzťahy modelu [`User`](../../Models/User.php) (posledné objednávky, predvolené adresy) | view [`profile/dashboard`](../../../resources/views/profile/README.md) |
| [`ProfileController`](ProfileController.php) | *(auth)* `GET/PATCH/DELETE /profile` | model `User`; vstup [`ProfileUpdateRequest`](../Requests/ProfileUpdateRequest.php) | redirect na dashboard (`#profile`), po zmazaní účtu na `home` |

Podpriečinky:
- [`Api/`](Api/README.md) – controllery pre AJAX (JSON odpovede pre JavaScript)
- [`Auth/`](Auth/README.md) – prihlásenie, registrácia, overenie e-mailu (Laravel Breeze)

## Príklad: zrušenie objednávky

```
orders/show.blade.php  <form data-confirm method=POST action=orders.cancel>   (potvrdenie: page-actions.js)
  └► POST /orders/{n}/cancel  (middleware auth + CSRF)
       └► OrderController::cancel()
            ├► userOrder(): OrderService::findUserOrder() ?? abort(404)   (cudzia objednávka = 404)
            └► OrderService::cancelOrder()  ── transakcia: stav Cancelled + vrátenie kusov na sklad (Product)
                  └─ OrderException ──► redirect orders.show s 'error'
       ◄── redirect orders.show s 'success'  ──► GET ──► view s x-ui.flash-messages
```
