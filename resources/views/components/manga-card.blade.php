@props(['manga'])

@php
    $categoryColors = [
        'shonen' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
        'seinen' => 'bg-violet-500/10 text-violet-400 border-violet-500/30',
        'shojo' => 'bg-pink-500/10 text-pink-400 border-pink-500/30',
        'dark-fantasy' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
    ];

    $catSlug = $manga->category?->slug ?? 'shonen';
    $badgeClass = $categoryColors[$catSlug] ?? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30';
@endphp

<article class="group relative flex flex-col rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-violet-500/50 hover:shadow-2xl hover:shadow-violet-500/10 transition-all duration-300 overflow-hidden backdrop-blur-sm">
    <!-- Portada con efecto de zoom y badges -->
    <div class="relative aspect-[3/4] w-full overflow-hidden bg-slate-950">
        <a href="{{ route('mangas.show', $manga->slug) }}" class="block w-full h-full">
            <img 
                src="{{ $manga->cover_image }}" 
                alt="{{ $manga->title }}" 
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                loading="lazy"
            >
        </a>

        <!-- Overlay con gradiente sutil -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-80 pointer-events-none"></div>

        <!-- Badges superiores -->
        <div class="absolute top-3 left-3 right-3 flex items-center justify-between pointer-events-none">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold tracking-wide uppercase border backdrop-blur-md {{ $badgeClass }}">
                {{ $manga->category?->name ?? 'Manga' }}
            </span>

            @if($manga->is_featured)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20">
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Top
                </span>
            @endif
        </div>

        <!-- Stock Status flotante -->
        <div class="absolute bottom-3 left-3 pointer-events-none">
            @if($manga->is_in_stock)
                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 font-medium bg-slate-950/80 px-2 py-0.5 rounded-md border border-emerald-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ $manga->stock }} en stock
                </span>
            @else
                <span class="inline-flex items-center gap-1 text-xs text-rose-400 font-medium bg-slate-950/80 px-2 py-0.5 rounded-md border border-rose-500/30">
                    Agotado
                </span>
            @endif
        </div>
    </div>

    <!-- Contenido del Card -->
    <div class="p-4 flex flex-col flex-grow justify-between gap-3">
        <div>
            <!-- Editorial -->
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                {{ $manga->publisher?->name ?? 'Editorial' }}
            </p>

            <!-- Título -->
            <h3 class="text-base font-bold text-slate-100 group-hover:text-violet-400 transition-colors line-clamp-1">
                <a href="{{ route('mangas.show', $manga->slug) }}">
                    {{ $manga->title }}
                </a>
            </h3>

            <!-- Autor -->
            <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">
                Por {{ $manga->author }}
            </p>
        </div>

        <!-- Pie: Precio y Botón de compra -->
        <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between gap-2">
            <div>
                <span class="text-xs text-slate-400 block leading-tight">Precio</span>
                <span class="text-lg font-extrabold text-white tracking-tight">
                    {{ $manga->formatted_price }}
                </span>
            </div>

            <!-- Botón añadir al carrito -->
            @if($manga->is_in_stock)
                <form action="{{ route('cart.add', $manga) }}" method="POST" class="inline-block">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center p-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white shadow-lg shadow-violet-600/20 hover:shadow-violet-500/40 transition-all duration-200 active:scale-95 group/btn"
                        title="Añadir al carrito"
                    >
                        <svg class="w-4 h-4 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="sr-only">Añadir</span>
                    </button>
                </form>
            @else
                <button 
                    disabled 
                    class="p-2.5 rounded-xl bg-slate-800 text-slate-500 cursor-not-allowed opacity-60"
                    title="Agotado"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
</article>
