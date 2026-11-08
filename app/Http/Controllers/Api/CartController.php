<?php

// [AI] Upravene s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddCartItemRequest;
use App\Http\Requests\Api\RemoveCartItemRequest;
use App\Http\Requests\Api\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

/**
 * API Controller pre pracu s nakupnym kosikom.
 *
 * Odpoved obsahuje data kosika (cart) aj HTML vyrenderovane z Blade komponentov (html) -
 * vzhlad kosika je len v sablonach (View), JavaScript HTML iba vlozi do stranky.
 * Chyby kosika (CartException) prevedie na JSON odpoved Laravel cez CartException::render().
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
     * Vrati aktualny obsah kosika ako JSON (data + HTML).
     */
    public function index(): JsonResponse
    {
        $cart = $this->cartService->getCart();

        return response()->json([...$cart, 'html' => $this->renderCart($cart)]);
    }

    /**
     * Prida produkt do kosika (validaciu robi AddCartItemRequest, dostupnost CartService).
     */
    public function add(AddCartItemRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->integer('product_id'));

        $this->cartService->addProduct($product, (int) ($request->validated('quantity') ?? 1));

        return $this->cartResponse(__('Product has been added to cart.'));
    }

    /**
     * Aktualizuje mnozstvo produktu v kosiku.
     */
    public function update(UpdateCartItemRequest $request): JsonResponse
    {
        $this->cartService->updateQuantity($request->integer('product_id'), $request->integer('quantity'));

        return $this->cartResponse(__('Cart has been updated.'));
    }

    /**
     * Odstrani produkt z kosika.
     */
    public function remove(RemoveCartItemRequest $request): JsonResponse
    {
        $this->cartService->removeProduct($request->integer('product_id'));

        return $this->cartResponse(__('Product has been removed from cart.'));
    }

    /**
     * Vyprazdni cely kosik.
     */
    public function clear(): JsonResponse
    {
        $this->cartService->clear();

        return $this->cartResponse(__('Cart has been cleared.'));
    }

    /**
     * Jednotna odpoved po uspesnej akcii: sprava + aktualny kosik (data aj HTML).
     */
    private function cartResponse(string $message): JsonResponse
    {
        $cart = $this->cartService->getCart();

        return response()->json([
            'success' => true,
            'message' => $message,
            'cart' => $cart,
            'html' => $this->renderCart($cart),
        ]);
    }

    /**
     * Vyrenderuje casti stranky s kosikom (nahlad v hlavicke, stranka kosika) z Blade komponentov.
     *
     * @return array{dropdown: string, page: string}
     */
    private function renderCart(array $cart): array
    {
        return [
            'dropdown' => view('components.shop.cart-dropdown', ['cart' => $cart])->render(),
            'page' => view('components.shop.cart-page', ['cart' => $cart])->render(),
        ];
    }
}
