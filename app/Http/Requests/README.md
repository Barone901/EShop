> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Http/Requests/` – validácia vstupov (Form Requests)

**Teória.** Form Request je trieda, ktorú Laravel spracuje **pred** zavolaním controllera.
Stačí ju uviesť ako typ parametra (`public function store(UserAddressRequest $request)`):

1. `authorize()` – smie používateľ akciu vykonať? (`false` → 403)
2. `prepareForValidation()` – úprava vstupu pred kontrolou (napr. e-mail na malé písmená)
3. `rules()` – pravidlá **dátového typu aj platnej hodnoty** (`integer`, `email`, `max:255`, `regex`, `exists`, `Rule::in`)
4. chyba → web: presmerovanie späť s `$errors` a starými hodnotami (`old()`); AJAX: JSON 422
5. OK → controller dostane `$request->validated()` – len overené polia

Tak je controller bez validácie a rovnaké pravidlá sa nedajú obísť. Celý tok: [`app/README.md`](../../README.md).

**Klient + server rovnako.** Polia s regulárnym vzorom berú vzor z [`InputPattern`](../../Support/InputPattern.php).
Controller pošle konštantu `PATTERNS` do view, komponent [`x-form.input`](../../../resources/views/components/README.md)
ju vypíše ako HTML atribút `pattern` a [`form-validator.js`](../../../resources/js/modules/README.md) ju skontroluje
v prehliadači – server potom kontroluje ten istý vzor (`InputPattern::applyTo()`).

## Triedy

| Request | Používa ho | Kontroluje |
|---|---|---|
| [`CheckoutRequest`](CheckoutRequest.php) | [`OrderController::store`](../Controllers/OrderController.php) | doručovacia a fakturačná adresa (meno, e-mail, telefón, mesto, PSČ, IČO/DIČ/IČ DPH), poznámka max. 1000 znakov; `PATTERNS` pre vzory |
| [`ContactRequest`](ContactRequest.php) | [`ContactController::send`](../Controllers/ContactController.php) | meno (vzor), e-mail, predmet, správa 10–5000 znakov |
| [`ProductIndexRequest`](ProductIndexRequest.php) | [`ProductController::index`](../Controllers/ProductController.php) | GET filtre `category` (slug), `search`, `sort` (len hodnoty z `ProductService::SORTS`) |
| [`ProfileUpdateRequest`](ProfileUpdateRequest.php) | [`ProfileController::update`](../Controllers/ProfileController.php) | údaje profilu, unikátny e-mail (malé písmená) |
| [`UserAddressRequest`](UserAddressRequest.php) | [`UserAddressController::store/update`](../Controllers/UserAddressController.php) | adresa (typ len pri vytvorení); pomocné `addressData()` a `wantsDefault()` pre controller |

Podpriečinky: [`Api/`](Api/README.md) (AJAX endpointy), [`Auth/`](Auth/README.md) (prihlásenie, registrácia).
