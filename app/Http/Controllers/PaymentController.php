<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Muestra la vista del formulario de checkout.
     */
    public function showCheckout()
    {
        return view('checkout');
    }

    /**
     * Crear un PaymentIntent dinámico basado en el monto ingresado.
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Stripe procesa montos en unidades mínimas de la moneda (ej. centavos USD / EUR)
            $amountInCents = (int) round($request->amount * 100);

            // Obtener el nombre y email del usuario (del request o del usuario autenticado)
            $userName = $request->name ?? (auth()->check() ? auth()->user()->name : null);
            $userEmail = $request->email ?? (auth()->check() ? auth()->user()->email : null);

            $description = $userName
                ? "Pago de {$userName}"
                : ($request->description ?? 'Pago de prueba');

            // Crear o buscar el cliente en Stripe para que aparezca en el panel de Stripe
            $customerId = null;
            if ($userEmail || $userName) {
                if ($userEmail) {
                    $existing = Customer::all(['email' => $userEmail, 'limit' => 1]);
                    if (count($existing->data) > 0) {
                        $customer = $existing->data[0];
                        if (!$customer->name && $userName) {
                            Customer::update($customer->id, ['name' => $userName]);
                        }
                        $customerId = $customer->id;
                    }
                }

                if (!$customerId) {
                    $customerData = [];
                    if ($userName) {
                        $customerData['name'] = $userName;
                    }
                    if ($userEmail) {
                        $customerData['email'] = $userEmail;
                    }
                    $customer = Customer::create($customerData);
                    $customerId = $customer->id;
                }
            }

            $paymentIntentParams = [
                'amount' => $amountInCents,
                'currency' => strtolower($request->currency),
                'description' => $description,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'customer_name' => $userName ?? 'No especificado',
                    'customer_email' => $userEmail ?? 'No especificado',
                    'description' => $description,
                ],
            ];

            if ($customerId) {
                $paymentIntentParams['customer'] = $customerId;
            }

            if ($userEmail) {
                $paymentIntentParams['receipt_email'] = $userEmail;
            }

            $paymentIntent = PaymentIntent::create($paymentIntentParams);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'customerId' => $customerId,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Webhook para capturar la confirmación del pago.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException | SignatureVerificationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $customerName = $paymentIntent->metadata->customer_name ?? 'Cliente';
            Log::info("¡Pago exitoso recibido vía Webhook! Cliente: {$customerName}, ID: {$paymentIntent->id}, Monto: " . ($paymentIntent->amount / 100) . " {$paymentIntent->currency}");
        }

        return response()->json(['status' => 'success'], 200);
    }
}
