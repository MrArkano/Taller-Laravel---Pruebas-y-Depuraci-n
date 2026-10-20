<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService
    ) {}

    /**
     * Muestra la pantalla de Checkout integrando Stripe Elements.
     */
    public function index()
    {
        if ($this->cartService->getItemsCount() === 0) {
            return redirect()->route('mangas.index')->with('info', 'Tu carrito está vacío. Agrega mangas antes de proceder al pago.');
        }

        $items = $this->cartService->getItems();
        $summary = $this->cartService->getSummary();

        // 1. Inicializar PaymentIntent de Stripe para el monto total del carrito
        $clientSecret = null;
        $stripeKey = config('services.stripe.key');
        $stripeSecret = config('services.stripe.secret');

        if ($stripeSecret && $summary['total'] > 0) {
            try {
                Stripe::setApiKey($stripeSecret);
                $amountInCents = (int) round($summary['total'] * 100);

                $paymentIntent = PaymentIntent::create([
                    'amount' => $amountInCents,
                    'currency' => 'usd',
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                    'description' => "Compra en MANGA・VAULT ({$summary['count']} tomos)",
                    'metadata' => [
                        'cart_items_count' => $summary['count'],
                        'subtotal' => $summary['subtotal'],
                        'tax' => $summary['tax'],
                        'shipping' => $summary['shipping'],
                        'total' => $summary['total'],
                    ],
                ]);

                $clientSecret = $paymentIntent->client_secret;
            } catch (Exception $e) {
                Log::error('Error al inicializar PaymentIntent en CheckoutController: ' . $e->getMessage());
            }
        }

        return view('checkout.index', compact('items', 'summary', 'clientSecret', 'stripeKey'));
    }

    /**
     * Procesa la compra utilizando el OrderService y confirma datos con Stripe.
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_email' => 'required|email|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'shipping_address' => 'required|string|max:250',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'payment_method' => 'required|in:card,transfer,paypal',
            'payment_intent_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // Si el pago fue con tarjeta Stripe, actualizar metadatos y cliente en Stripe
        if ($validated['payment_method'] === 'card' && !empty($validated['payment_intent_id'])) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));

                // Crear o asociar cliente en Stripe con el nombre y email ingresados
                $customer = Customer::create([
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'phone' => $validated['customer_phone'] ?? null,
                    'address' => [
                        'line1' => $validated['shipping_address'],
                        'city' => $validated['city'],
                        'postal_code' => $validated['postal_code'] ?? null,
                    ],
                ]);

                // Actualizar PaymentIntent en Stripe con el cliente y descripción
                PaymentIntent::update($validated['payment_intent_id'], [
                    'customer' => $customer->id,
                    'description' => "Compra de {$validated['customer_name']} en MANGA・VAULT",
                    'metadata' => [
                        'customer_name' => $validated['customer_name'],
                        'customer_email' => $validated['customer_email'],
                        'shipping_city' => $validated['city'],
                    ],
                ]);

                $validated['payment_method'] = 'Tarjeta de Crédito (Stripe)';
            } catch (Exception $e) {
                Log::warning('No se pudo actualizar el PaymentIntent en Stripe: ' . $e->getMessage());
                $validated['payment_method'] = 'Tarjeta de Crédito';
            }
        }

        try {
            $order = $this->orderService->createOrderFromCart($validated, $this->cartService);

            return redirect()->route('checkout.success', $order->order_number)
                ->with('success', '¡Tu compra se ha completado con éxito! Tu pedido está en preparación.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Página de confirmación y agradecimiento tras la compra.
     */
    public function success(string $orderNumber)
    {
        $order = Order::with(['items.manga.publisher', 'items.manga.category'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}
