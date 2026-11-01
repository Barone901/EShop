> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/views/profile/` – účet zákazníka

| View | Úloha | Dáta / odosiela |
|---|---|---|
| [`dashboard`](dashboard.blade.php) | prehľad účtu: posledné objednávky, predvolené adresy, nastavenia profilu | [`DashboardController`](../../../app/Http/Controllers/DashboardController.php) → `user`, `recentOrders`, `shippingAddress`, `billingAddress`, `profilePatterns` |
| [`partials/update-profile-information-form`](partials/update-profile-information-form.blade.php) | zmena mena, e-mailu… (Breeze) | `PATCH /profile` → [`ProfileUpdateRequest`](../../../app/Http/Requests/README.md) → [`ProfileController::update`](../../../app/Http/Controllers/ProfileController.php) |
| [`partials/update-password-form`](partials/update-password-form.blade.php) | zmena hesla (Breeze) | `PUT /password` → [`PasswordController`](../../../app/Http/Controllers/Auth/README.md) |
| [`partials/delete-user-form`](partials/delete-user-form.blade.php) | zmazanie účtu s potvrdením heslom v modálnom okne (Alpine `x-modal`, Breeze) | `DELETE /profile` → `ProfileController::destroy` |

Partials sa vkladajú do dashboardu cez `@include`. Po uložení controller presmeruje späť na
`dashboard#profile` so `status`, ktorý šablóna zobrazí ako „Saved.“.
