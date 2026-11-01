> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Filament/Resources/Pages/` – administrácia: Page

Články (titulok, slug, perex, obsah z RichEditora, obrázok, SEO, stav, dátum publikácie, zvýraznenie). Model má soft delete – zmazaný článok sa dá obnoviť.
Navigačná skupina **Media**. Všeobecný popis Filamentu a toku: [`app/Filament/README.md`](../../README.md).

| Súbor | Úloha |
|---|---|
| [`PageResource.php`](./PageResource.php) | spája model [`Page`](../../../Models/Page.php), formulár, tabuľku a stránky |
| [`Schemas/PageForm.php`](Schemas/PageForm.php) | polia formulára a ich validácia |
| [`Tables/PagesTable.php`](Tables/PagesTable.php) | stĺpce, filtre a akcie zoznamu |
| [`Pages/ListPages.php`](Pages/ListPages.php) | stránka panelu: zoznam |
| [`Pages/CreatePage.php`](Pages/CreatePage.php) | stránka panelu: vytvorenie |
| [`Pages/EditPage.php`](Pages/EditPage.php) | stránka panelu: úprava |

**Autorizácia:** [`PagePolicy`](../../../Policies/PagePolicy.php) – oprávnenie `manage pages` (trait `AuthorizesByPermission`).

**Prepojenie s eshopom:** Na webe sa zobrazia len publikované ([`Page::scopePublished`](../../../Models/Page.php)); HTML obsahu sa pred výpisom sanitizuje (`safe_content`).
