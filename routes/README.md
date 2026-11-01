> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `routes/` – routy (vstupná brána aplikácie)

**Teória.** Routa je pravidlo „**HTTP metóda + URL → kód**“. Je to prvé miesto, kam po spustení
frameworku príde požiadavka. Routa určí controller a metódu, **middleware** (filtre pred controllerom)
a **názov** (`->name('orders.show')`), cez ktorý sa URL generuje v kóde: `route('orders.show', $n)` –
URL sa tak dá zmeniť na jednom mieste. Celý tok: [`app/README.md`](../app/README.md).

| Súbor | Obsah |
|---|---|
| [`web.php`](web.php) | všetky stránky eshopu, AJAX API (`/api/...`), účet zákazníka; na konci `require auth.php` |
| [`auth.php`](auth.php) | prihlásenie, registrácia, odhlásenie, heslo, overenie e-mailu (Laravel Breeze) |
| [`console.php`](console.php) | Artisan príkazy (len vzorový `inspire` z Laravelu) |

Všetky routy majú skupinu middleware **`web`** (session, cookies, CSRF ochrana). Administráciu `/admin`
registruje Filament sám ([`AdminPanelProvider`](../app/Providers/README.md)). Výpis všetkých rout:
`./vendor/bin/sail artisan route:list`.

## Mapa `web.php`

| Skupina | URL | Middleware | Controller |
|---|---|---|---|
| Verejné stránky | `/`, `/about`, `/products`, `/category/{slug}`, `/product/{slug}`, `/cart`, `/contact`, `/pages`, `/pages/{slug}` | – (kontakt POST: `throttle:5,1`) | [`HomeController`, `ProductController`, `CategoryController`, `ContactController`, `PageController`](../app/Http/Controllers/README.md); `/about` a `/cart` sú `Route::view` (len šablóna) |
| AJAX API | `/api/products/search`, `/api/products/{slug}`, `/api/cart`, `/api/cart/add\|update\|remove\|clear` | `throttle:60,1` | [`Api/ProductApiController`, `Api/CartController`](../app/Http/Controllers/Api/README.md) |
| Nákup (aj host) | `/checkout`, `POST /orders`, `/orders/confirmation/{n}` | – | [`OrderController`](../app/Http/Controllers/OrderController.php) |
| Účet | `/dashboard`, `/profile`, `/orders`, `/orders/{n}` (+ cancel, delete), `/addresses` (resource) + `/addresses/{a}/default` | **`auth`** | `DashboardController`, `ProfileController`, `OrderController`, `UserAddressController` |

**Route model binding:** `{product:slug}` – Laravel sám nájde `Product` podľa `slug` a do controllera
pošle hotový model (neexistujúci = 404). `Route::resource('addresses', ...)` vytvorí 7 CRUD rout
(index, create, store, edit, update, destroy; `show` je vypnutá).

**Prečo je API vo `web.php`:** košík je v session a POST požiadavky z JS musia prejsť CSRF kontrolou –
oboje poskytuje skupina `web`. JS posiela token v hlavičke `X-CSRF-TOKEN`
([`api.js`](../resources/js/services/api.js)).
