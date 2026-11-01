> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Filament/Resources/Products/` – administrácia: Product

Produkty eshopu (názov, slug, popis, cena, sklad, obrázok, kategória).
Navigačná skupina **Eshop**. Všeobecný popis Filamentu a toku: [`app/Filament/README.md`](../../README.md).

| Súbor | Úloha |
|---|---|
| [`ProductResource.php`](./ProductResource.php) | spája model [`Product`](../../../Models/Product.php), formulár, tabuľku a stránky |
| [`Schemas/ProductForm.php`](Schemas/ProductForm.php) | polia formulára a ich validácia |
| [`Tables/ProductsTable.php`](Tables/ProductsTable.php) | stĺpce, filtre a akcie zoznamu |
| [`Pages/ListProducts.php`](Pages/ListProducts.php) | stránka panelu: zoznam |
| [`Pages/CreateProduct.php`](Pages/CreateProduct.php) | stránka panelu: vytvorenie |
| [`Pages/EditProduct.php`](Pages/EditProduct.php) | stránka panelu: úprava |

**Autorizácia:** [`ProductPolicy`](../../../Policies/ProductPolicy.php) – oprávnenie `manage products` (trait `AuthorizesByPermission`).

**Prepojenie s eshopom:** Produkt sa na webe zobrazí v [`products/index`](../../../../resources/views/pages/README.md) a detaile, len ak je jeho kategória aktívna (`Product::visible()`).
