@extends('layouts.app')

@section('title', 'Catálogo de Mangas | MANGA・VAULT')

@section('content')
<div class="space-y-12 pb-16">

    <!-- Hero Section con Mangas Destacados -->
    <section class="relative overflow-hidden bg-gradient-to-b from-slate-900 via-slate-950 to-slate-950 border-b border-slate-800/80 pt-8 pb-16">
        <!-- Glows de fondo -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-violet-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 right-1/4 w-96 h-96 bg-fuchsia-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                <!-- Texto de Bienvenida -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400 text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-violet-400 animate-ping"></span>
                        Nuevas Ediciones Coleccionista 2026
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black font-display tracking-tight text-white leading-tight">
                        Colecciona los <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-indigo-300 to-fuchsia-400">mejores títulos</span> del universo manga.
                    </h1>

                    <p class="text-base sm:text-lg text-slate-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Ediciones oficiales de Panini, Ivrea, Norma y Milky Way. Tomos Tankobon y Kanzenban de lujo con envío rápido y seguro directo a tu puerta.
                    </p>

                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="#catalogo" class="px-6 py-3 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm shadow-xl shadow-violet-600/25 transition-all duration-200 active:scale-95">
                            Explorar Catálogo
                        </a>
                        <a href="{{ route('mangas.index', ['category' => 'seinen']) }}" class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-200 font-bold text-sm transition-all duration-200">
                            Ver Sección Seinen
                        </a>
                    </div>

                    <!-- Datos rápidos de confianza -->
                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-800/60 max-w-lg mx-auto lg:mx-0">
                        <div>
                            <span class="block text-xl font-extrabold text-white">100%</span>
                            <span class="text-xs text-slate-400">Originales Sellados</span>
                        </div>
                        <div>
                            <span class="block text-xl font-extrabold text-white">24/48h</span>
                            <span class="text-xs text-slate-400">Despacho Rápido</span>
                        </div>
                        <div>
                            <span class="block text-xl font-extrabold text-white">+50 USD</span>
                            <span class="text-xs text-slate-400">Envío Gratis</span>
                        </div>
                    </div>
                </div>

                <!-- Showcase de Mangas Destacados (Hero Cards) -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-sm">
                        @if($featuredMangas->isNotEmpty())
                            @php $featured = $featuredMangas->first(); @endphp
                            <div class="relative rounded-2xl p-4 bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 shadow-2xl">
                                <div class="relative aspect-[3/4] rounded-xl overflow-hidden mb-4">
                                    <img src="{{ $featured->cover_image }}" alt="{{ $featured->title }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                                    <div class="absolute bottom-3 left-3 right-3 flex justify-between items-end">
                                        <div>
                                            <span class="text-xs font-bold text-violet-400 uppercase tracking-wider block">Destacado de la semana</span>
                                            <h3 class="text-lg font-black text-white leading-tight">{{ $featured->title }}</h3>
                                        </div>
                                        <span class="text-xl font-black text-amber-400">{{ $featured->formatted_price }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('mangas.show', $featured->slug) }}" class="flex-1 py-2 text-center text-xs font-bold rounded-lg bg-violet-600 hover:bg-violet-500 text-white transition">
                                        Ver Detalles
                                    </a>
                                    <form action="{{ route('cart.add', $featured) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition" title="Añadir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Catálogo Principal con Filtros y Grid -->
    <div id="catalogo" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Barra de Filtros y Ordenamiento -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900/60 border border-slate-800/80 backdrop-blur-md">
            
            <!-- Pills de Categorías -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 scrollbar-none text-xs font-semibold">
                <a 
                    href="{{ route('mangas.index', array_merge(request()->except('category', 'page'))) }}" 
                    class="px-3.5 py-2 rounded-xl transition-all whitespace-nowrap {{ !request('category') ? 'bg-violet-600 text-white shadow-md shadow-violet-600/30' : 'bg-slate-800/60 text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                >
                    Todos los Tomos
                </a>
                @foreach($categories as $cat)
                    <a 
                        href="{{ route('mangas.index', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}" 
                        class="px-3.5 py-2 rounded-xl transition-all whitespace-nowrap {{ request('category') === $cat->slug ? 'bg-violet-600 text-white shadow-md shadow-violet-600/30' : 'bg-slate-800/60 text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        {{ $cat->name }} <span class="opacity-60 text-[10px]">({{ $cat->mangas_count }})</span>
                    </a>
                @endforeach
            </div>

            <!-- Controles: Editorial y Orden -->
            <form action="{{ route('mangas.index') }}" method="GET" class="flex items-center gap-3 shrink-0">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif

                <!-- Filtro por Editorial -->
                <select 
                    name="publisher" 
                    onchange="this.form.submit()" 
                    class="text-xs bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-violet-500 cursor-pointer"
                >
                    <option value="">Todas las Editoriales</option>
                    @foreach($publishers as $pub)
                        <option value="{{ $pub->slug }}" {{ request('publisher') === $pub->slug ? 'selected' : '' }}>
                            {{ $pub->name }} ({{ $pub->mangas_count }})
                        </option>
                    @endforeach
                </select>

                <!-- Selector de Orden -->
                <select 
                    name="sort" 
                    onchange="this.form.submit()" 
                    class="text-xs bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-violet-500 cursor-pointer"
                >
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Más recientes</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                </select>
            </form>

        </div>

        <!-- Resultados y Búsqueda Activa -->
        @if(request('q') || request('category') || request('publisher'))
            <div class="flex items-center justify-between text-xs text-slate-400">
                <div>
                    Mostrando resultados para:
                    @if(request('q')) <span class="text-white font-semibold">"{{ request('q') }}"</span> @endif
                    @if(request('category')) <span class="text-violet-400 font-semibold">• Categoría: {{ request('category') }}</span> @endif
                    @if(request('publisher')) <span class="text-indigo-400 font-semibold">• Editorial: {{ request('publisher') }}</span> @endif
                </div>
                <a href="{{ route('mangas.index') }}" class="text-rose-400 hover:underline">
                    Limpiar Filtros
                </a>
            </div>
        @endif

        <!-- Grid de Productos -->
        @if($mangas->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($mangas as $manga)
                    <x-manga-card :manga="$manga" />
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="pt-6">
                {{ $mangas->links() }}
            </div>
        @else
            <!-- Estado Vacío -->
            <div class="text-center py-16 px-4 rounded-2xl bg-slate-900/40 border border-slate-800">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-800 flex items-center justify-center text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">No se encontraron títulos</h3>
                <p class="text-xs text-slate-400 mb-6 max-w-sm mx-auto">
                    Intenta ajustar tus filtros de búsqueda o explora otras demografías.
                </p>
                <a href="{{ route('mangas.index') }}" class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-xs font-bold transition">
                    Ver Todos los Mangas
                </a>
            </div>
        @endif

    </div>

</div>
@endsection
