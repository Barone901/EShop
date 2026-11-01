> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Exceptions/` – očakávané chyby logiky

**Teória.** Výnimka preruší vykonávanie a „vybuble“ k volajúcemu. Služby ich používajú pre
**očakávané** situácie (málo kusov na sklade, prázdny košík), ktoré nie sú chybou programu, ale
odpoveďou pre používateľa. Služba nevie, či ju volá web alebo API – preto len vyhodí výnimku a
**controller** rozhodne, ako ju zobraziť. Teória: [`app/README.md`](../README.md).

```
Service ──throw──► Controller (try/catch) ──► web: redirect s flash 'error'
                                         └──► API: JSON {success:false, message} so stavom
```

| Výnimka | Vyhadzuje | Zachytáva | Výsledok |
|---|---|---|---|
| [`CartException`](CartException.php) – továrenské metódy `notEnoughStock()`, `quantityLimit()`, `notInCart()`, `notAvailable()`; **kód výnimky = HTTP stav** | [`CartService`](../Services/CartService.php) | [`Api/CartController::respond()`](../Http/Controllers/Api/CartController.php) | JSON s `message` → [`cart.js`](../../resources/js/modules/cart.js) zobrazí Toast a obnoví košík |
| [`OrderException`](OrderException.php) – správa je určená priamo zákazníkovi (bez interných detailov) | [`OrderService`](../Services/OrderService.php) (prázdny košík, málo kusov, nedá sa zrušiť/zmazať, chyba DB) | [`OrderController`](../Http/Controllers/OrderController.php) `store`, `cancel`, `destroy`; Filament [`EditOrder`](../Filament/Resources/Orders/README.md) | redirect s `error` (+ `withInput()` pri checkoute) |

Neočakávané chyby (napr. výpadok DB) sa nezachytávajú tu – spracuje ich Laravel (log + chybová stránka).
