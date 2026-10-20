@inject('cartService', 'App\Services\CartService')
<!DOCTYPE html>
<html lang="es" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MANGA・VAULT | Tienda Virtual de Manga')</title>

    <!-- Google Fonts (Outfit & Space Grotesk) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    <!-- Compilación con Vite + Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fallback CDN de Tailwind CSS para garantizar visualización inmediata -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            900: '#4c1d95',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .font-display {
            font-family: 'Space Grotesk', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col selection:bg-violet-600 selection:text-white antialiased">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-xl border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">

                <!-- Logo de la Tienda -->
                <a href="{{ route('mangas.index') }}" class="flex items-center gap-3 group shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-violet-600 via-indigo-600 to-fuchsia-500 flex items-center justify-center shadow-lg shadow-violet-600/30 group-hover:scale-105 transition-transform duration-300">
                        <span class="text-white font-black text-xl leading-none">漫</span>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white font-display block leading-none">
                            MANGA<span class="text-violet-400">・VAULT</span>
                        </span>
                        <span class="text-[10px] tracking-widest text-slate-400 font-semibold uppercase block mt-0.5">
                            Comics & E-Commerce
                        </span>
                    </div>
                </a>

                <!-- Barra de Búsqueda Global -->
                <div class="hidden md:flex flex-1 max-w-md mx-4">
                    <form action="{{ route('mangas.index') }}" method="GET" class="w-full relative">
                        <input 
                            type="search" 
                            name="q" 
                            value="{{ request('q') }}"
                            placeholder="Buscar por título, autor o palabra clave..." 
                            class="w-full pl-10 pr-4 py-2 text-sm bg-slate-900 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-all shadow-inner"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>
                </div>

                <!-- Navegación & Carrito -->
                <div class="flex items-center gap-3 sm:gap-5">
                    <nav class="hidden lg:flex items-center gap-6 text-sm font-medium text-slate-300">
                        <a href="{{ route('mangas.index') }}" class="hover:text-violet-400 transition-colors {{ request()->routeIs('mangas.index') ? 'text-violet-400 font-bold' : '' }}">
                            Catálogo
                        </a>
                        <a href="{{ route('mangas.index', ['category' => 'shonen']) }}" class="hover:text-amber-400 transition-colors">
                            Shonen
                        </a>
                        <a href="{{ route('mangas.index', ['category' => 'seinen']) }}" class="hover:text-violet-400 transition-colors">
                            Seinen
                        </a>
                        <a href="{{ route('mangas.index', ['category' => 'dark-fantasy']) }}" class="hover:text-rose-400 transition-colors">
                            Dark Fantasy
                        </a>
                    </nav>

                    <!-- Ícono de Carrito con Conteo Dinámico -->
                    @php $cartCount = $cartService->getItemsCount(); @endphp
                    <a 
                        href="{{ route('cart.index') }}" 
                        class="relative inline-flex items-center justify-center p-2.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-violet-500/50 hover:bg-slate-800 text-slate-200 hover:text-white transition-all group"
                        title="Ver Carrito de Compras"
                    >
                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>

                        <!-- Badge Contador -->
                        <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1.5 rounded-full bg-violet-600 text-white text-[11px] font-extrabold flex items-center justify-center shadow-md shadow-violet-600/40 border border-slate-950 transition-all {{ $cartCount > 0 ? 'scale-100' : 'scale-90 opacity-75' }}">
                            {{ $cartCount }}
                        </span>
                    </a>
                </div>

            </div>
        </div>

        <!-- Barra de búsqueda en móvil -->
        <div class="md:hidden px-4 pb-3">
            <form action="{{ route('mangas.index') }}" method="GET" class="w-full relative">
                <input 
                    type="search" 
                    name="q" 
                    value="{{ request('q') }}"
                    placeholder="Buscar mangas..." 
                    class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-900 border border-slate-800 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>
    </header>

    <!-- Contenedor de Alertas Flash de Sesión -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm shadow-lg backdrop-blur-md animate-fade-in" role="alert">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm shadow-lg backdrop-blur-md animate-fade-in" role="alert">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-sm shadow-lg backdrop-blur-md animate-fade-in" role="alert">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif
    </div>

    <!-- Contenido Principal -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-slate-900 bg-slate-950/60 backdrop-blur-md text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Columna 1: Marca -->
                <div class="md:col-span-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-600 flex items-center justify-center text-white font-bold text-sm">
                            漫
                        </div>
                        <span class="text-lg font-black tracking-tight text-white font-display">
                            MANGA<span class="text-violet-400">・VAULT</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Tu tienda de confianza para coleccionar los mejores mangas en ediciones oficiales importadas y en español.
                    </p>
                </div>

                <!-- Columna 2: Demografías -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200 mb-3">Demografías</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('mangas.index', ['category' => 'shonen']) }}" class="hover:text-violet-400 transition">Shonen</a></li>
                        <li><a href="{{ route('mangas.index', ['category' => 'seinen']) }}" class="hover:text-violet-400 transition">Seinen</a></li>
                        <li><a href="{{ route('mangas.index', ['category' => 'shojo']) }}" class="hover:text-violet-400 transition">Shojo</a></li>
                        <li><a href="{{ route('mangas.index', ['category' => 'dark-fantasy']) }}" class="hover:text-violet-400 transition">Dark Fantasy</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Editoriales -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200 mb-3">Editoriales</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('mangas.index', ['publisher' => 'panini-manga']) }}" class="hover:text-violet-400 transition">Panini Manga</a></li>
                        <li><a href="{{ route('mangas.index', ['publisher' => 'editorial-ivrea']) }}" class="hover:text-violet-400 transition">Editorial Ivrea</a></li>
                        <li><a href="{{ route('mangas.index', ['publisher' => 'norma-editorial']) }}" class="hover:text-violet-400 transition">Norma Editorial</a></li>
                        <li><a href="{{ route('mangas.index', ['publisher' => 'milky-way']) }}" class="hover:text-violet-400 transition">Milky Way</a></li>
                    </ul>
                </div>

                <!-- Columna 4: Seguridad y Pasarelas -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200 mb-3">Pagos Seguros</h4>
                    <p class="text-xs text-slate-400 mb-3">
                        Todas tus transacciones están protegidas con cifrado SSL de 256 bits.
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-[10px] font-bold rounded bg-slate-900 border border-slate-800 text-slate-300">VISA</span>
                        <span class="px-2 py-1 text-[10px] font-bold rounded bg-slate-900 border border-slate-800 text-slate-300">MasterCard</span>
                        <span class="px-2 py-1 text-[10px] font-bold rounded bg-slate-900 border border-slate-800 text-slate-300">Stripe</span>
                        <span class="px-2 py-1 text-[10px] font-bold rounded bg-slate-900 border border-slate-800 text-slate-300">PayPal</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
                <p>&copy; {{ date('Y') }} MANGA・VAULT. Arquitectura Laravel + Tailwind CSS Dark Mode.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-slate-200">Términos de Servicio</a>
                    <a href="#" class="hover:text-slate-200">Privacidad</a>
                    <a href="#" class="hover:text-slate-200">Envíos y Devoluciones</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
