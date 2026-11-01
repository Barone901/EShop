> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Models/` – modely (M v MVC)

**Teória.** Model reprezentuje jednu tabuľku databázy a pravidlá nad jej dátami. Laravel používa
**Eloquent ORM**: riadok tabuľky = objekt, vzťahy sú metódy (`$order->items`), dotazy sa skladajú
metódami (`Product::visible()->search($q)`) a Eloquent ich prekladá na SQL s **parametrami (prepared
statements)** – vstup používateľa sa nikdy nevkladá do SQL ako text, preto je aplikácia chránená proti
SQL injection. Celý tok: [`app/README.md`](../README.md).

Prvky modelu v projekte:
- **vzťahy** – `belongsTo` (N:1), `hasMany` (1:N), `hasOne`
- **scopes** – opakovane použiteľné podmienky dotazu (`scopeVisible` → `Product::visible()`)
- **accessory** – vypočítané atribúty (`getFormattedPriceAttribute` → `$product->formatted_price`)
- **casts** – typy stĺpcov (enum, decimal, boolean, `hashed` heslo)
- **doménové metódy** – pravidlá stavu (`Order::isCancellable()`, `UserAddress::makeDefault()`)

Model **nevolá** controller ani view. Používajú ho [služby](../Services/README.md), controllery,
[Filament](../Filament/README.md) a [seedery](../../database/seeders/README.md). Tabuľky vytvárajú
[migrácie](../../database/migrations/README.md).

## Vzťahy (ER diagram)

```
users 1───N orders 1───N order_items N───1 products N───1 categories ┐
  │            │                                          (parent_id) └─1:N sama na seba (podkategórie)
  │            └──1───N order_addresses   (snapshot adresy pri objednávke)
  ├──1───N user_addresses                 (uložené adresy zákazníka)
  └──1───N pages (author_id) 1───N banners (page_id)
```

## Triedy

| Model | Tabuľka | Vzťahy | Dôležité metódy / atribúty | Používa |
|---|---|---|---|---|
| [`User`](User.php) | `users` | `orders()` 1:N, `addresses()` 1:N | `full_name`, `addressesOfType()`, `default_shipping_address`, `default_billing_address`, `canAccessPanel()` (rola → admin), heslo cast `hashed`, role cez Spatie `HasRoles` | [`Auth/`](../Http/Controllers/Auth/README.md), [`DashboardController`](../Http/Controllers/DashboardController.php), [`OrderController`](../Http/Controllers/OrderController.php) |
| [`Category`](Category.php) | `categories` | `parent()` N:1, `children()` 1:N (sama na seba), `products()` 1:N | scopes `parents()`, `active()`, `ordered()`; `image_src`; routa podľa `slug` | [`CategoryService`](../Services/CategoryService.php), [`ProductController`](../Http/Controllers/ProductController.php) |
| [`Product`](Product.php) | `products` | `category()` N:1, `orderItems()` 1:N | scopes `visible()` (aktívna kategória), `inStock()`, `search()` (escapuje `%` a `_` v LIKE); `formatted_price`, `image_src`, `is_in_stock`, `is_low_stock`, `isVisible()` | [`ProductService`](../Services/ProductService.php), [`CartService`](../Services/CartService.php), [`OrderService`](../Services/OrderService.php), [Resources](../Http/Resources/README.md) |
| [`Order`](Order.php) | `orders` | `user()` N:1, `items()` 1:N, `addresses()` 1:N, `shippingAddress()` / `billingAddress()` hasOne | `generateOrderNumber()`, `isCancellable()`, `isCancelled()`, `isDeletable()`, `progressStep()`, formátované sumy; stavy ako [enumy](../Enums/README.md) | [`OrderService`](../Services/OrderService.php), views `orders/*` |
| [`OrderItem`](OrderItem.php) | `order_items` | `order()` N:1, `product()` N:1 | snapshot `product_name` a `price` z času nákupu; `formatted_price`, `formatted_subtotal` | `OrderService::createOrder`, `orders/show` |
| [`OrderAddress`](OrderAddress.php) | `order_addresses` | `order()` N:1 | `attributesFromCheckout()` – adresa z checkout formulára; trait `HasAddressFormatting` | `OrderService::createOrder`, komponent `x-shop.order-address` |
| [`UserAddress`](UserAddress.php) | `user_addresses` | `user()` N:1 | `type_label`, `makeDefault()` (zruší predvolenú pri ostatných adresách typu); trait `HasAddressFormatting` | [`UserAddressController`](../Http/Controllers/UserAddressController.php), checkout |
| [`Page`](Page.php) | `pages` (soft delete) | `author()` N:1 → `User` | scopes `published()`, `featured()`, `ordered()`; automatický unikátny `slug`; `safe_content` (sanitizované HTML z admina); `incrementViews()`; `url` | [`PageService`](../Services/PageService.php), [`PageController`](../Http/Controllers/PageController.php) |
| [`Banner`](Banner.php) | `banners` | `page()` N:1 | scopes `active()`, `ordered()`; `link` (vlastná URL alebo článok), `image_url` | [`BannerService`](../Services/BannerService.php), slider na homepage |

[`Concerns/HasAddressFormatting`](Concerns/HasAddressFormatting.php) – trait spoločný pre `UserAddress`
a `OrderAddress` (atribút `full_name` z mena a priezviska), aby sa kód adries neopakoval.

## Prečo sú niektoré dáta „zdvojené“ (snapshot)
`order_addresses` a `order_items.product_name/price` kopírujú hodnoty v čase objednávky. Je to zámer,
nie duplicita: objednávka je historický záznam – keď zákazník zmení adresu alebo admin zmení cenu
produktu, stará objednávka musí ostať taká, aká bola.
