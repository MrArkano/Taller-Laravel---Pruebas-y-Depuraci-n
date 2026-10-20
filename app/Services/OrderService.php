<?php

namespace App\Services;

use App\Models\Manga;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    /**
     * Procesa la compra completa a partir del carrito en sesión.
     * Ejecuta las deducciones de stock y creación de órdenes en una transacción atómica.
     */
    public function createOrderFromCart(array $customerData, CartService $cartService): Order
    {
        $items = $cartService->getItems();

        if ($items->isEmpty()) {
            throw new RuntimeException('El carrito de compras se encuentra vacío.');
        }

        return DB::transaction(function () use ($customerData, $items, $cartService) {
            // 1. Validar disponibilidad y stock en tiempo real
            foreach ($items as $item) {
                $manga = Manga::lockForUpdate()->find($item['id']);

                if (!$manga) {
                    throw new RuntimeException("El manga '{$item['title']}' ya no se encuentra en el catálogo.");
                }

                if ($manga->stock < $item['quantity']) {
                    throw new RuntimeException("Stock insuficiente para '{$manga->title}'. Disponibles: {$manga->stock}.");
                }
            }

            // 2. Calcular montos
            $subtotal = $cartService->getSubtotal();
            $tax = $cartService->getTax();
            $shippingCost = $cartService->getShippingCost();
            $total = $cartService->getTotal();

            // 3. Generar número de orden único
            $orderNumber = 'MNGA-' . strtoupper(date('Ymd')) . '-' . strtoupper(Str::random(5));

            // 4. Crear registro maestro de la Orden
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerData['customer_name'],
                'customer_email' => $customerData['customer_email'],
                'customer_phone' => $customerData['customer_phone'] ?? null,
                'shipping_address' => $customerData['shipping_address'],
                'city' => $customerData['city'],
                'postal_code' => $customerData['postal_code'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'completed',
                'payment_method' => $customerData['payment_method'] ?? 'card',
                'payment_status' => 'paid',
                'notes' => $customerData['notes'] ?? null,
            ]);

            // 5. Crear ítems y descontar inventario
            foreach ($items as $item) {
                $manga = Manga::find($item['id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'manga_id' => $manga->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => round($item['price'] * $item['quantity'], 2),
                ]);

                // Descontar inventario
                $manga->decrement('stock', $item['quantity']);
            }

            // 6. Limpiar el carrito de la sesión
            $cartService->clear();

            // Retornar la orden cargando los detalles
            return $order->load(['items.manga.publisher', 'items.manga.category']);
        });
    }
}
