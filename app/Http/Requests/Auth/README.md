> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Http/Requests/Auth/` – validácia prihlásenia a registrácie

| Request | Pôvod | Používa ho | Čo robí |
|---|---|---|---|
| [`LoginRequest`](LoginRequest.php) | Laravel Breeze (upravený) | [`AuthenticatedSessionController::store`](../../Controllers/Auth/AuthenticatedSessionController.php) | pravidlá e-mail + heslo; `authenticate()` overí údaje cez `Auth::attempt` (porovnanie hashu hesla); `ensureIsNotRateLimited()` – po 5 neúspešných pokusoch dočasné zablokovanie (ochrana proti hádaniu hesla); kľúč limitu = e-mail + IP |
| [`RegisterRequest`](RegisterRequest.php) | vlastný | [`RegisteredUserController::store`](../../Controllers/Auth/RegisteredUserController.php) | e-mail na malé písmená (`prepareForValidation`), unikátny e-mail, meno podľa vzoru z [`InputPattern`](../../../Support/InputPattern.php), heslo `Password::defaults()` (min. 8, veľké + malé písmeno, číslo) + potvrdenie |

Pravidlá hesla nastavuje [`AppServiceProvider`](../../../Providers/README.md) a rovnaké pravidlo `password`
kontroluje v prehliadači [`form-validator.js`](../../../../resources/js/modules/README.md).
Teória Form Requestov: [`Requests/`](../README.md).
