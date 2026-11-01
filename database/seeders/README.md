> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `database/seeders/` – ukážkové dáta

**Teória.** Seeder naplní databázu dátami (`artisan db:seed`). Používa tie isté [modely](../../app/Models/README.md)
ako aplikácia, takže dáta prejdú rovnakými pravidlami (casts, automatický slug…). Pri prvom spustení v
Dockeri ich spustí skript `docker/8.5/init-app` (pozri koreňový [`README.md`](../../README.md)).

Poradie spúšťa [`DatabaseSeeder`](DatabaseSeeder.php):

| # | Seeder | Vytvorí | Závisí od |
|---|---|---|---|
| 1 | [`RolesAndPermissionsSeeder`](RolesAndPermissionsSeeder.php) | oprávnenia (`manage products`, `manage orders`…) a roly z [`UserRole`](../../app/Enums/README.md) s priradenými oprávneniami | – |
| 2 | [`AdminUserSeeder`](AdminUserSeeder.php) | administrátor `admin@admin.com` | roly |
| 3 | [`DemoUserSeeder`](DemoUserSeeder.php) | používateľ pre každú rolu (napr. `customer@example.com`), heslo `password`, adresy zákazníka | roly |
| 4 | [`CatalogSeeder`](CatalogSeeder.php) | kategórie a produkty (náradie pre autoservisy) s obrázkami | – |
| 5 | [`ContentSeeder`](ContentSeeder.php) | články (publikované aj koncept) a bannery slidera | autor (používateľ) |
| 6 | [`OrderSeeder`](OrderSeeder.php) | objednávky zákazníka v rôznych stavoch (s položkami a adresami) | zákazník, produkty |

[`Concerns/CopiesDemoImages`](Concerns/CopiesDemoImages.php) – trait, ktorý skopíruje obrázok z
[`images/`](images) na disk `public` a vráti cestu do DB (rovnako ako upload v administrácii).

Oprávnenia rolí potom vyhodnocujú [Policies](../../app/Policies/README.md) v administrácii.
