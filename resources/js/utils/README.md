> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `resources/js/utils/` – spoločné pomôcky

Funkcie bez vlastného stavu, ktoré používa viac modulov (pomenované exporty). Teória:
[`resources/js/README.md`](../README.md).

| Súbor | Exporty | Úloha | Používajú |
|---|---|---|---|
| [`dom.js`](dom.js) | `byId()`, `delegate()`, `onEscape()`, `onOutsideClick()` | prístup k prvkom; **delegované udalosti** – jeden listener na `document` pre každý typ udalosti, funguje aj pre HTML vložené neskôr cez AJAX (napr. tlačidlá v košíku) | všetky moduly |
| [`templates.js`](templates.js) | `icon()`, `productImage()`, `buttonClass()`, `stockBadge()`, `spinner()` | HTML šablóny pre obsah z AJAXu; triedy zodpovedajú Blade komponentom (`x-button`, `x-shop.stock-badge`, `x-ui.loading`), takže prvok vyzerá rovnako v Blade aj v JS | `cart.js`, `search.js`, `quickview.js` |
| [`escape-html.js`](escape-html.js) | `escapeHtml()` | escapuje `& < > " '` pred vložením dát zo servera do `innerHTML` – ochrana proti **XSS** | `cart.js`, `search.js`, `quickview.js`, `toast.js`, `templates.js` |
| [`transition.js`](transition.js) | `showAnimated()`, `hideAnimated()` | animované zobrazenie/skrytie (triedy `hidden`, `opacity-0`, `translate-y-2`) | `menu.js` |
