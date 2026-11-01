> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Policies/` – autorizácia (kto smie čo)

**Teória.** Autentifikácia odpovedá „kto si“ (prihlásenie), **autorizácia** odpovedá „smieš to
urobiť?“. Policy je trieda s metódami pomenovanými podľa akcií (`viewAny`, `view`, `create`,
`update`, `delete`), ktoré vrátia `true/false`. Laravel ju nájde automaticky podľa názvu
(`UserAddress` → `UserAddressPolicy`). Volá sa:
- v controlleri: `Gate::authorize('update', $address)` → pri `false` odpoveď **403**
- vo Filamente: automaticky pred zobrazením zoznamu, formulára, mazaním

Teória: [`app/README.md`](../README.md).

| Policy | Model | Pravidlo | Kto ju volá |
|---|---|---|---|
| [`UserAddressPolicy`](UserAddressPolicy.php) | [`UserAddress`](../Models/UserAddress.php) | `update`, `delete` len vlastník (`$address->user_id === $user->id`) | [`UserAddressController`](../Http/Controllers/UserAddressController.php) `edit`, `update`, `destroy`, `setDefault` |
| [`ProductPolicy`](ProductPolicy.php) | `Product` | oprávnenie `manage products` | Filament [`Products`](../Filament/Resources/Products/README.md) |
| [`CategoryPolicy`](CategoryPolicy.php) | `Category` | `manage categories` | Filament [`Categories`](../Filament/Resources/Categories/README.md) |
| [`OrderPolicy`](OrderPolicy.php) | `Order` | `manage orders`; `create` vždy `false` (objednávky vznikajú len v eshope) | Filament [`Orders`](../Filament/Resources/Orders/README.md) |
| [`PagePolicy`](PagePolicy.php) | `Page` | `manage pages` + `restore`, `forceDelete` (soft delete) | Filament [`Pages`](../Filament/Resources/Pages/README.md) |
| [`BannerPolicy`](BannerPolicy.php) | `Banner` | `manage media` | Filament [`Banners`](../Filament/Resources/Banners/README.md) |

[`Concerns/AuthorizesByPermission`](Concerns/AuthorizesByPermission.php) – trait pre admin policies:
všetky akcie povolí jedno oprávnenie z konštanty `PERMISSION` (`$user->can(...)`, Spatie Permission).
Oprávnenia rolí priraďuje [`RolesAndPermissionsSeeder`](../../database/seeders/README.md)
(napr. Designer: médiá, články, produkty, kategórie; Employee: produkty, kategórie, objednávky; Admin: všetko).

Objednávky zákazníka na webe nechráni policy, ale [`OrderService::findUserOrder()`](../Services/OrderService.php):
hľadá len medzi objednávkami prihláseného používateľa, cudzia objednávka vráti **404** (neprezradí, že existuje).
