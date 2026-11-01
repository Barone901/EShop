> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/views/components/` – Blade komponenty

**Teória.** Komponent je znovupoužiteľný kúsok šablóny s parametrami (`@props`). Súbor
`components/shop/product-card.blade.php` sa použije ako `<x-shop.product-card :product="$product" />`.
Atribúty, ktoré nie sú v `@props`, sa prenesú na hlavný prvok (`$attributes`). Komponenty zaručujú,
že rovnaký prvok vyzerá a správa sa všade rovnako (bez opakovania kódu).
Teória views: [`resources/views/README.md`](../README.md).

## Breeze komponenty (koreň priečinka, prevzaté z Laravel Breeze a upravené)

| Komponent | Úloha |
|---|---|
| [`button`](button.blade.php) | `<x-button variant size href>` – tlačidlo/odkaz dizajnového systému (primary, secondary, ghost, danger, dark) |
| [`text-input`](text-input.blade.php), [`input-label`](input-label.blade.php), [`input-error`](input-error.blade.php) | pole, popisok a chyba (zo `$errors`) vo formulároch auth a profilu |
| [`auth-session-status`](auth-session-status.blade.php) | hláška zo session (napr. odoslaný overovací e-mail) |
| [`modal`](modal.blade.php) | modálne okno (Alpine.js, udalosti `open-modal` / `close-modal`) – potvrdenie zmazania účtu |

## `form/` – formuláre
| [`form/input`](form/input.blade.php) | `<x-form.input name label ...>` – label + pole (aj textarea/select) + chyba zo servera (`aria-invalid`); atribúty `required`, `pattern`… z neho číta [`form-validator.js`](../../js/modules/form-validator.js) |
|---|---|

## `ui/` – všeobecné UI
| Komponent | Úloha | Používa sa |
|---|---|---|
| [`ui/card`](ui/card.blade.php) | biela karta, `padding`, `interactive` | adresy, objednávky, kontakt |
| [`ui/badge`](ui/badge.blade.php) | stavový štítok (neutral/ok/warn/danger/accent/dark) | základ pre `shop/stock-badge`, `shop/order-status-badge` |
| [`ui/page-header`](ui/page-header.blade.php) | h1 stránky + podnadpis + akcie | väčšina stránok |
| [`ui/section-header`](ui/section-header.blade.php) | h2 sekcie s pruhom a odkazom | home, detail produktu, články, dashboard |
| [`ui/flash-messages`](ui/flash-messages.blade.php) | hlášky `success` / `error` zo session (po redirecte z controllera) | stránky s formulármi |
| [`ui/loading`](ui/loading.blade.php) | indikátor načítavania | stránka košíka pred odpoveďou API |
| [`ui/article-card`](ui/article-card.blade.php) | karta článku (`.article-card`) | výpis a súvisiace články |

## `shop/` – prvky obchodu
| Komponent | Dáta | Väzba na JS / server |
|---|---|---|
| [`shop/product-card`](shop/product-card.blade.php) | `Product` | `[data-add-to-cart]` → [`cart.js`](../../js/modules/cart.js), `[data-quick-view]` → [`quickview.js`](../../js/modules/quickview.js) |
| [`shop/stock-badge`](shop/stock-badge.blade.php) | `Product` (`is_in_stock`, `is_low_stock`) | rovnaké stavy v JS: `stockBadge()` v [`templates.js`](../../js/utils/templates.js) |
| [`shop/order-status-badge`](shop/order-status-badge.blade.php) | `Order` (stav z [`OrderStatus`](../../../app/Enums/README.md)) | – |
| [`shop/order-address`](shop/order-address.blade.php), [`shop/address-card`](shop/address-card.blade.php) | `OrderAddress`, `UserAddress` | karta adresy má formuláre „nastaviť predvolenú“ a „zmazať“ (`data-confirm` → [`page-actions.js`](../../js/modules/page-actions.js)) |
| [`shop/checkout-steps`](shop/checkout-steps.blade.php) | aktuálny krok 1–3 | košík → checkout → potvrdenie |
| [`shop/banner-slider`](shop/banner-slider.blade.php) | `Banner[]` | `#banner-slider`, `[data-banner-*]` → [`banner-slider.js`](../../js/modules/banner-slider.js) |
| [`shop/categories-grid`](shop/categories-grid.blade.php) | `Category[]` | odkazy na `category.show` |
