@extends('layouts.app')

@section('title', '¡Pedido Confirmado! | MANGA・VAULT')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">

    <!-- Tarjeta de Confirmación -->
    <div class="p-8 sm:p-12 rounded-3xl bg-slate-900/80 border border-slate-800 text-center space-y-6 shadow-2xl backdrop-blur-md relative overflow-hidden">
        <!-- Glows -->
        <div class="absolute -top-12 -left-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-violet-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Icono Éxito -->
        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shadow-xl shadow-emerald-500/20">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>

        <div class="space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                Pago y Pedido Aprobado
            </span>
            <h1 class="text-3xl sm:text-4xl font-black font-display text-white tracking-tight">
                ¡Gracias por tu compra, {{ $order->customer_name }}!
            </h1>
            <p class="text-sm text-slate-400 max-w-md mx-auto">
                Hemos recibido tu pedido correctamente. Enviamos el comprobante y guía de seguimiento a <strong class="text-white">{{ $order->customer_email }}</strong>.
            </p>
        </div>

        <!-- Datos Clave de la Orden -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-950/80 border border-slate-800 text-left text-xs">
            <div>
                <span class="text-slate-400 block">Número de Orden</span>
                <strong class="text-violet-400 font-mono text-sm block mt-0.5">{{ $order->order_number }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block">Fecha de Compra</span>
                <strong class="text-white block mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block">Método de Pago</span>
                <strong class="text-white capitalize block mt-0.5">{{ $order->payment_method }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block">Total Pagado</span>
                <strong class="text-emerald-400 text-sm block mt-0.5">${{ number_format($order->total, 2) }}</strong>
            </div>
        </div>

        <!-- Lista de Tomos Adquiridos -->
        <div class="text-left space-y-3 pt-4 border-t border-slate-800">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">
                Tomos en este envío ({{ $order->items->count() }})
            </h3>
            <div class="divide-y divide-slate-800/80 rounded-2xl bg-slate-950/50 border border-slate-800 overflow-hidden text-xs">
                @foreach($order->items as $item)
                    <div class="p-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->manga?->cover_image }}" alt="{{ $item->manga?->title }}" class="w-12 h-16 rounded-lg object-cover bg-slate-900 shrink-0">
                            <div>
                                <h4 class="font-bold text-white">{{ $item->manga?->title }}</h4>
                                <p class="text-slate-400 text-[11px]">{{ $item->manga?->publisher?->name }} • {{ $item->manga?->format }}</p>
                                <span class="text-slate-400 text-[11px]">Cantidad: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}</span>
                            </div>
                        </div>
                        <span class="font-black text-white text-sm">
                            ${{ number_format($item->total_price, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Dirección de Envío Confirmada -->
        <div class="text-left p-4 rounded-xl bg-slate-950/50 border border-slate-800 text-xs space-y-1">
            <span class="text-slate-400 block font-semibold">Dirección de Destino:</span>
            <p class="text-white">{{ $order->shipping_address }}, {{ $order->city }} @if($order->postal_code) (CP: {{ $order->postal_code }}) @endif</p>
            @if($order->notes)
                <p class="text-slate-400 italic">Notas: "{{ $order->notes }}"</p>
            @endif
        </div>

        <!-- Botones de Acción -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            <a href="{{ route('mangas.index') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-xl shadow-violet-600/30 transition">
                Volver al Catálogo de Mangas
            </a>
            <button onclick="window.print()" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition">
                Imprimir Comprobante
            </button>
        </div>
    </div>

</div>
@endsection
