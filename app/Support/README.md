> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Support/` – pomocné triedy

Malé triedy bez stavu so statickými metódami, ktoré používa viac vrstiev (modely, requesty, služby,
views). Vďaka nim sa rovnaké pravidlo (formát ceny, vzor vstupu) nepíše na viacerých miestach.
Teória: [`app/README.md`](../README.md).

| Trieda | Metódy | Používajú ju |
|---|---|---|
| [`Price`](Price.php) | `format($amount)` → napr. `€12.50` | accessory `formatted_*` v [`Product`](../Models/Product.php), [`Order`](../Models/Order.php), [`OrderItem`](../Models/OrderItem.php); [`CartService`](../Services/CartService.php); view `pages/home` (cena dopravy) |
| [`MediaUrl`](MediaUrl.php) | `from($path)` – cestu na disku `public` alebo absolútnu URL prevedie na verejnú URL | accessory obrázkov v `Product`, `Category`, `Page`, `Banner` |
| [`InputPattern`](InputPattern.php) | konštanty vzorov (`PERSON_NAME`, `PHONE`, `CITY`, `POSTAL_CODE`, `COMPANY_ID`…), `rule()`, `message()`, `applyTo()`, `messagesFor()` | [Form Requesty](../Http/Requests/README.md) (server) a cez `PATTERNS` → controller → view → [`x-form.input`](../../resources/views/components/README.md) atribút `pattern` → [`form-validator.js`](../../resources/js/modules/README.md) (prehliadač) |

`InputPattern` je most medzi serverom a klientom: **jeden vzor, dve kontroly**. Server kontroluje
vždy (klientskú kontrolu sa dá obísť), klient kvôli rýchlej spätnej väzbe bez odoslania formulára.
