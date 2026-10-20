<?php

namespace App\Services;

use App\Models\Manga;
use Illuminate\Support\Collection;

class CartService
{
    protected const SESSION_KEY = 'manga_cart';
    protected const TAX_RATE = 0.10; // 10% IVA
    protected const FREE_SHIPPING_THRESHOLD = 50.00;
    protected const STANDARD_SHIPPING_COST = 4.99;

    /**
     * Obtiene el contenido bruto del carrito desde la sesión.
     */
    public function getCart(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    /**
     * Agrega un manga al carrito o incrementa su cantidad.
     */
    public function add(Manga $manga, int $quantity = 1): array
    {
        $cart = $this->getCart();
        $id = $manga->id;

        $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        // Limitar al stock disponible
        if ($newQty > $manga->stock) {
            $newQty = $manga->stock;
        }

        $cart[$id] = [
            'id' => $manga->id,
            'title' => $manga->title,
            'slug' => $manga->slug,
            'author' => $manga->author,
            'price' => (float) $manga->price,
            'cover_image' => $manga->cover_image,
            'format' => $manga->format,
            'quantity' => $newQty,
            'max_stock' => $manga->stock,
            'subtotal' => round((float) $manga->price * $newQty, 2),
        ];

        session()->put(self::SESSION_KEY, $cart);

        return $cart;
    }

    /**
     * Actualiza la cantidad de un ítem existente.
     */
    public function update(int $mangaId, int $quantity): array
    {
        $cart = $this->getCart();

        if (isset($cart[$mangaId])) {
            if ($quantity <= 0) {
                unset($cart[$mangaId]);
            } else {
                $maxStock = $cart[$mangaId]['max_stock'] ?? 99;
                $validQty = min($quantity, $maxStock);

                $cart[$mangaId]['quantity'] = $validQty;
                $cart[$mangaId]['subtotal'] = round($cart[$mangaId]['price'] * $validQty, 2);
            }

            session()->put(self::SESSION_KEY, $cart);
        }

        return $cart;
    }

    /**
     * Remueve un manga del carrito.
     */
    public function remove(int $mangaId): array
    {
        $cart = $this->getCart();

        if (isset($cart[$mangaId])) {
            unset($cart[$mangaId]);
            session()->put(self::SESSION_KEY, $cart);
        }

        return $cart;
    }

    /**
     * Vacía completamente el carrito.
     */
    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * Retorna los ítems formateados como Collection de objetos/arrays.
     */
    public function getItems(): Collection
    {
        return collect($this->getCart());
    }

    /**
     * Suma total de los productos sin impuestos ni envíos.
     */
    public function getSubtotal(): float
    {
        $subtotal = 0.0;
        foreach ($this->getCart() as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        return round($subtotal, 2);
    }

    /**
     * Calcula el monto del impuesto (IVA).
     */
    public function getTax(): float
    {
        return round($this->getSubtotal() * self::TAX_RATE, 2);
    }

    /**
     * Calcula el costo de envío (Gratis a partir del umbral).
     */
    public function getShippingCost(): float
    {
        $subtotal = $this->getSubtotal();
        if ($subtotal === 0.0 || $subtotal >= self::FREE_SHIPPING_THRESHOLD) {
            return 0.00;
        }

        return self::STANDARD_SHIPPING_COST;
    }

    /**
     * Total definitivo con subtotal + impuestos + envío.
     */
    public function getTotal(): float
    {
        return round($this->getSubtotal() + $this->getTax() + $this->getShippingCost(), 2);
    }

    /**
     * Total de ejemplares individuales dentro del carrito.
     */
    public function getItemsCount(): int
    {
        $count = 0;
        foreach ($this->getCart() as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    /**
     * Entrega un resumen estructurado para las vistas y JSON.
     */
    public function getSummary(): array
    {
        $subtotal = $this->getSubtotal();
        $tax = $this->getTax();
        $shipping = $this->getShippingCost();
        $total = $this->getTotal();
        $count = $this->getItemsCount();

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'count' => $count,
            'free_shipping_threshold' => self::FREE_SHIPPING_THRESHOLD,
            'amount_to_free_shipping' => max(0, round(self::FREE_SHIPPING_THRESHOLD - $subtotal, 2)),
        ];
    }
}
