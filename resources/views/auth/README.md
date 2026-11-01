> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/views/auth/` – prihlásenie a registrácia

Šablóny z **Laravel Breeze** (https://github.com/laravel/breeze), redizajnované. Používajú layout s
`type="guest"` (karta v strede) a Breeze komponenty (`x-text-input`, `x-input-label`, `x-input-error`).
Reset hesla v projekte neexistuje.

| View | Routa ([`routes/auth.php`](../../../routes/auth.php)) | Controller | Formulár odosiela |
|---|---|---|---|
| [`login`](login.blade.php) | `GET /login` | [`AuthenticatedSessionController::create`](../../../app/Http/Controllers/Auth/README.md) | `POST /login` → [`LoginRequest`](../../../app/Http/Requests/Auth/README.md) (e-mail, heslo, „Remember me“) |
| [`register`](register.blade.php) | `GET /register` | `RegisteredUserController::create` | `POST /register` → [`RegisterRequest`](../../../app/Http/Requests/Auth/README.md) |
| [`verify-email`](verify-email.blade.php) | `GET /email/verify` | `EmailVerificationPromptController` | `POST /email/verification-notification` (znova poslať), `POST /logout` |

Formuláre majú `data-validate` → kontrola v prehliadači ([`form-validator.js`](../../js/modules/form-validator.js)),
chyby zo servera zobrazí `x-input-error`, stav zo session `x-auth-session-status`.
