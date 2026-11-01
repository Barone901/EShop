> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `database/migrations/` – štruktúra databázy

**Teória.** Migrácia je verziovaný popis zmeny databázy v PHP (`up()` vytvorí, `down()` vráti späť).
Databáza sa tak dá kedykoľvek vytvoriť rovnako (`artisan migrate`), zmeny sú v gite a nikto neupravuje
tabuľky ručne. Tabuľky zodpovedajú [modelom](../../app/Models/README.md) (vrstva M).

| Migrácia | Tabuľka | Kľúče a väzby | Model |
|---|---|---|---|
| `0001_01_01_000000_create_users_table` | `users`, `password_reset_tokens`, `sessions` | `email` unique | [`User`](../../app/Models/User.php) (Laravel) |
| `0001_01_01_000001_create_cache_table`, `…000002_create_jobs_table` | `cache`, `jobs`… | – | Laravel (vygenerované) |
| `2026_10_09_172937_create_permission_tables` | `roles`, `permissions`, pivoty | – | Spatie Permission (vygenerované) |
| `2026_10_11_135210_create_categories_table` | `categories` | `slug` unique, `parent_id` → categories (null on delete) | [`Category`](../../app/Models/Category.php) |
| `2026_10_11_135831_create_products_table` | `products` | `slug` unique, `category_id` → categories (cascade) | [`Product`](../../app/Models/Product.php) |
| `2026_10_19_175412_create_orders_table` | `orders` | `order_number` unique, `user_id` → users (null on delete; hosť nemá používateľa) | [`Order`](../../app/Models/Order.php) |
| `2026_10_19_180133_create_order_items_table` | `order_items` | `order_id` → orders (cascade), `product_id` → products (null on delete; snapshot názvu a ceny ostane) | [`OrderItem`](../../app/Models/OrderItem.php) |
| `2026_10_19_181208_create_order_addresses_table` | `order_addresses` | `order_id` → orders (cascade) | [`OrderAddress`](../../app/Models/OrderAddress.php) |
| `2026_10_21_184417_create_user_addresses_table` | `user_addresses` | `user_id` → users (cascade) | [`UserAddress`](../../app/Models/UserAddress.php) |
| `2026_10_22_175006_create_pages_table` | `pages` | `slug` unique, `author_id` → users (null on delete), soft delete | [`Page`](../../app/Models/Page.php) |
| `2026_10_22_221903_create_banners_table` | `banners` | `page_id` → pages (null on delete) | [`Banner`](../../app/Models/Banner.php) |
| `2026_10_27_170952_set_default_country_to_slovakia` | úprava | predvolená krajina adries | – |

**Cudzie kľúče** strážia integritu priamo v databáze: `cascade` zmaže závislé riadky (zmazanie
objednávky zmaže jej položky a adresy – na tom stavia [`OrderService::deleteOrder`](../../app/Services/OrderService.php)),
`null on delete` väzbu len odpojí (zmazaný produkt nezmaže históriu objednávok).
ER diagram vzťahov: [`app/Models/README.md`](../../app/Models/README.md).
