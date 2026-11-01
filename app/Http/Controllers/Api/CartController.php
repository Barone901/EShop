<?php

// [AI] Upravene s pomocou AI (Claude): validacia presunuta do FormRequest (Api/*CartItemRequest), readonly injekcia.

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\CartException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddCartItemRequest;
use App\Http\Requests\Api\RemoveCartItemRequest;
use App\Http\Requests\Api\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Closure;
use Illuminate\Http\JsonResponse;

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
     * Prida produkt do kosika (validaciu robi AddCartItemRequest, dostupnost CartService).
     */
    public function add(AddCartItemRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->integer('product_id'));

        return $this->respond(
            fn () => $this->cartService->addProduct($product, (int) ($request->validated('quantity') ?? 1)),
            'Product has been added to cart.'
        );
    }

    /**
     * Aktualizuje mnozstvo produktu v kosiku.
     */
    public function update(UpdateCartItemRequest $request): JsonResponse
    {
        return $this->respond(
            fn () => $this->cartService->updateQuantity($request->integer('product_id'), $request->integer('quantity')),
            'Cart has been updated.'
        );
    }

    /**
     * Odstrani produkt z kosika.
     */
    public function remove(RemoveCartItemRequest $request): JsonResponse
    {
        return $this->respond(
            fn () => $this->cartService->removeProduct($request->integer('product_id')),
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
