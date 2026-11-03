# Triumf Professional Tools – e-shop

Semestrálna práca z predmetu VAII (2026/27). Ide o e-shop s ručným náradím a vybavením autoservisov.
Zákazník si môže prezerať katalóg, nakupovať (aj bez registrácie), spravovať adresy a objednávky.
Zamestnanci firmy spravujú katalóg, obsah a objednávky v administrácii.

**Technológie:** PHP 8.5, Laravel 12, Filament 4 (administrácia), Spatie Laravel Permission (roly),
MySQL 8.4, Tailwind CSS 4, Vite, Alpine.js, Docker (Laravel Sail).

## Spustenie (Docker)

Potrebný je iba Docker. Pri prvom spustení kontajner sám:

- nainštaluje závislosti,
- vytvorí `.env`,
- spustí migrácie,
- naimportuje ukážkové dáta,
- vytvorí odkaz `public/storage` pre obrázky (`php artisan storage:link`),
- zbuildí frontend.

Robí to skript `docker/8.5/init-app`.

```bash
git clone <adresa-repozitara> eShop
cd eShop
docker compose up -d
docker compose logs -f laravel.test   # počkať na "[init] Hotovo" (prvé spustenie trvá niekoľko minút)
```

Aplikácia beží na <http://localhost>, administrácia na <http://localhost/admin>.

- Iné porty: pred spustením `cp .env.example .env` a v `.env` zmeniť `APP_PORT` a `FORWARD_DB_PORT`.
- Vymazanie databázy a nový import ukážkových dát: `docker compose down -v && docker compose up -d`.
- Testy: `docker compose exec laravel.test php artisan test`.

### Ukážkové účty

| Rola | E-mail | Heslo | Prístup |
|---|---|---|---|
| admin | admin@admin.com | admin123 | celá administrácia |
| employee | employee@example.com | password | produkty, kategórie, objednávky |
| designer | designer@example.com | password | produkty, kategórie, články, bannery |
| customer | customer@example.com | password | e-shop, má adresy a 4 objednávky v rôznych stavoch |

> **Upozornenie k heslám:** heslá ukážkových účtov sú verejne uvedené v tomto súbore a sú slabé
> (`admin123`, `password`). Nespĺňajú ani pravidlá, ktoré aplikácia vyžaduje pri registrácii a zmene hesla
> (aspoň 8 znakov, veľké aj malé písmeno a číslica), pretože ich vytvárajú seedery priamo v databáze.
> Slúžia len na lokálne vyskúšanie. Pri nasadení inde ako na vlastnom počítači ukážkové dáta neimportuj
> alebo heslá všetkých účtov hneď zmeň (profil → Update Password) a ukážkové účty zmaž.

## Splnenie požiadaviek

| Požiadavka | Riešenie |
|---|---|
| Dynamické stránky | homepage, produkty (filter, triedenie, stránkovanie), detail produktu, kategória, košík, checkout, potvrdenie objednávky, moje objednávky, detail objednávky, adresy, dashboard, články, detail článku |
| DB entity a vzťahy | `categories` (1:N sama na seba, 1:N `products`), `products`, `orders` (N:1 `users`), `order_items` (N:1 `orders`, N:1 `products`), `order_addresses`, `user_addresses` (N:1 `users`), `pages` (N:1 autor), `banners` (N:1 `pages`); roly a oprávnenia sú M:N (Spatie) |
| CRUD na frontende | `user_addresses`: vytvorenie, čítanie, úprava, zmazanie, nastavenie predvolenej (`UserAddressController`). `orders`: vytvorenie (checkout), čítanie, zrušenie, zmazanie zrušenej (`OrderController`) |
| CRUD v administrácii | produkty, kategórie, články, bannery, objednávky (Filament resources v `app/Filament`) |
| AJAX | košík bez obnovenia stránky (`api/cart/*`, `resources/js/modules/cart.js`), živé vyhľadávanie (`api/products/search`, `search.js`), rýchly náhľad produktu (`api/products/{slug}`, `quickview.js`) |
| Roly a autorizácia | 4 roly s oprávneniami (`RolesAndPermissionsSeeder`), Policies v `app/Policies`, prístup do administrácie podľa roly (`User::canAccessPanel`), zákazník vidí len svoje objednávky a adresy |
| Práca so súbormi | upload obrázkov produktov, kategórií, bannerov a článkov a príloh v editore článku (disk `public`), mazanie a náhrada v administrácii |
| Kontrola vstupov | server: Form Requesty v `app/Http/Requests` (typ, dĺžka aj platná hodnota). Klient: `resources/js/modules/form-validator.js`. Spoločné vzory pre oba v `app/Support/InputPattern.php` |
| Bezpečnosť | heslá hashované (bcrypt), Eloquent bez raw SQL (ochrana pred SQL injection), CSRF, sanitizácia HTML článkov, throttle prihlásenia a kontaktného formulára, kontrola vlastníka objednávky aj pre hosťa (token v session) |
| Vlastný JavaScript | `resources/js` (košík, vyhľadávanie, quick view, validácia, checkout, banner slider, menu) |
| Vlastné CSS | `resources/css/app.css` (téma, formuláre, karty, obsah článku, slider) |
| Responzívny dizajn | Tailwind breakpointy, mobilné menu a vyhľadávanie, tabuľky prispôsobené mobilu |
| Docker a ukážkové dáta | `compose.yaml`, `docker/`, seedery v `database/seeders` (obrázky v `database/seeders/images`) |

## Prevzatý kód

- **Laravel 12** – kostra aplikácie (`config/`, `bootstrap/`, `public/index.php`, predvolené migrácie `users`, `cache`, `jobs`).
- **Laravel Breeze** (<https://github.com/laravel/breeze>) – prihlásenie, registrácia, overenie e-mailu a profil. Ide o controllery v `app/Http/Controllers/Auth`, `ProfileController`, `LoginRequest`, `ProfileUpdateRequest`, `routes/auth.php`, komponenty `text-input`, `input-label`, `input-error`, `modal`, `auth-session-status`, šablóny v `resources/views/auth` a `resources/views/profile/partials` a testy v `tests/Feature/Auth`. Súbory sú upravené a označené komentárom v hlavičke.
- **Laravel Sail** (<https://github.com/laravel/sail>) – Docker runtime v `docker/` (doplnený skript `init-app`) a `compose.yaml`.
- **Filament** – resources v `app/Filament/Resources` boli vygenerované príkazom `make:filament-resource` a následne upravené (formuláre, tabuľky, oprávnenia).
- **Spatie Laravel Permission** – tabuľky a modely rolí a oprávnení.

## Použitie AI

Pri práci bol použitý nástroj generatívnej AI **Claude (Anthropic) cez Claude Code**.
Súbory, ktoré AI vytvorila, majú v hlavičke značku **`[AI]`** (napr. `app/Support/*`, `app/Exceptions/*`,
seedery, `docker/8.5/init-app`, testy `ShopTest`, `AdminPanelTest`, `InputValidationTest`, `DemoDataTest`).
