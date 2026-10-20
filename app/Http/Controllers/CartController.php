<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Vista principal del carrito de compras.
     */
    public function index()
    {
        $cart = $this->cartService->getItems();
        $summary = $this->cartService->getSummary();

        return view('cart.index', compact('cart', 'summary'));
    }

    /**
     * Añade un manga al carrito.
     */
    public function add(Request $request, Manga $manga)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1|max:' . max(1, $manga->stock),
        ]);

        $quantity = (int) $request->input('quantity', 1);

        if (!$manga->is_in_stock) {
            return back()->with('error', "El manga '{$manga->title}' se encuentra agotado temporalmente.");
        }

        $this->cartService->add($manga, $quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "¡'{$manga->title}' fue añadido a tu colección!",
                'cart_count' => $this->cartService->getItemsCount(),
                'summary' => $this->cartService->getSummary(),
            ]);
        }

        return back()->with('success', "¡'{$manga->title}' ha sido añadido al carrito!");
    }

    /**
     * Actualiza la cantidad de un producto.
     */
    public function update(Request $request, Manga $manga)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:' . max(1, $manga->stock),
        ]);

        $this->cartService->update($manga->id, (int) $request->quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart_count' => $this->cartService->getItemsCount(),
                'summary' => $this->cartService->getSummary(),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Carrito actualizado correctamente.');
    }

    /**
     * Elimina un ítem del carrito.
     */
    public function remove(Manga $manga)
    {
        $this->cartService->remove($manga->id);

        return redirect()->route('cart.index')->with('success', "Se quitó '{$manga->title}' de tu selección.");
    }

    /**
     * Vacía el carrito por completo.
     */
    public function clear()
    {
        $this->cartService->clear();

        return redirect()->route('cart.index')->with('info', 'El carrito ha sido vaciado.');
    }
}
