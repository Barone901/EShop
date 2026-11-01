> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Http/Resources/` – JSON reprezentácia modelov

**Teória.** API Resource je „view pre JavaScript“: rovnako ako Blade view určuje, ako vyzerá HTML,
Resource určuje, **aké polia a v akom tvare** dostane JS. Model sa tak neposiela celý (žiadne interné
stĺpce) a JS dostane hotové hodnoty (napr. naformátovanú cenu, URL obrázka a detailu).
[`AppServiceProvider`](../../Providers/README.md) vypína obal `{"data": ...}` (`JsonResource::withoutWrapping()`).

| Resource | Vracia ho | Polia | Spracuje ho v JS |
|---|---|---|---|
| [`ProductResource`](ProductResource.php) | `GET /api/products/search` ([`ProductApiController::search`](../Controllers/Api/ProductApiController.php)) | `id, name, slug, price, formatted_price, image_url, category` (názov), `in_stock, url` | [`search.js`](../../../resources/js/modules/search.js) → `renderResults()` |
| [`ProductDetailResource`](ProductDetailResource.php) | `GET /api/products/{slug}` (`ProductApiController::show`) | navyše `description, stock, low_stock`, `category` ako objekt `{id, name, slug}` | [`quickview.js`](../../../resources/js/modules/quickview.js) → `render()` (`stockBadge()` z [`templates.js`](../../../resources/js/utils/templates.js)) |

Hodnoty berú z accessorov modelu [`Product`](../../Models/Product.php) (`formatted_price`, `image_src`,
`is_in_stock`, `is_low_stock`), takže web aj API počítajú rovnako.
