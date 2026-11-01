> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/js/` – JavaScript (klientska časť)

**Teória.** JS dopĺňa server-side MVC o interakcie bez obnovenia stránky. Server vyrenderuje HTML
(Blade), JS sa naň **naviaže cez `id` a `data-*` atribúty** a pri potrebe dát volá server
**asynchrónne (AJAX, `fetch`)**. Vite z týchto súborov vytvorí jeden balík `public/build/assets/app-*.js`,
ktorý načíta layout cez `@vite`. Celý AJAX tok: [`app/README.md`](../../app/README.md#tok-3--ajax-asynchrónna-komunikácia).

Vnútorné vrstvy (podobné MVC na strane klienta):

```
HTML (Blade) ──udalosť (klik, písanie)──► modules/   (správanie, „controller“ v prehliadači)
                                             │  volá
                                             ▼
                                         services/   (komunikácia so serverom – fetch na /api)
                                             │  JSON
                                             ▼
                          modules/ prekreslí HTML cez utils/templates.js („view“ v JS)
```

| Súbor / priečinok | Úloha |
|---|---|
| [`app.js`](app.js) | vstup pre Vite: spustí Alpine.js (malé interakcie v Blade, napr. modálne okno) a načíta `eshop.js` |
| [`eshop.js`](eshop.js) | po `DOMContentLoaded` zavolá `init()` všetkých modulov; `window.Eshop` sprístupní moduly pre ladenie |
| [`modules/`](modules/README.md) | jednotlivé funkcie stránky (košík, vyhľadávanie, quick view, validácia formulárov…) |
| [`services/`](services/README.md) | volania API servera |
| [`utils/`](utils/README.md) | spoločné pomôcky (DOM udalosti, HTML šablóny, escapovanie) |

Pravidlá (ktoré `id`/`data-*` sa nesmú meniť): mapa väzieb v [`CLAUDE.md`](../../CLAUDE.md).
