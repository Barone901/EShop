> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `tests/` – automatické testy

**Teória.** Feature test simuluje HTTP požiadavku (`$this->get('/products')`, `$this->postJson('/api/cart/add', …)`)
a overí odpoveď – prejde teda **celým MVC tokom** (routa → middleware → request → controller →
service → model → DB → view/JSON). Testy bežia nad samostatnou databázou `testing` (`phpunit.xml`),
ktorá sa pred každým testom vyčistí (`RefreshDatabase`).

Spustenie: `./vendor/bin/sail artisan test` (58 testov).

| Test | Počet | Čo overuje |
|---|---|---|
| [`Feature/ShopTest`](Feature/ShopTest.php) | 15 | hlavné toky: katalóg, filtre, vyhľadávanie, košík (API), checkout, objednávky, adresy, články |
| [`Feature/ShopIntegrityTest`](Feature/ShopIntegrityTest.php) | 14 | regresie: sklad a ceny v košíku, rušenie objednávok, čísla objednávok, viditeľnosť produktov, slugy, oprávnenia admina, escapovanie `%` a `_` vo vyhľadávaní |
| [`Feature/InputValidationTest`](Feature/InputValidationTest.php) | 3 | platné hodnoty vstupov na serveri a rovnaké vzory (`pattern`) vo formulároch |
| [`Feature/AdminPanelTest`](Feature/AdminPanelTest.php) | 4 | prístup do Filamentu podľa roly, stránky resources, vytvorenie článku |
| [`Feature/ProfileTest`](Feature/ProfileTest.php) | 5 | úprava profilu, zmazanie účtu (Breeze) |
| [`Feature/DemoDataTest`](Feature/DemoDataTest.php) | 1 | seedery vytvoria ukážkové dáta |
| [`Feature/Auth/*`](Feature/Auth) | 14 | prihlásenie, registrácia, zmena hesla, overenie e-mailu (Breeze) |
| [`Feature/ExampleTest`](Feature/ExampleTest.php), [`Unit/ExampleTest`](Unit/ExampleTest.php) | 2 | vzorové testy Laravelu |

[`TestCase`](TestCase.php) – spoločný predok testov (Laravel).
