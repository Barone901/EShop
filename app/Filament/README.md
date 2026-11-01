> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Filament/` – administrácia (Filament)

**Filament** (https://filamentphp.com) je framework na administrácie nad Laravelom. Súbory v tomto
priečinku vytvoril príkaz `php artisan make:filament-resource` a projekt ich **nakonfiguroval**
(polia formulárov, stĺpce tabuliek, akcie). Panel nastavuje [`AdminPanelProvider`](../Providers/README.md)
na adrese `/admin`. Teória: [`app/README.md`](../README.md).

## Ako Filament zapadá do MVC

Filament je samostatný MVC celok: **Resource** = controller + konfigurácia, **Schemas/Tables** = view
(formulár a tabuľka sa vykreslia z PHP konfigurácie), **Model** = ten istý Eloquent model ako na webe.

```
/admin/products ──► AdminPanelProvider (auth, canAccessPanel: rola Admin/Employee/Designer)
      └► ProductResource ──► ProductPolicy (oprávnenie 'manage products')
             ├► Pages/ListProducts  ─► Tables/ProductsTable  (zoznam, filtre, mazanie)
             ├► Pages/CreateProduct ─► Schemas/ProductForm   (formulár + validácia polí)
             └► Pages/EditProduct   ─► Schemas/ProductForm
                      └► Model Product ──► DB   (tie isté dáta zobrazuje eshop)
```

## Štruktúra jedného resource

| Súbor / priečinok | Úloha |
|---|---|
| `XxxResource.php` | spája model, formulár, tabuľku a stránky; navigácia (skupina `Eshop` / `Media`) |
| `Pages/` | stránky panelu: `ListXxx` (zoznam), `CreateXxx`, `EditXxx`, príp. `ViewXxx` |
| `Schemas/XxxForm.php` | polia formulára a ich validácia (required, max dĺžka, upload obrázka…) |
| `Tables/XxxTable.php` | stĺpce, filtre a akcie zoznamu |

## Resources (plné CRUD v administrácii)

| Resource | Model | Akcie | Policy | Dokumentácia |
|---|---|---|---|---|
| Products | [`Product`](../Models/Product.php) | vytvoriť, zoznam, upraviť, zmazať | [`ProductPolicy`](../Policies/README.md) | [`Resources/Products`](Resources/Products/README.md) |
| Categories | [`Category`](../Models/Category.php) | vytvoriť, zoznam, upraviť, zmazať | `CategoryPolicy` | [`Resources/Categories`](Resources/Categories/README.md) |
| Pages (články) | [`Page`](../Models/Page.php) | vytvoriť, zoznam, upraviť, zmazať, obnoviť (soft delete) | `PagePolicy` | [`Resources/Pages`](Resources/Pages/README.md) |
| Banners | [`Banner`](../Models/Banner.php) | vytvoriť, zoznam, upraviť, zmazať | `BannerPolicy` | [`Resources/Banners`](Resources/Banners/README.md) |
| Orders | [`Order`](../Models/Order.php) | zoznam, detail, úprava stavu, zrušenie (bez vytvorenia) | `OrderPolicy` | [`Resources/Orders`](Resources/Orders/README.md) |
