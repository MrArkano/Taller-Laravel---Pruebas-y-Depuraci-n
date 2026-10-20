@extends('layouts.app')

@section('title', 'Finalizar Compra con Stripe | MANGA・VAULT')

@section('content')
<!-- SDK oficial de Stripe.js v3 -->
<script src="https://js.stripe.com/v3/"></script>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Encabezado de Checkout -->
    <div class="pb-6 border-b border-slate-800">
        <span class="text-xs font-bold uppercase tracking-wider text-violet-400">Paso Final</span>
        <h1 class="text-3xl font-black font-display text-white tracking-tight">Finalizar tu Pedido</h1>
        <p class="text-xs text-slate-400 mt-1">Completa tus datos de envío e ingresa tu tarjeta para procesar el pago seguro con Stripe.</p>
    </div>

    <!-- Formulario Principal -->
    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @csrf
        <input type="hidden" name="payment_intent_id" id="payment_intent_id" value="">

        <!-- Columna Izquierda: Datos de Envío y Método de Pago -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Sección 1: Información de Contacto -->
            <div class="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                    <span class="w-6 h-6 rounded-full bg-violet-600/20 text-violet-400 text-xs font-bold flex items-center justify-center border border-violet-500/30">1</span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Datos del Destinatario</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nombre Completo -->
                    <div class="sm:col-span-2">
                        <label for="customer_name" class="block text-xs font-semibold text-slate-300 mb-1.5">Nombre y Apellido *</label>
                        <input 
                            type="text" 
                            id="customer_name" 
                            name="customer_name" 
                            value="{{ old('customer_name', auth()->user()?->name) }}"
                            placeholder="Ej. Jorge Enrique Balaguera" 
                            class="w-full px-3.5 py-2.5 text-xs bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 @error('customer_name') border-rose-500 @enderror"
                            required
                        >
                        @error('customer_name') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Correo Electrónico -->
                    <div>
                        <label for="customer_email" class="block text-xs font-semibold text-slate-300 mb-1.5">Correo Electrónico *</label>
                        <input 
                            type="email" 
                            id="customer_email" 
                            name="customer_email" 
                            value="{{ old('customer_email', auth()->user()?->email) }}"
                            placeholder="jbalaguera@ejemplo.com" 
                            class="w-full px-3.5 py-2.5 text-xs bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 @error('customer_email') border-rose-500 @enderror"
                            required
                        >
                        @error('customer_email') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="customer_phone" class="block text-xs font-semibold text-slate-300 mb-1.5">Teléfono / Celular</label>
                        <input 
                            type="tel" 
                            id="customer_phone" 
                            name="customer_phone" 
                            value="{{ old('customer_phone') }}"
                            placeholder="+57 300 123 4567" 
                            class="w-full px-3.5 py-2.5 text-xs bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500"
                        >
                    </div>
                </div>
            </div>

            <!-- Sección 2: Dirección de Envío -->
            <div class="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                    <span class="w-6 h-6 rounded-full bg-violet-600/20 text-violet-400 text-xs font-bold flex items-center justify-center border border-violet-500/30">2</span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Dirección de Entrega</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Dirección -->
                    <div class="sm:col-span-2">
                        <label for="shipping_address" class="block text-xs font-semibold text-slate-300 mb-1.5">Dirección Completa (Calle, Carrera, Apto / Casa) *</label>
                        <input 
                            type="text" 
                            id="shipping_address" 
                            name="shipping_address" 
                            value="{{ old('shipping_address') }}"
                            placeholder="Calle 123 # 45 - 67, Apto 802" 
                            class="w-full px-3.5 py-2.5 text-xs bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 @error('shipping_address') border-rose-500 @enderror"
                            required
                        >
                        @error('shipping_address') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ciudad -->
                    <div>
                        <label for="city" class="block text-xs font-semibold text-slate-300 mb-1.5">Ciudad / Municipio *</label>
                        <input 
                            type="text" 
                            id="city" 
                            name="city" 
                            value="{{ old('city') }}"
                            placeholder="Bucaramanga / Bogotá / Medellín" 
                            class="w-full px-3.5 py-2.5 text-xs bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 @error('city') border-rose-500 @enderror"
                            required
                        >
                        @error('city') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Código Postal -->
                    <div>
                        <label for="postal_code" class="block text-xs font-semibold text-slate-300 mb-1.5">Código Postal</label>
                        <input 
                            type="text" 
                            id="postal_code" 
                            name="postal_code" 
                            value="{{ old('postal_code') }}"
                            placeholder="680003" 
                            class="w-full px-3.5 py-2.5 text-xs bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500"
                        >
                    </div>

                    <!-- Notas Adicionales -->
                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-xs font-semibold text-slate-300 mb-1.5">Instrucciones de Entrega (Opcional)</label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="2" 
                            placeholder="Dejar en portería o timbre 201..." 
                            class="w-full px-3.5 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500"
                        >{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Método de Pago e Integración de Stripe -->
            <div class="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                    <span class="w-6 h-6 rounded-full bg-violet-600/20 text-violet-400 text-xs font-bold flex items-center justify-center border border-violet-500/30">3</span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Método de Pago</h2>
                </div>

                <div class="space-y-3">
                    <!-- Opción 1: Tarjeta de Crédito (Stripe) -->
                    <label class="flex items-center justify-between p-4 rounded-xl bg-slate-950 border border-violet-500/50 cursor-pointer transition hover:bg-slate-900/60">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="card" checked onchange="togglePaymentContainers()" class="text-violet-600 focus:ring-violet-500">
                            <div>
                                <span class="text-xs font-bold text-white block">Tarjeta de Crédito / Débito (Stripe)</span>
                                <span class="text-[11px] text-slate-400">Procesamiento seguro instantáneo</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-900 border border-slate-800 text-slate-300">VISA</span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-900 border border-slate-800 text-slate-300">MC</span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-900 border border-slate-800 text-slate-300">AMEX</span>
                        </div>
                    </label>

                    <!-- CONTENEDOR DONDE STRIPE ELEMENTS RENDERIZA EL FORMULARIO SEGURO DE TARJETA -->
                    <div id="stripe-card-container" class="p-4 rounded-2xl bg-slate-950 border border-violet-500/30 space-y-3">
                        <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-800/80">
                            <span class="font-semibold text-slate-200">Datos de la Tarjeta</span>
                            <span class="flex items-center gap-1 text-[11px] text-emerald-400 font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Cifrado TLS 256 bits
                            </span>
                        </div>

                        <!-- Elemento montado por Stripe -->
                        <div id="payment-element" class="min-h-[140px] pt-1">
                            <div class="flex items-center justify-center py-8 text-xs text-slate-400">
                                <span class="animate-pulse">Cargando formulario seguro de Stripe...</span>
                            </div>
                        </div>

                        <!-- Contenedor para mensajes de error de Stripe -->
                        <div id="payment-message" class="hidden p-3 rounded-xl text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30"></div>
                    </div>

                    <!-- Opción 2: Transferencia Directa -->
                    <label class="flex items-center justify-between p-4 rounded-xl bg-slate-950 border border-slate-800 cursor-pointer transition hover:bg-slate-900/60">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="transfer" onchange="togglePaymentContainers()" class="text-violet-600 focus:ring-violet-500">
                            <div>
                                <span class="text-xs font-bold text-white block">Transferencia Bancaria / PSE</span>
                                <span class="text-[11px] text-slate-400">Recibirás los datos bancarios al confirmar</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-violet-400">PSE / Bancolombia</span>
                    </label>

                    <!-- Opción 3: PayPal -->
                    <label class="flex items-center justify-between p-4 rounded-xl bg-slate-950 border border-slate-800 cursor-pointer transition hover:bg-slate-900/60">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="paypal" onchange="togglePaymentContainers()" class="text-violet-600 focus:ring-violet-500">
                            <div>
                                <span class="text-xs font-bold text-white block">PayPal Billetera Digital</span>
                                <span class="text-[11px] text-slate-400">Protección al comprador internacional</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-sky-400">PayPal</span>
                    </label>
                </div>
            </div>

        </div>

        <!-- Columna Derecha: Resumen de Orden y Confirmación -->
        <div class="lg:col-span-5 space-y-4">
            <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-5 shadow-2xl sticky top-28 backdrop-blur-md">
                <h2 class="text-base font-black font-display text-white">Resumen de tu Compra</h2>

                <!-- Mini Lista de Ítems en el Carrito -->
                <div class="max-h-60 overflow-y-auto space-y-3 pr-1 divide-y divide-slate-800/60 text-xs">
                    @foreach($items as $item)
                        <div class="pt-3 first:pt-0 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $item['cover_image'] }}" alt="{{ $item['title'] }}" class="w-10 h-14 rounded-lg object-cover shrink-0 bg-slate-950">
                                <div>
                                    <h4 class="font-bold text-white line-clamp-1">{{ $item['title'] }}</h4>
                                    <p class="text-[11px] text-slate-400">{{ $item['quantity'] }} × ${{ number_format($item['price'], 2) }}</p>
                                </div>
                            </div>
                            <span class="font-bold text-white shrink-0">${{ number_format($item['subtotal'], 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Totales -->
                <div class="pt-4 border-t border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal</span>
                        <span class="text-white font-semibold">${{ number_format($summary['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>IVA (10%)</span>
                        <span class="text-white font-semibold">${{ number_format($summary['tax'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Costo de Envío</span>
                        @if($summary['shipping'] == 0)
                            <span class="text-emerald-400 font-bold uppercase">Gratis</span>
                        @else
                            <span class="text-white font-semibold">${{ number_format($summary['shipping'], 2) }}</span>
                        @endif
                    </div>
                    <div class="pt-3 border-t border-slate-800 flex justify-between items-baseline">
                        <span class="font-bold text-white text-sm">Total a Pagar</span>
                        <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-indigo-300">
                            ${{ number_format($summary['total'], 2) }}
                        </span>
                    </div>
                </div>

                <!-- Botón de Confirmación -->
                <button 
                    type="submit" 
                    id="submit-order-btn"
                    class="w-full py-4 px-6 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm shadow-xl shadow-violet-600/30 hover:shadow-violet-500/50 transition-all duration-200 flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg id="btn-lock-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span id="btn-text">Pagar ${{ number_format($summary['total'], 2) }} con Stripe</span>
                </button>

                <p class="text-[11px] text-center text-slate-500">
                    Al confirmar tu pedido, aceptas los términos de servicio y políticas de entrega de MANGA・VAULT.
                </p>
            </div>
        </div>

    </form>

</div>

<!-- Script de Integración Completa con Stripe.js -->
<script>
    const stripeKey = "{{ $stripeKey }}";
    const clientSecret = "{{ $clientSecret }}";

    let stripe = null;
    let elements = null;

    // Inicializar Stripe con tema oscuro adaptado al diseño de la tienda
    if (stripeKey && clientSecret) {
        stripe = Stripe(stripeKey);

        elements = stripe.elements({
            clientSecret: clientSecret,
            appearance: {
                theme: 'night',
                variables: {
                    colorPrimary: '#8b5cf6',
                    colorBackground: '#020617',
                    colorText: '#f8fafc',
                    colorDanger: '#fb7185',
                    fontFamily: 'Outfit, sans-serif',
                    borderRadius: '12px',
                    spacingUnit: '4px',
                },
                rules: {
                    '.Input': {
                        border: '1px solid #1e293b',
                        boxShadow: 'none',
                    },
                    '.Input:focus': {
                        border: '1px solid #8b5cf6',
                    }
                }
            }
        });

        const paymentElement = elements.create('payment', {
            layout: 'tabs'
        });

        paymentElement.mount('#payment-element');
    }

    function togglePaymentContainers() {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const stripeContainer = document.getElementById('stripe-card-container');
        const btnText = document.getElementById('btn-text');

        if (method === 'card') {
            stripeContainer.classList.remove('hidden');
            btnText.innerText = "Pagar ${{ number_format($summary['total'], 2) }} con Stripe";
        } else {
            stripeContainer.classList.add('hidden');
            btnText.innerText = "Confirmar Pedido (${{ number_format($summary['total'], 2) }})";
        }
    }

    // Manejar envío del formulario y confirmación de pago con Stripe
    const checkoutForm = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('submit-order-btn');
    const btnText = document.getElementById('btn-text');
    const paymentMessage = document.getElementById('payment-message');

    checkoutForm.addEventListener('submit', async (e) => {
        const method = document.querySelector('input[name="payment_method"]:checked').value;

        // Si el método no es tarjeta, se envía el formulario directamente
        if (method !== 'card') {
            return;
        }

        // Si es tarjeta, interceptar para procesar a través de Stripe Elements
        e.preventDefault();

        // 1. Validar campos requeridos de envío
        if (!checkoutForm.reportValidity()) {
            return;
        }

        if (!stripe || !elements) {
            showMessage("Error al conectar con la pasarela de pagos. Por favor recarga la página.", true);
            return;
        }

        const customerName = document.getElementById('customer_name').value.trim();
        const customerEmail = document.getElementById('customer_email').value.trim();
        const shippingAddress = document.getElementById('shipping_address').value.trim();
        const city = document.getElementById('city').value.trim();
        const postalCode = document.getElementById('postal_code').value.trim();

        // Deshabilitar botón mientras procesa
        submitBtn.disabled = true;
        btnText.innerHTML = `<span class="inline-block animate-spin mr-2">⟳</span> Procesando pago seguro con Stripe...`;
        hideMessage();

        try {
            // Confirmar pago directamente con Stripe
            const { error, paymentIntent } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: window.location.href,
                    payment_method_data: {
                        billing_details: {
                            name: customerName,
                            email: customerEmail,
                            address: {
                                line1: shippingAddress,
                                city: city,
                                postal_code: postalCode || undefined,
                            }
                        }
                    }
                },
                redirect: 'if_required' // Permite completar el flujo inline sin abandonar la página
            });

            if (error) {
                showMessage(error.message || "Ocurrió un error al procesar la tarjeta.", true);
                submitBtn.disabled = false;
                btnText.innerText = "Pagar ${{ number_format($summary['total'], 2) }} con Stripe";
            } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                // Pago exitoso en Stripe -> Asignar ID al input oculto y enviar al backend para registrar la orden
                document.getElementById('payment_intent_id').value = paymentIntent.id;
                btnText.innerHTML = `¡Pago aprobado! Generando orden...`;
                checkoutForm.submit();
            } else {
                showMessage("Estado de pago no completado. Intente nuevamente.", true);
                submitBtn.disabled = false;
                btnText.innerText = "Pagar ${{ number_format($summary['total'], 2) }} con Stripe";
            }
        } catch (err) {
            showMessage("Error de comunicación con Stripe: " + err.message, true);
            submitBtn.disabled = false;
            btnText.innerText = "Pagar ${{ number_format($summary['total'], 2) }} con Stripe";
        }
    });

    function showMessage(msg, isError = true) {
        paymentMessage.classList.remove('hidden');
        paymentMessage.innerText = msg;
    }

    function hideMessage() {
        paymentMessage.classList.add('hidden');
        paymentMessage.innerText = '';
    }
</script>
@endsection
