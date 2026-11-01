> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Providers/` – nastavenie aplikácie pri štarte

**Teória.** Service Provider sa spustí pri každej požiadavke ešte pred routou. `register()` registruje
služby do kontajnera, `boot()` nastavuje správanie frameworku. Zoznam providerov je v
[`bootstrap/providers.php`](../../bootstrap/providers.php). Teória: [`app/README.md`](../README.md).

| Provider | Čo nastavuje | Ovplyvní |
|---|---|---|
| [`AppServiceProvider`](AppServiceProvider.php) | `Password::defaults()` – min. 8 znakov, veľké + malé písmeno, číslo | [`RegisterRequest`](../Http/Requests/Auth/README.md), [`PasswordController`](../Http/Controllers/Auth/README.md) (rovnaké pravidlo `password` má [`form-validator.js`](../../resources/js/modules/README.md)) |
| | `JsonResource::withoutWrapping()` – JSON bez obalu `data` | [`Http/Resources`](../Http/Resources/README.md) → `search.js`, `quickview.js` |
| | `Model::preventLazyLoading()` mimo produkcie + log | upozorní na N+1 dotazy (relácia načítaná v cykle) |
| [`Filament/AdminPanelProvider`](Filament/AdminPanelProvider.php) | admin panel: `id/path('admin')`, vlastné prihlásenie, farby, automatické načítanie resources z [`app/Filament/Resources`](../Filament/README.md), middleware panelu | celá administrácia `/admin`; prístup overí [`User::canAccessPanel()`](../Models/User.php) |
