<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CartException;
use App\Models\Product;
use App\Support\MediaUrl;
use App\Support\Price;

/**
 * Sluzba pre pracu s nakupnym kosikom ulozenym v session.
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    /**
     * Ziska obsah kosika vratane vypocitanych hodnot.
     */
    public function getCart(): array
    {
        $items = collect($this->items())->map(function (array $item) {
            $subtotal = (float) $item['price'] * $item['quantity'];

            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'price' => $item['price'],
                'formatted_price' => Price::format($item['price']),
                'quantity' => $item['quantity'],
                'image_url' => MediaUrl::from($item['image_url'] ?? null),
                'subtotal' => $subtotal,
                'formatted_subtotal' => Price::format($subtotal),
                'url' => route('product.show', $item['slug']),
            ];
        })->values();

        $total = $items->sum('subtotal');

        return [
            'items' => $items,
            'total' => $total,
            'formatted_total' => Price::format($total),
            'count' => $items->sum('quantity'),
        ];
    }

    /**
     * Prida produkt do kosika s kontrolou skladu.
     *
     * @throws CartException
     */
    public function addProduct(Product $product, int $quantity = 1): void
    {
        if ($product->stock < $quantity) {
            throw CartException::notEnoughStock();
        }

        $cart = $this->items();

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'quantity' => $quantity,
                'image_url' => $product->image_url,
            ];
        }

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
            $product = Product::find($productId);

            if ($product && $product->stock < $quantity) {
                throw CartException::notEnoughStock();
            }

            $cart[$productId]['quantity'] = $quantity;
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

    private function items(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    private function save(array $cart): void
    {
        session()->put(self::SESSION_KEY, $cart);
    }
}
