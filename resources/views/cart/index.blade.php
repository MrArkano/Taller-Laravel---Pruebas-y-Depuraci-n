@extends('layouts.app')

@section('title', 'Tu Carrito de Mangas | MANGA・VAULT')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Encabezado del Carrito -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-violet-400">Revisión de Pedido</span>
            <h1 class="text-3xl font-black font-display text-white tracking-tight">Tu Carrito de Colección</h1>
        </div>
        @if($cart->isNotEmpty())
            <div class="text-xs text-slate-400">
                Tienes <span class="text-white font-bold">{{ $summary['count'] }}</span> ejemplar(es) seleccionado(s)
            </div>
        @endif
    </div>

    @if($cart->isNotEmpty())

        <!-- Barra de Progreso hacia Envío Gratis -->
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-xs">
                @if($summary['amount_to_free_shipping'] > 0)
                    <span class="text-slate-300">
                        ¡Agrega <strong class="text-amber-400">${{ number_format($summary['amount_to_free_shipping'], 2) }}</strong> más para obtener <strong class="text-emerald-400">ENVÍO GRATIS</strong>!
                    </span>
                @else
                    <span class="text-emerald-400 font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        ¡Felicidades! Calificas para ENVÍO GRATIS en este pedido.
                    </span>
                @endif
                <span class="text-slate-400 font-medium">Meta: ${{ number_format($summary['free_shipping_threshold'], 2) }}</span>
            </div>

            @php
                $progressPercent = min(100, round(($summary['subtotal'] / $summary['free_shipping_threshold']) * 100));
            @endphp
            <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-violet-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
            </div>
        </div>

        <!-- Contenido en 2 Columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Columna Izquierda: Lista de Ítems -->
            <div class="lg:col-span-8 space-y-4">
                <div class="rounded-2xl bg-slate-900/60 border border-slate-800 divide-y divide-slate-800/80 overflow-hidden">
                    @foreach($cart as $item)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                            
                            <!-- Miniatura y Datos del Manga -->
                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                <div class="w-16 h-22 sm:w-20 sm:h-28 rounded-xl overflow-hidden bg-slate-950 border border-slate-800 shrink-0">
                                    <img src="{{ $item['cover_image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $item['format'] ?? 'Manga' }}</span>
                                    <h3 class="text-base font-bold text-white hover:text-violet-400 transition">
                                        <a href="{{ route('mangas.show', $item['slug']) }}">
                                            {{ $item['title'] }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-400">Por {{ $item['author'] }}</p>
                                    <span class="text-xs font-semibold text-violet-400 sm:hidden block">
                                        ${{ number_format($item['price'], 2) }} c/u
                                    </span>
                                </div>
                            </div>

                            <!-- Controles de Cantidad y Subtotal -->
                            <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-0 border-slate-800">
                                <div class="hidden sm:block text-right">
                                    <span class="text-xs text-slate-400 block">Unitario</span>
                                    <span class="text-sm font-semibold text-slate-200">${{ number_format($item['price'], 2) }}</span>
                                </div>

                                <!-- Actualizar Cantidad -->
                                <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="flex items-center bg-slate-950 border border-slate-800 rounded-xl overflow-hidden">
                                    @csrf
                                    @method('PUT')
                                    <button 
                                        type="submit" 
                                        name="quantity" 
                                        value="{{ $item['quantity'] - 1 }}" 
                                        class="px-2.5 py-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition"
                                        title="Reducir"
                                    >
                                        -
                                    </button>
                                    <span class="px-3 text-xs font-bold text-white">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button 
                                        type="submit" 
                                        name="quantity" 
                                        value="{{ $item['quantity'] + 1 }}" 
                                        class="px-2.5 py-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition"
                                        title="Aumentar"
                                        {{ $item['quantity'] >= $item['max_stock'] ? 'disabled' : '' }}
                                    >
                                        +
                                    </button>
                                </form>

                                <!-- Subtotal del ítem -->
                                <div class="text-right min-w-[70px]">
                                    <span class="text-xs text-slate-400 block">Subtotal</span>
                                    <span class="text-base font-black text-white">
                                        ${{ number_format($item['subtotal'], 2) }}
                                    </span>
                                </div>

                                <!-- Eliminar Ítem -->
                                <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition"
                                        title="Quitar del carrito"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Botones Inferiores -->
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('mangas.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-white transition">
                        &larr; Continuar Comprando
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-rose-400 hover:text-rose-300 transition">
                            Vaciar Carrito Completo
                        </button>
                    </form>
                </div>
            </div>

            <!-- Columna Derecha: Resumen de Orden -->
            <div class="lg:col-span-4 space-y-4">
                <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-5 shadow-2xl backdrop-blur-md">
                    <h2 class="text-lg font-black font-display text-white">Resumen del Pedido</h2>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal ({{ $summary['count'] }} productos)</span>
                            <span class="text-white font-semibold">${{ number_format($summary['subtotal'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Impuestos estimados (IVA 10%)</span>
                            <span class="text-white font-semibold">${{ number_format($summary['tax'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Costo de Envío</span>
                            @if($summary['shipping'] == 0)
                                <span class="text-emerald-400 font-bold uppercase tracking-wider">Gratis</span>
                            @else
                                <span class="text-white font-semibold">${{ number_format($summary['shipping'], 2) }}</span>
                            @endif
                        </div>
                        <div class="pt-3 border-t border-slate-800 flex justify-between items-baseline text-base">
                            <span class="font-bold text-white">Total Estimado</span>
                            <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-indigo-300">
                                ${{ number_format($summary['total'], 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Botón Ir a Checkout -->
                    <a 
                        href="{{ route('checkout.index') }}" 
                        class="w-full py-3.5 px-6 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm shadow-xl shadow-violet-600/30 hover:shadow-violet-500/50 transition-all duration-200 flex items-center justify-center gap-2 active:scale-95 text-center"
                    >
                        <span>Proceder al Checkout</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>

                    <!-- Sellos de Seguridad -->
                    <div class="pt-4 border-t border-slate-800/80 space-y-2 text-[11px] text-slate-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Compra 100% segura y protegida</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Garantía de cambio en tomos con falla de imprenta</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @else

        <!-- Estado Vacío del Carrito -->
        <div class="text-center py-20 px-4 rounded-3xl bg-slate-900/40 border border-slate-800 max-w-2xl mx-auto space-y-6">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-slate-800 flex items-center justify-center text-slate-500 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </div>
            <div class="space-y-2">
                <h2 class="text-2xl font-black font-display text-white">Tu carrito está esperando su próxima gran historia</h2>
                <p class="text-sm text-slate-400 max-w-md mx-auto">
                    Todavía no has agregado mangas a tu selección. Explora nuestro catálogo y colecciona tus series favoritas.
                </p>
            </div>
            <div>
                <a href="{{ route('mangas.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm shadow-xl shadow-violet-600/30 transition-all duration-200">
                    <span>Explorar Catálogo de Mangas</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

    @endif

</div>
@endsection
