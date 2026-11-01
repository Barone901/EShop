> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/js/modules/` – moduly správania stránky

Každý modul je objekt s metódou `init()`, ktorú zavolá [`eshop.js`](../eshop.js). Modul nájde svoje
prvky v HTML (podľa `id` / `data-*`); ak na stránke nie sú, nič nerobí. Teória: [`resources/js/README.md`](../README.md).

| Modul | Väzba v HTML (kde) | Čo robí | Komunikácia so serverom |
|---|---|---|---|
| [`cart.js`](cart.js) | `[data-add-to-cart]` ([`product-card`](../../views/components/shop/product-card.blade.php), detail produktu, quick view), `#cart-count`, `#cart-dropdown-content` ([hlavička](../../views/layouts/partials/header.blade.php)), `#cart-page-content` ([`pages/cart`](../../views/pages/cart/index.blade.php)) | pridanie, zmena množstva, odstránenie, vyprázdnenie; prekreslí počet, dropdown aj stránku košíka | **AJAX** [`cart-service.js`](../services/cart-service.js) → `/api/cart/*` → [`Api/CartController`](../../../app/Http/Controllers/Api/README.md) |
| [`search.js`](search.js) | `#search-input` + `#search-results` (desktop aj mobil, hlavička) | živé vyhľadávanie: debounce 300 ms, min. 2 znaky, zrušenie starej požiadavky (`AbortController`) | **AJAX** [`product-service.js`](../services/product-service.js) → `/api/products/search` → [`ProductResource`](../../../app/Http/Resources/README.md) |
| [`quickview.js`](quickview.js) | `[data-quick-view]` na karte produktu; modal `#quick-view-modal` si vytvorí sám | rýchly náhľad produktu v modálnom okne, fokus, Escape | **AJAX** `product-service.js` → `/api/products/{slug}` → `ProductDetailResource` |
| [`form-validator.js`](form-validator.js) | `form[data-validate]` (prihlásenie, registrácia, checkout, adresy, kontakt, profil) | kontrola v prehliadači podľa atribútov `required`, `type=email`, `minlength`, `maxlength`, `pattern` a `data-rules`; chyba = `aria-invalid` + `p.validation-error` | žiadna – server kontroluje znova ([Form Requests](../../../app/Http/Requests/README.md)) |
| [`checkout.js`](checkout.js) | `#checkout-form`, `#same-as-shipping`, `#shipping-address-select`, `#billing-address-select`, `#submit-order-btn` ([`pages/checkout`](../../views/pages/checkout/index.blade.php)) | predvyplnenie z uložených adries, kopírovanie doručovacej do fakturačnej, stav „odosiela sa“ | formulár sa odošle klasicky (POST `/orders`) |
| [`menu.js`](menu.js) | `#account-btn`, `#mobile-menu-btn`, `#cart-wrapper`… (hlavička) | rozbaľovacie menu účtu, mobilné menu, dropdown košíka; `aria-expanded`, Escape, klik mimo | – |
| [`banner-slider.js`](banner-slider.js) | `#banner-slider`, `[data-banner-*]` ([`banner-slider`](../../views/components/shop/banner-slider.blade.php)) | automatické posúvanie, šípky, bodky, klávesnica, swipe | – |
| [`page-actions.js`](page-actions.js) | `form[data-confirm]`, `select[data-navigate-on-change]`, `[data-copy-url]` | potvrdenie pred zmazaním/zrušením, radenie produktov, kopírovanie odkazu | – |
| [`toast.js`](toast.js) | vytvára `#toast-container` | krátke notifikácie (úspech / chyba / info) | – (volajú ho iné moduly) |

## Príklad toku: zmena množstva na stránke košíka

```
<input data-cart-quantity="5">  change
  └► cart.js  delegate('change', '[data-cart-quantity]')  →  Cart.update(5, qty)
       └► CartService.updateQuantity()  →  ApiService.post('/cart/update', {product_id, quantity})
            └► POST /api/cart/update  →  UpdateCartItemRequest  →  Api\CartController::update
                 →  app/Services/CartService::updateQuantity  (session; sklad → CartException 422)
       ◄── {success, message, cart}  →  Cart.updateUI(cart)  →  renderCartPage()  +  Toast.success()
       ◄── chyba                     →  Toast.error(message)  +  Cart.refresh()  (vráti správne číslo)
```
