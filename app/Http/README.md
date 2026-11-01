> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Http/` – vstupná vrstva HTTP

Všetko, čo súvisí s HTTP požiadavkou a odpoveďou. Teória a celý tok požiadavky: [`app/README.md`](../README.md).

| Priečinok | Úloha v toku | Ďalej posiela správu do |
|---|---|---|
| [`Requests/`](Requests/README.md) | validuje vstup **pred** controllerom | controller (len ak je vstup platný) |
| [`Controllers/`](Controllers/README.md) | riadi požiadavku: zavolá službu, vyberie odpoveď | [`Services/`](../Services/README.md), modely, views |
| [`Resources/`](Resources/README.md) | premení model na JSON pre JavaScript | prehliadač (`resources/js/services`) |

Middleware (`auth`, `guest`, `throttle`, `signed`, CSRF, session) poskytuje Laravel; zapína sa v
[`routes/`](../../routes/README.md), vlastný middleware projekt nemá.

Poradie pri jednej požiadavke:

```
routa + middleware ─► Form Request (rules, authorize) ─► Controller ─► Service/Model
                                                              │
                         view (HTML) / redirect / JSON (Resource) ◄┘
```
