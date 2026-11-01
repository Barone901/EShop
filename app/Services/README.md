> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `app/Services/` – biznis logika

**Teória.** Service (služba) obsahuje pravidlá obchodu, ktoré nepatria ani do controllera (ten len
riadi HTTP), ani do jedného modelu (pracujú s viacerými modelmi naraz). Controller dostane službu
cez **dependency injection** v konštruktore (`private readonly CartService $cartService`) – Laravel
ju vytvorí sám. Tú istú službu môže použiť webový controller, API controller aj Filament.
Teória a toky: [`app/README.md`](../README.md).

```
Controller ──► Service ──► Model(y) ──► DB
     ▲            │
     └── dáta / výnimka (CartException, OrderException)
```

| Služba | Metódy | Pracuje s | Volajú ju |
|---|---|---|---|
| [`CartService`](CartService.php) | `getCart()`, `addProduct()`, `updateQuantity()`, `removeProduct()`, `clear()` | **session** (kľúč `cart`: len `id produktu => množstvo`), model [`Product`](../Models/Product.php) (cena, sklad, názov sa vždy čítajú z DB), [`Price`](../Support/Price.php), `config('shop.*')` | [`Api/CartController`](../Http/Controllers/Api/README.md), [`OrderController::checkout`](../Http/Controllers/OrderController.php), [`OrderService`](OrderService.php) |
| [`OrderService`](OrderService.php) | `createOrder()`, `getUserOrders()`, `findUserOrder()`, `findGuestOrder()`, `cancelOrder()`, `deleteOrder()` | [`Order`](../Models/Order.php), [`OrderItem`](../Models/OrderItem.php), [`OrderAddress`](../Models/OrderAddress.php), `Product`, `CartService`, [enumy](../Enums/README.md) | [`OrderController`](../Http/Controllers/OrderController.php), Filament [`EditOrder`](../Filament/Resources/Orders/README.md) |
| [`ProductService`](ProductService.php) | `search()` (autocomplete, min. 2 znaky), `getProducts()` (filtre, radenie `SORTS`, stránkovanie) | `Product` (scopes `visible`, `search`) | [`ProductController`](../Http/Controllers/ProductController.php), [`ProductApiController`](../Http/Controllers/Api/ProductApiController.php), [`ProductIndexRequest`](../Http/Requests/ProductIndexRequest.php) (`SORTS`) |
| [`CategoryService`](CategoryService.php) | `getParentCategories()` | [`Category`](../Models/Category.php) | [`HomeController`](../Http/Controllers/HomeController.php), [`CategoryController`](../Http/Controllers/CategoryController.php) |
| [`PageService`](PageService.php) | `getPublishedPages()`, `findBySlug()`, `getRelatedPages()` | [`Page`](../Models/Page.php) | [`PageController`](../Http/Controllers/PageController.php) |
| [`BannerService`](BannerService.php) | `getActiveBanners()` | [`Banner`](../Models/Banner.php) | `HomeController`, `CategoryController` |

## Najdôležitejší tok: vytvorenie objednávky (`OrderService::createOrder`)

```
OrderController::store(CheckoutRequest)
  └► OrderService::createOrder($request->validated())
       ├─ CartService::getCart()  → prázdny košík = OrderException
       ├─ DB::transaction:
       │    1) produkty z košíka zamkne (lockForUpdate) a skontroluje sklad  → málo kusov = OrderException
       │    2) Order (číslo z generateOrderNumber, medzisúčet z cien v DB, doprava z config/shop.php)
       │    3) snapshot adries → OrderAddress (doručovacia + fakturačná)
       │    4) OrderItem pre každú položku + Product::decrement('stock')
       ├─ po commite: CartService::clear()
       └─ host: token do session 'guest_order_token' (len on uvidí potvrdenie)
  ◄── redirect orders.confirmation
```

Transakcia zaručí, že sa uloží buď celá objednávka, alebo nič; zámok riadkov zabráni tomu, aby dvaja
zákazníci naraz kúpili posledný kus.
