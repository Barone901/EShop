<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\CartException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Controller pre pracu s nakupnym kosikom.
 */
class CartController extends Controller
{
    /**
     * Injektuje sluzbu pre kosik.
     */
    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * Vrati aktualny obsah kosika ako JSON.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->cartService->getCart());
    }

    /**
     * Prida produkt do kosika s validaciou mnozstva a dostupnosti.
     */
    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:' . config('shop.max_cart_quantity')],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        return $this->respond(
            fn () => $this->cartService->addProduct($product, (int) ($validated['quantity'] ?? 1)),
            'Product has been added to cart.'
        );
    }

    /**
     * Aktualizuje mnozstvo produktu v kosiku.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity'   => ['required', 'integer', 'min:0', 'max:' . config('shop.max_cart_quantity')],
        ]);

        return $this->respond(
            fn () => $this->cartService->updateQuantity((int) $validated['product_id'], (int) $validated['quantity']),
            'Cart has been updated.'
        );
    }

    /**
     * Odstrani produkt z kosika.
     */
    public function remove(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        return $this->respond(
            fn () => $this->cartService->removeProduct((int) $validated['product_id']),
            'Product has been removed from cart.'
        );
    }

    /**
     * Vyprazdni cely kosik.
     */
    public function clear(): JsonResponse
    {
        return $this->respond(
            fn () => $this->cartService->clear(),
            'Cart has been cleared.'
        );
    }

    /**
     * Vykona akciu nad kosikom a vrati jednotnu JSON odpoved (sprava + aktualny kosik).
     */
    private function respond(Closure $action, string $successMessage): JsonResponse
    {
        try {
            $action();
        } catch (CartException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        return response()->json([
            'success' => true,
            'message' => $successMessage,
            'cart' => $this->cartService->getCart(),
        ]);
    }
}
