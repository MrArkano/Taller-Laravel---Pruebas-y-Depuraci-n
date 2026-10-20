@extends('layouts.app')

@section('title', $manga->title . ' | MANGA・VAULT')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium">
        <a href="{{ route('mangas.index') }}" class="hover:text-violet-400 transition">Catálogo</a>
        <span class="text-slate-600">/</span>
        <a href="{{ route('mangas.index', ['category' => $manga->category?->slug]) }}" class="hover:text-violet-400 transition">
            {{ $manga->category?->name ?? 'Manga' }}
        </a>
        <span class="text-slate-600">/</span>
        <span class="text-slate-200 truncate">{{ $manga->title }}</span>
    </nav>

    <!-- Ficha Principal de Producto -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">

        <!-- Columna Izquierda: Portada con atmósfera glow -->
        <div class="lg:col-span-5 flex flex-col items-center">
            <div class="relative w-full max-w-md aspect-[3/4] rounded-2xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl group">
                <img 
                    src="{{ $manga->cover_image }}" 
                    alt="{{ $manga->title }}" 
                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700"
                >
                <!-- Glow ambiental detrás de la portada -->
                <div class="absolute -inset-1 bg-gradient-to-tr from-violet-600/20 to-fuchsia-600/20 blur-2xl -z-10 rounded-2xl"></div>
            </div>

            <!-- Beneficios directos de compra -->
            <div class="w-full max-w-md grid grid-cols-3 gap-2 mt-6 p-4 rounded-xl bg-slate-900/50 border border-slate-800/80 text-center text-slate-300">
                <div>
                    <svg class="w-5 h-5 mx-auto text-violet-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-[11px] font-semibold block">100% Original</span>
                </div>
                <div>
                    <svg class="w-5 h-5 mx-auto text-violet-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[11px] font-semibold block">Envío Rápido</span>
                </div>
                <div>
                    <svg class="w-5 h-5 mx-auto text-violet-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="text-[11px] font-semibold block">Pago Cifrado</span>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Información, Sinopsis y Compra -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Badges de Metadatos -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-violet-500/10 text-violet-400 border border-violet-500/30">
                    {{ $manga->category?->name ?? 'Manga' }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                    {{ $manga->publisher?->name ?? 'Editorial' }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                    {{ $manga->format }}
                </span>
            </div>

            <!-- Título y Autor -->
            <div>
                <h1 class="text-3xl sm:text-4xl font-black font-display text-white tracking-tight leading-tight">
                    {{ $manga->title }}
                </h1>
                <p class="text-sm font-medium text-slate-400 mt-1">
                    Historia y Arte por <span class="text-violet-400 font-semibold">{{ $manga->author }}</span>
                </p>
            </div>

            <!-- Precio y Disponibilidad -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between gap-4">
                <div>
                    <span class="text-xs text-slate-400 block">Precio de venta</span>
                    <span class="text-3xl font-black text-white tracking-tight">
                        {{ $manga->formatted_price }}
                    </span>
                </div>

                <div>
                    @if($manga->is_in_stock)
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                {{ $manga->stock }} unidades disponibles
                            </span>
                            <span class="block text-[11px] text-slate-500 mt-1">Despacho inmediato</span>
                        </div>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                            Agotado temporalmente
                        </span>
                    @endif
                </div>
            </div>

            <!-- Selector de Cantidad y Acciones de Compra -->
            @if($manga->is_in_stock)
                <form action="{{ route('cart.add', $manga) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex items-center gap-4">
                        <label for="qty" class="text-xs font-bold uppercase tracking-wider text-slate-300">
                            Cantidad:
                        </label>
                        <div class="flex items-center bg-slate-900 border border-slate-800 rounded-xl overflow-hidden w-32">
                            <button 
                                type="button" 
                                onclick="const input = document.getElementById('qty'); input.value = Math.max(1, parseInt(input.value) - 1);" 
                                class="px-3 py-2 text-slate-400 hover:text-white hover:bg-slate-800 transition"
                            >
                                -
                            </button>
                            <input 
                                type="number" 
                                id="qty" 
                                name="quantity" 
                                value="1" 
                                min="1" 
                                max="{{ $manga->stock }}" 
                                class="w-full text-center bg-transparent text-sm font-bold text-white focus:outline-none" 
                                readonly
                            >
                            <button 
                                type="button" 
                                onclick="const input = document.getElementById('qty'); input.value = Math.min({{ $manga->stock }}, parseInt(input.value) + 1);" 
                                class="px-3 py-2 text-slate-400 hover:text-white hover:bg-slate-800 transition"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button 
                            type="submit" 
                            class="flex-1 py-3.5 px-6 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm shadow-xl shadow-violet-600/30 hover:shadow-violet-500/50 transition-all duration-200 flex items-center justify-center gap-2 active:scale-95"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                            Añadir al Carrito
                        </button>
                    </div>
                </form>
            @endif

            <!-- Sinopsis -->
            <div class="space-y-3 pt-4 border-t border-slate-800">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">
                    Sinopsis Oficial
                </h3>
                <p class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                    {{ $manga->synopsis }}
                </p>
            </div>

            <!-- Ficha Técnica Completa -->
            <div class="space-y-3 pt-4 border-t border-slate-800">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">
                    Ficha Técnica del Volumen
                </h3>
                <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                        <dt class="text-slate-400 font-medium">ISBN-13</dt>
                        <dd class="text-white font-bold mt-0.5">{{ $manga->isbn ?? '978-XXXXX' }}</dd>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                        <dt class="text-slate-400 font-medium">Páginas</dt>
                        <dd class="text-white font-bold mt-0.5">{{ $manga->pages ?? 192 }} págs.</dd>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                        <dt class="text-slate-400 font-medium">Formato</dt>
                        <dd class="text-white font-bold mt-0.5">{{ $manga->format }}</dd>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                        <dt class="text-slate-400 font-medium">Editorial</dt>
                        <dd class="text-white font-bold mt-0.5">{{ $manga->publisher?->name }}</dd>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                        <dt class="text-slate-400 font-medium">Origen / Idioma</dt>
                        <dd class="text-white font-bold mt-0.5">{{ $manga->publisher?->country }} (Español)</dd>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                        <dt class="text-slate-400 font-medium">Demografía</dt>
                        <dd class="text-white font-bold mt-0.5">{{ $manga->category?->name }}</dd>
                    </div>
                </dl>
            </div>

        </div>

    </div>

    <!-- Títulos Relacionados -->
    @if($relatedMangas->isNotEmpty())
        <section class="pt-12 border-t border-slate-800/80 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-violet-400 uppercase tracking-wider">Recomendados para ti</span>
                    <h2 class="text-2xl font-black font-display text-white">Más títulos de {{ $manga->category?->name }}</h2>
                </div>
                <a href="{{ route('mangas.index', ['category' => $manga->category?->slug]) }}" class="text-xs font-bold text-violet-400 hover:text-violet-300">
                    Ver todos &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedMangas as $rel)
                    <x-manga-card :manga="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
