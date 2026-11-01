> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Http/Controllers/Auth/` – autentifikácia

Prevzaté z **Laravel Breeze** (https://github.com/laravel/breeze) a orezané: existuje len prihlásenie,
registrácia, odhlásenie, zmena hesla a overenie e-mailu (reset hesla nie je). Routy sú v
[`routes/auth.php`](../../../../routes/auth.php), ktorý sa načíta z `web.php`. Teória: [`app/README.md`](../../../README.md).

**Teória autentifikácie.** Po úspešnom prihlásení Laravel uloží ID používateľa do **session** (cookie
s ID session, dáta na serveri). Každá ďalšia požiadavka prejde middlewarom `auth`, ktorý z session
načíta [`User`](../../../Models/User.php). Heslo sa nikdy neukladá ako text – model má cast `'password' => 'hashed'`
(bcrypt) a pri prihlásení sa porovnáva hash.

## Triedy

| Controller | Routa | Middleware | Čo robí | Ďalej |
|---|---|---|---|---|
| [`AuthenticatedSessionController`](AuthenticatedSessionController.php) | `GET/POST /login`, `POST /logout` | `guest` / `auth` | zobrazí formulár, prihlási cez [`LoginRequest::authenticate()`](../../Requests/Auth/LoginRequest.php) (s limitom pokusov), zregeneruje session; odhlásenie session zneplatní | view [`auth/login`](../../../../resources/views/auth/README.md); redirect na dashboard (`intended`) / po odhlásení na `/` |
| [`RegisteredUserController`](RegisteredUserController.php) | `GET/POST /register` | `guest` | vytvorí `User` z [`RegisterRequest`](../../Requests/Auth/RegisterRequest.php), priradí rolu `customer` ([`UserRole`](../../../Enums/README.md)), vyvolá udalosť `Registered` (odošle overovací e-mail) a prihlási | view `auth/register`; redirect na dashboard |
| [`PasswordController`](PasswordController.php) | `PUT /password` | `auth` | overí aktuálne heslo (`current_password`) a uloží nové (pravidlá `Password::defaults()` z [`AppServiceProvider`](../../../Providers/README.md)) | redirect späť so `status` |
| [`EmailVerificationPromptController`](EmailVerificationPromptController.php) | `GET /email/verify` | `auth` | stránka s výzvou na overenie | view `auth/verify-email` |
| [`EmailVerificationNotificationController`](EmailVerificationNotificationController.php) | `POST /email/verification-notification` | `auth`, `throttle:6,1` | znova pošle overovací e-mail | redirect späť so `status` |
| [`VerifyEmailController`](VerifyEmailController.php) | `GET /email/verify/{id}/{hash}` | `auth`, `signed` | podpísaný odkaz z e-mailu označí e-mail ako overený | redirect na dashboard |

Formuláre profilu (zmena hesla, údajov, zmazanie účtu) sú vo view
[`profile/partials`](../../../../resources/views/profile/README.md).
