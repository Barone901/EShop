> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/` – aplikačná logika (MVC)

Toto je **centrálny dokument** architektúry. Ostatné `README.md` v priečinkoch popisujú svoju vrstvu
a odkazujú sem na teóriu.

## Teória: MVC v Laraveli

**MVC (Model – View – Controller)** rozdeľuje aplikáciu na tri vrstvy, ktoré o sebe vedia len to nutné:

| Vrstva | Zodpovednosť | Kde je v projekte |
|---|---|---|
| **Model** | dáta a pravidlá nad nimi (tabuľky, vzťahy, stavy, výpočty) | [`Models/`](Models/README.md), [`Enums/`](Enums/README.md) |
| **View** | prezentácia – HTML, ktoré vidí používateľ | [`resources/views/`](../resources/views/README.md) (+ CSS a JS v [`resources/`](../resources/js/README.md)) |
| **Controller** | prijme HTTP požiadavku, zavolá logiku, vyberie odpoveď (view, presmerovanie, JSON) | [`Http/Controllers/`](Http/Controllers/README.md) |

Laravel pridáva medzi ne ďalšie vrstvy, aby controller ostal tenký („thin controller“):

| Vrstva | Prečo existuje | Kde |
|---|---|---|
| **Routy** | mapujú URL + HTTP metódu na metódu controllera, pridávajú middleware | [`routes/`](../routes/README.md) |
| **Middleware** | filtre pred controllerom (`auth`, `guest`, `throttle`, CSRF, session) | Laravel, zapnuté v routách |
| **Form Request** | validácia a autorizácia vstupu ešte pred controllerom | [`Http/Requests/`](Http/Requests/README.md) |
| **Service** | biznis logika (košík, objednávka, výpis produktov) – controller ju len volá | [`Services/`](Services/README.md) |
| **Policy** | kto smie čo urobiť s modelom (vlastník adresy, oprávnenia admina) | [`Policies/`](Policies/README.md) |
| **API Resource** | tvar JSON odpovede pre JavaScript | [`Http/Resources/`](Http/Resources/README.md) |
| **Výnimky** | očakávané chyby logiky (málo kusov na sklade), ktoré controller preloží na odpoveď | [`Exceptions/`](Exceptions/README.md) |
| **Support** | malé pomocné triedy bez stavu (formát ceny, vzory vstupov) | [`Support/`](Support/README.md) |

Pravidlo smeru: **Controller → Service → Model → databáza**. Model nevolá controller, view nevolá databázu
(dáta dostane hotové), service nevracia HTML. Vďaka tomu sa dá napr. `CartService` použiť z webového
aj API controllera.

## Životný cyklus požiadavky (kam ide správa)

```
Prehliadač ──HTTP──► public/index.php ──► bootstrap/app.php (Laravel kernel)
                                              │
                                              ▼
                               routes/web.php (+ routes/auth.php)
                               nájde routu, spustí middleware
                                              │
                                              ▼
                       Form Request (validácia) ──chyba──► späť s chybami (422 / redirect back)
                                              │ OK
                                              ▼
                                     Controller (metóda)
                                              │ volá
                                              ▼
                                Service ──► Model (Eloquent) ──► MySQL
                                              │ vráti dáta
                                              ▼
                 Controller vyberie odpoveď:  view(...)   │ redirect()->route(...)  │ JSON
                                              ▼
                     Blade view + layout + komponenty ──► HTML ──► prehliadač
