> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Filament/Resources/Banners/` – administrácia: Banner

Bannery slidera na homepage (obrázok, odkaz na URL alebo článok, poradie, aktívnosť).
Navigačná skupina **Media**. Všeobecný popis Filamentu a toku: [`app/Filament/README.md`](../../README.md).

| Súbor | Úloha |
|---|---|
| [`BannerResource.php`](./BannerResource.php) | spája model [`Banner`](../../../Models/Banner.php), formulár, tabuľku a stránky |
| [`Schemas/BannerForm.php`](Schemas/BannerForm.php) | polia formulára a ich validácia |
| [`Tables/BannersTable.php`](Tables/BannersTable.php) | stĺpce, filtre a akcie zoznamu |
| [`Pages/ListBanners.php`](Pages/ListBanners.php) | stránka panelu: zoznam |
| [`Pages/CreateBanner.php`](Pages/CreateBanner.php) | stránka panelu: vytvorenie |
| [`Pages/EditBanner.php`](Pages/EditBanner.php) | stránka panelu: úprava |

**Autorizácia:** [`BannerPolicy`](../../../Policies/BannerPolicy.php) – oprávnenie `manage media` (trait `AuthorizesByPermission`).

**Prepojenie s eshopom:** Aktívne bannery načíta [`BannerService`](../../../Services/BannerService.php) pre slider [`x-shop.banner-slider`](../../../../resources/views/components/README.md).
