> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/View/` – triedy Blade komponentov

**Teória.** Blade komponent môže byť len šablóna (`resources/views/components/*.blade.php`, tzv.
anonymný komponent) alebo **trieda + šablóna**. Trieda sa hodí, keď komponent potrebuje typované
parametre alebo logiku. Je to stále vrstva **View** – nepracuje s databázou.

| Trieda | Tag v šablóne | Šablóna | Parametre |
|---|---|---|---|
| [`Components/Layouts/DefaultLayout`](Components/Layouts/DefaultLayout.php) | `<x-layouts.default-layout title="…" type="…">` | [`resources/views/layouts/default.blade.php`](../../resources/views/layouts/README.md) | `title` (titulok stránky), `type`: `default` / `app` (účet) / `guest` (prihlásenie, registrácia – karta v strede) |

Každá stránka v [`resources/views/pages`](../../resources/views/pages/README.md) je obalená týmto
layoutom; obsah stránky sa vloží do `{{ $slot }}`. Teória views: [`resources/views/README.md`](../../resources/views/README.md).
