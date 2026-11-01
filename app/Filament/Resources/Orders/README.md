> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Filament/Resources/Orders/` – administrácia: Order

Objednávky zákazníkov. V admine sa nevytvárajú (`OrderPolicy::create` = false), iba sa spracúvajú: zmena stavu objednávky a platby.
Navigačná skupina **Eshop**. Všeobecný popis Filamentu a toku: [`app/Filament/README.md`](../../README.md).

| Súbor | Úloha |
|---|---|
| [`OrderResource.php`](./OrderResource.php) | spája model [`Order`](../../../Models/Order.php), formulár, tabuľku a stránky |
| [`Schemas/OrderForm.php`](Schemas/OrderForm.php) | polia formulára a ich validácia |
| [`Tables/OrdersTable.php`](Tables/OrdersTable.php) | stĺpce, filtre a akcie zoznamu |
| [`Pages/ListOrders.php`](Pages/ListOrders.php) | stránka panelu: zoznam |
| [`Pages/ViewOrder.php`](Pages/ViewOrder.php) | stránka panelu: detail (len čítanie) |
| [`Pages/EditOrder.php`](Pages/EditOrder.php) | stránka panelu: úprava |

**Autorizácia:** [`OrderPolicy`](../../../Policies/OrderPolicy.php) – oprávnenie `manage orders` (trait `AuthorizesByPermission`).

**Prepojenie s eshopom:** [`Pages/EditOrder`](Pages/EditOrder.php) má akciu **Cancel**, ktorá volá [`OrderService::cancelOrder($order, byAdmin: true)`](../../../Services/OrderService.php) – rovnaká logika ako pri zrušení zákazníkom (vrátenie na sklad), chyby prídu ako [`OrderException`](../../../Exceptions/README.md).
