> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Enums/` – stavy a typy (PHP enumy)

**Teória.** Enum je pevne daný zoznam hodnôt. Namiesto „magických reťazcov“ (`'pending'`) sa v kóde
používa `OrderStatus::Pending` – preklep sa zistí hneď, IDE napovedá a správanie stavu (popisok,
farba, či sa dá zrušiť) je na jednom mieste. V databáze je uložená reťazcová hodnota (`string`-backed
enum), model ju pretypuje cez `casts()`. Patrí to do vrstvy **Model**. Teória: [`app/README.md`](../README.md).

| Enum | Hodnoty | Metódy | Používa |
|---|---|---|---|
| [`OrderStatus`](OrderStatus.php) | Pending, Confirmed, Processing, Shipped, Delivered, Cancelled | `getLabel()`, `getColor()` (Filament), `badgeColor()` (frontend), `progressSteps()`, `isCancellableByCustomer()`, `isCancellableByAdmin()` | [`Order`](../Models/Order.php), [`OrderService`](../Services/OrderService.php), Filament objednávky, komponent `x-shop.order-status-badge`, `orders/show` (priebeh) |
| [`PaymentStatus`](PaymentStatus.php) | Pending, Paid, Failed | `getLabel()`, `getColor()` | `Order`, `OrderService` (pri zrušení adminom), Filament |
| [`PaymentMethod`](PaymentMethod.php) | CashOnDelivery | `getLabel()` | `Order`, checkout, Filament |
| [`AddressType`](AddressType.php) | Shipping, Billing | `label()` | [`UserAddress`](../Models/UserAddress.php), [`OrderAddress`](../Models/OrderAddress.php), [`UserAddressRequest`](../Http/Requests/UserAddressRequest.php), [`UserAddressController`](../Http/Controllers/UserAddressController.php) |
| [`PageStatus`](PageStatus.php) | Draft, Published, Archived | `getLabel()`, `getColor()` | [`Page`](../Models/Page.php) (`scopePublished`), Filament články |
| [`UserRole`](UserRole.php) | Admin, Employee, Designer, Customer | `panelRoles()` | [`User::canAccessPanel`](../Models/User.php), [`RegisteredUserController`](../Http/Controllers/Auth/RegisteredUserController.php), [`RolesAndPermissionsSeeder`](../../database/seeders/README.md) |

`HasLabel` a `HasColor` sú rozhrania Filamentu – vďaka nim Filament sám zobrazí popisok a farebný
štítok v tabuľkách a selectoch administrácie.
