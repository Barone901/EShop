<?php

// [AI] Upravene s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CartException;
use App\Models\Product;
use App\Support\Price;
use Illuminate\Support\Collection;

/**
 * Sluzba pre pracu s nakupnym kosikom ulozenym v session.
 *
 * V session je len "id produktu => mnozstvo". Nazov, cena a obrazok sa vzdy nacitaju
 * z databazy, takze kosik ukazuje aktualnu cenu a nedostupne produkty z neho zmiznu.
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    /**
     * Ziska obsah kosika vratane vypocitanych hodnot.
     */
    public function getCart(): array
    {
        $quantities = $this->items();
        $products = $this->visibleProducts(array_keys($quantities));

        // Produkty, ktore medzicasom zmizli z ponuky, z kosika odstranime
        if ($products->count() !== count($quantities)) {
            $quantities = array_intersect_key($quantities, $products->all());
            $this->save($quantities);
        }

        $items = collect($quantities)->map(function (int $quantity, int $productId) use ($products) {
            // Kluce $quantities su po prieniku vyssie podmnozinou klucov $products, produkt teda vzdy existuje
            /** @var Product $product */
            $product = $products->get($productId);
            $subtotal = (float) $product->price * $quantity;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'quantity' => $quantity,
                'image_url' => $product->image_src,
                'subtotal' => $subtotal,
                'formatted_subtotal' => Price::format($subtotal),
                'url' => route('product.show', $product),
            ];
        })->values();

        $total = $items->sum('subtotal');
        $shippingCost = (float) config('shop.shipping_cost');
        $grandTotal = $total + $shippingCost;

        return [
            'items' => $items,
            'total' => $total,
            'formatted_total' => Price::format($total),
            'shipping_cost' => $shippingCost,
            'formatted_shipping_cost' => Price::format($shippingCost),
            'grand_total' => $grandTotal,
            'formatted_grand_total' => Price::format($grandTotal),
            'count' => $items->sum('quantity'),
        ];
    }

    /**
     * Prida produkt do kosika. Kontroluje sa celkove mnozstvo v kosiku (uz pridane + nove).
     *
     * @throws CartException
     */
    public function addProduct(Product $product, int $quantity = 1): void
    {
        if (!$product->isVisible()) {
            throw CartException::notAvailable();
        }

        $cart = $this->items();
        $newQuantity = ($cart[$product->id] ?? 0) + $quantity;

        $this->ensureQuantityAllowed($product, $newQuantity);

        $cart[$product->id] = $newQuantity;
        $this->save($cart);
    }

    /**
     * Aktualizuje mnozstvo produktu v kosiku (0 = odstranit).
     *
     * @throws CartException
     */
    public function updateQuantity(int $productId, int $quantity): void
    {
        $cart = $this->items();

        if (!isset($cart[$productId])) {
            throw CartException::notInCart();
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $product = Product::visible()->find($productId) ?? throw CartException::notAvailable();

            $this->ensureQuantityAllowed($product, $quantity);

            $cart[$productId] = $quantity;
        }

        $this->save($cart);
    }

    /**
     * Odstrani produkt z kosika.
     */
    public function removeProduct(int $productId): void
    {
        $cart = $this->items();
        unset($cart[$productId]);

        $this->save($cart);
    }

    /**
     * Vyprazdni cely kosik.
     */
    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * @throws CartException
     */
    private function ensureQuantityAllowed(Product $product, int $quantity): void
    {
        $max = (int) config('shop.max_cart_quantity');

        if ($quantity > $max) {
            throw CartException::quantityLimit($max);
        }

        if ($quantity > $product->stock) {
            throw CartException::notEnoughStock();
        }
    }

    /**
     * Viditelne produkty podla ID, klucovane podla ID (jeden dotaz pre cely kosik).
     *
     * @param  list<int>  $ids
     */
    /**
     * @param  array<int, int>  $ids
     * @return Collection<int, Product> produkty indexovane podla id
     */
    private function visibleProducts(array $ids): Collection
    {
        if ($ids === []) {
            return new Collection();
        }

        return Product::visible()->whereKey($ids)->get()->keyBy('id');
    }

    /**
     * @return array<int, int> id produktu => mnozstvo
     */
    private function items(): array
    {
        // Starsi format kosika ukladal cely snapshot produktu, z neho berieme len mnozstvo
        return array_map(
            fn (mixed $item) => is_array($item) ? (int) $item['quantity'] : (int) $item,
            session()->get(self::SESSION_KEY, [])
        );
    }

    /**
     * @param  array<int, int>  $cart
     */
    private function save(array $cart): void
    {
        session()->put(self::SESSION_KEY, $cart);
    }
}
