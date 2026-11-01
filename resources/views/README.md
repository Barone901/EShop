> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/views/` – šablóny Blade (V v MVC)

**Teória.** View je prezentačná vrstva: dostane **hotové dáta** z controllera
(`view('pages.orders.show', ['order' => $order])`) a vytvorí z nich HTML. Neobsahuje dotazy do
databázy ani biznis logiku – len zobrazenie, cykly a podmienky. Blade navyše **automaticky escapuje**
výpis `{{ $x }}` (ochrana proti XSS); `{!! !!}` sa používa len pre sanitizovaný obsah článkov.
Celý tok: [`app/README.md`](../../app/README.md).

Bodka v názve view = priečinok: `view('pages.products.index')` → `pages/products/index.blade.php`.

## Skladanie stránky

```
Controller ──view('pages.x', [...])──► pages/x.blade.php
                                          └► <x-layouts.default-layout>        (app/View/Components/Layouts/DefaultLayout.php)
                                               └► layouts/default.blade.php    (head, @vite CSS+JS, CSRF meta)
                                                    ├► @include layouts/partials/header   (+ account)
                                                    ├► {{ $slot }} = obsah stránky
                                                    │     └► komponenty <x-shop.product-card>, <x-ui.card>, <x-form.input>…
                                                    └► @include layouts/partials/footer
```

| Priečinok | Obsah | README |
|---|---|---|
| `layouts/` | jediný layout + hlavička, účet, pätička | [layouts](layouts/README.md) |
| `components/` | opakovane použiteľné kúsky UI (`<x-...>`) | [components](components/README.md) |
| `pages/` | stránky eshopu (jedna na routu) | [pages](pages/README.md) |
| `auth/` | prihlásenie, registrácia, overenie e-mailu (Breeze) | [auth](auth/README.md) |
| `profile/` | účet zákazníka (dashboard + formuláre profilu) | [profile](profile/README.md) |
| `pagination/` | [`shop.blade.php`](pagination/shop.blade.php) – stránkovanie v dizajne obchodu (`$products->links('pagination.shop')`), podľa Laravel view `pagination/tailwind` | – |

**Väzba na JavaScript:** views obsahujú `id` a `data-*` atribúty, na ktoré sa viažu
[JS moduly](../js/modules/README.md) (mapa v [`CLAUDE.md`](../../CLAUDE.md)).