```

### Tok 1 – zobrazenie stránky (GET)
Príklad `GET /products?category=tools`:

1. [`routes/web.php`](../routes/web.php) → `ProductController@index`
2. Laravel vytvorí [`ProductIndexRequest`](Http/Requests/ProductIndexRequest.php) a overí `category`, `search`, `sort`
3. [`ProductController::index()`](Http/Controllers/ProductController.php) zavolá
   [`ProductService::getProducts()`](Services/ProductService.php)
4. Service poskladá dotaz cez scopes modelu [`Product`](Models/Product.php) (`visible()`, `search()`) → SQL do MySQL
5. Controller vráti `view('pages.products.index', [...])` →
   [`resources/views/pages/products/index.blade.php`](../resources/views/pages/products/index.blade.php)
6. View sa vloží do layoutu [`<x-layouts.default-layout>`](View/README.md) a použije komponenty (`x-shop.product-card`)

### Tok 2 – odoslanie formulára (POST → redirect → GET)
Príklad uloženie adresy `POST /addresses`:

1. Middleware `auth` (len prihlásený) + CSRF token z `@csrf`
2. [`UserAddressRequest`](Http/Requests/UserAddressRequest.php) validuje (pri chybe redirect späť, chyby v `$errors`)
3. [`UserAddressController::store()`](Http/Controllers/UserAddressController.php) uloží cez model [`UserAddress`](Models/UserAddress.php)
4. Odpoveď je **redirect** s flash správou `success` (vzor *Post/Redirect/Get* – obnovenie stránky formulár neodošle znova)
5. Prehliadač urobí `GET /addresses`, view zobrazí hlášku cez `x-ui.flash-messages`

Kontrola na strane klienta prebehne ešte pred odoslaním:
[`form-validator.js`](../resources/js/modules/README.md) číta HTML atribúty (`required`, `pattern`…), ktoré
generuje rovnaký zdroj ako server ([`InputPattern`](Support/InputPattern.php)).

### Tok 3 – AJAX (asynchrónna komunikácia)
Príklad pridanie do košíka bez obnovenia stránky:

```
klik [data-add-to-cart]                                     (view: product-card.blade.php)
  └► resources/js/modules/cart.js  Cart.add()
       └► resources/js/services/cart-service.js  CartService.addProduct()
            └► resources/js/services/api.js  fetch POST /api/cart/add  (X-CSRF-TOKEN, JSON)
                 └► routes/web.php  api.cart.add  (throttle:60,1)
                      └► AddCartItemRequest (validácia) → Api\CartController::add()
                           └► app/Services/CartService::addProduct()  → session
                      ◄── JSON { success, message, cart }
       ◄── cart.js updateUI(): počet v hlavičke, dropdown, stránka košíka + Toast
```

Ďalšie AJAX volania: živé vyhľadávanie (`GET /api/products/search` → [`ProductResource`](Http/Resources/ProductResource.php))
a quick view (`GET /api/products/{slug}` → [`ProductDetailResource`](Http/Resources/ProductDetailResource.php)).

### Tok 4 – administrácia (Filament)
`/admin` → [`AdminPanelProvider`](Providers/README.md) → [`User::canAccessPanel()`](Models/User.php) (rola) →
Filament Resource ([`Filament/`](Filament/README.md)) → [Policy](Policies/README.md) (oprávnenie) → Model → DB.
Filament je framework – formuláre a tabuľky v `Filament/Resources/*/Schemas|Tables` sú konfigurácia napísaná v projekte.

### Tok 5 – chyby
- Neplatný vstup → Form Request vráti chyby (web: redirect späť, API: JSON 422).
- Očakávaná chyba logiky → Service vyhodí [`CartException` / `OrderException`](Exceptions/README.md),
  controller ju zachytí a vráti flash `error` alebo JSON so stavom (napr. 422).
- Cudzia / neexistujúca objednávka alebo adresa → `404` / `403` (controller, Policy).

## Priečinky v `app/`

| Priečinok | Obsah |
|---|---|
| [`Enums/`](Enums/README.md) | stavy a typy (stav objednávky, typ adresy, rola) |
| [`Exceptions/`](Exceptions/README.md) | očakávané chyby košíka a objednávky |
| [`Filament/`](Filament/README.md) | administrácia (CRUD produktov, kategórií, článkov, bannerov, objednávok) |
| [`Http/`](Http/README.md) | controllery, form requesty, API resources |
| [`Models/`](Models/README.md) | Eloquent modely a vzťahy |
| [`Policies/`](Policies/README.md) | autorizácia |
| [`Providers/`](Providers/README.md) | nastavenie aplikácie pri štarte, admin panel |
| [`Services/`](Services/README.md) | biznis logika |
| [`Support/`](Support/README.md) | pomocné triedy |
| [`View/`](View/README.md) | trieda Blade layoutu |
