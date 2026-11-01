> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/css/` – štýly

[`app.css`](app.css) je vstupný súbor pre **Tailwind CSS v4** (cez Vite). Prehliadač ho **nenačítava
priamo** – `npm run build` z neho vytvorí `public/build/assets/app-*.css`, ktorý vloží `@vite(...)`
v layoute [`layouts/default.blade.php`](../views/layouts/README.md). Zmena v `app.css` sa prejaví až
po `./vendor/bin/sail npm run build` (alebo pri bežiacom `npm run dev`).

Súbor má dve časti (podrobne v jeho hlavičke):

| Časť | Obsah |
|---|---|
| **[AI] dizajnový systém** | `@theme` – tokeny farieb, písma, zaoblenia (z nich vznikajú triedy `bg-ink-900`, `text-muted`…); `@layer base` – nadpisy, focus, výber textu; `@layer components` – polia formulárov, `.card`, `.eyebrow`, `.num`, `.validation-error` |
| **Pôvodné pravidlá autora** (22) | `.site-footer`, hover kariet `.product-card` / `.article-card`, obsah článkov `.article-content …`, scrollbar, bodky slidera `[data-banner-dot]` |

Kde sa pravidlá uplatnia:

| Pravidlo | HTML | Súbor |
|---|---|---|
| `.site-footer` | `<footer class="site-footer">` | [`layouts/partials/footer`](../views/layouts/partials/footer.blade.php) |
| `.product-card`, `.article-card` | karty vo výpise | [`components/shop/product-card`](../views/components/shop/product-card.blade.php), [`components/ui/article-card`](../views/components/ui/article-card.blade.php) |
| `.article-content …` | obsah článku z administrácie | [`pages/articles/show`](../views/pages/articles/show.blade.php) |
| `[data-banner-dot]` (+ `aria-current`) | bodky slidera, aktívnu nastavuje JS | [`components/shop/banner-slider`](../views/components/shop/banner-slider.blade.php), [`banner-slider.js`](../js/modules/banner-slider.js) |
| `[aria-invalid="true"]`, `.validation-error` | chybné pole a hláška | [`x-form.input`](../views/components/form/input.blade.php) (server), [`form-validator.js`](../js/modules/form-validator.js) (klient) |

Farby a pravidlá dizajnu: [`CLAUDE.md`](../../CLAUDE.md).
