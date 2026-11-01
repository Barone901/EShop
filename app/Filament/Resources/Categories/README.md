> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Filament/Resources/Categories/` – administrácia: Category

Kategórie produktov s hierarchiou (nadradená kategória), poradím a príznakom aktívnosti.
Navigačná skupina **Eshop**. Všeobecný popis Filamentu a toku: [`app/Filament/README.md`](../../README.md).

| Súbor | Úloha |
|---|---|
| [`CategoryResource.php`](./CategoryResource.php) | spája model [`Category`](../../../Models/Category.php), formulár, tabuľku a stránky |
| [`Schemas/CategoryForm.php`](Schemas/CategoryForm.php) | polia formulára a ich validácia |
| [`Tables/CategoriesTable.php`](Tables/CategoriesTable.php) | stĺpce, filtre a akcie zoznamu |
| [`Pages/ListCategories.php`](Pages/ListCategories.php) | stránka panelu: zoznam |
| [`Pages/CreateCategory.php`](Pages/CreateCategory.php) | stránka panelu: vytvorenie |
| [`Pages/EditCategory.php`](Pages/EditCategory.php) | stránka panelu: úprava |

**Autorizácia:** [`CategoryPolicy`](../../../Policies/CategoryPolicy.php) – oprávnenie `manage categories` (trait `AuthorizesByPermission`).

**Prepojenie s eshopom:** Neaktívna kategória skryje na webe seba aj svoje produkty ([`CategoryController`](../../../Http/Controllers/CategoryController.php) vráti 404).
