<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Citas médicas') · Medícate o Muérete</title>
    <meta name="description" content="Medícate o Muérete: gestión de citas médicas sencilla, cálida y confiable.">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='19' fill='%236b7a4a'/%3E%3Cpath d='M17 10h6v7h7v6h-7v7h-6v-7h-7v-6h7z' fill='%23fbf6ea'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,700;1,9..144,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream: #f7f1e3; --paper: #fdf9ef; --beige: #ece2cb; --sand: #e0d2b4;
            --coffee: #3f2d22; --brown: #6f4e37; --muted: #8b7762;
            --olive: #6b7a4a; --olive-soft: #8a9a5f; --olive-pale: #e4e8d3;
            --terracota: #a4543f; --ochre: #b7863a;
            --serif: 'Fraunces', Georgia, 'Times New Roman', serif;
            --sans: 'Inter', 'Segoe UI', system-ui, -apple-system, Roboto, sans-serif;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: var(--sans); color: var(--coffee); min-height: 100vh;
               background: radial-gradient(900px 480px at 85% -10%, #efe4c9 0%, transparent 60%), var(--cream); }
        a { color: var(--olive); text-decoration: none; }

        /* Barra de navegación */
        .nav { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
               padding: 14px 28px; background: rgba(253,249,239,.88); backdrop-filter: blur(8px);
               border-bottom: 1px solid var(--sand); position: sticky; top: 0; z-index: 10; }
        .brand { display: inline-flex; align-items: center; gap: 12px; color: var(--coffee); }
        .brand-mark { width: 40px; height: 40px; flex: none; }
        .brand-text { display: flex; flex-direction: column; line-height: 1.05; }
        .brand-name { font-family: var(--serif); font-weight: 700; font-size: 1.28rem; letter-spacing: -.01em; }
        .brand-name em { font-style: italic; font-weight: 500; color: var(--olive); }
        .brand-tag { font-size: .66rem; letter-spacing: .18em; text-transform: uppercase; color: var(--muted); margin-top: 3px; }
        .nav-user { display: flex; align-items: center; gap: 12px; color: var(--muted); font-size: .88rem; }
        .nav-user strong { color: var(--brown); font-weight: 600; }

        .container { max-width: 1080px; margin: 36px auto; padding: 0 20px; }
        .card { background: var(--paper); border: 1px solid var(--sand); border-radius: 18px; padding: 28px;
                box-shadow: 0 18px 40px -22px rgba(63,45,34,.35); }
        .head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
        h1 { margin: 0; font-family: var(--serif); font-weight: 700; font-size: 1.75rem; letter-spacing: -.01em; color: var(--coffee); }

        .btn { display: inline-block; border: 0; cursor: pointer; padding: 10px 20px; border-radius: 999px; font: 600 .92rem var(--sans);
               background: var(--olive); color: #fdf9ef; transition: transform .15s, box-shadow .15s, background .15s; }
        .btn:hover { background: #5b6a3d; transform: translateY(-1px); box-shadow: 0 10px 20px -10px rgba(91,106,61,.7); }
        .btn-ghost { background: transparent; color: var(--brown); border: 1px solid var(--sand); }
        .btn-ghost:hover { background: var(--beige); box-shadow: none; }

        .alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 18px; font-size: .94rem; }
        .alert-success { background: var(--olive-pale); border: 1px solid #c5ceab; color: #4a5a2e; }
        .alert-error { background: #f6e1db; border: 1px solid #e4bcb1; color: var(--terracota); }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        th, td { text-align: left; padding: 13px 10px; border-bottom: 1px solid var(--beige); font-size: .92rem; vertical-align: top; }
        th { color: var(--muted); font-weight: 600; text-transform: uppercase; font-size: .7rem; letter-spacing: .1em; }
        tbody tr:hover { background: #f8f1df; }

        .badge { display: inline-block; padding: 3px 12px; border-radius: 999px; font-size: .74rem; font-weight: 600; text-transform: capitalize; }
        .badge-pendiente { background: #f5e7c6; color: #8d6420; border: 1px solid #e6cf9b; }
        .badge-confirmada { background: var(--olive-pale); color: #4a5a2e; border: 1px solid #c5ceab; }
        .badge-cancelada { background: #f6e1db; color: var(--terracota); border: 1px solid #e4bcb1; }
        .empty { text-align: center; color: var(--muted); padding: 44px 10px; font-family: var(--serif); font-style: italic; font-size: 1.05rem; }

        label { display: block; margin: 16px 0 6px; color: var(--brown); font-size: .84rem; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 11px 13px; border-radius: 12px; border: 1px solid var(--sand);
                                  background: #fffdf6; color: var(--coffee); font: inherit; }
        input:focus, select:focus, textarea:focus { outline: 2px solid var(--olive-soft); outline-offset: 1px; border-color: transparent; }
        .field-error { color: var(--terracota); font-size: .82rem; margin-top: 5px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 18px; }
        .readonly { padding: 11px 13px; border-radius: 12px; background: var(--beige); border: 1px dashed var(--sand); color: var(--brown); }

        .login-intro { color: var(--muted); margin: 6px 0 0; font-size: .95rem; }
        .footer { text-align: center; color: var(--muted); font-size: .78rem; padding: 8px 0 36px; }

        @media (max-width: 720px) {
            .grid { grid-template-columns: 1fr; }
            .container { margin-top: 20px; }
            .nav { padding: 12px 16px; }
            .card { padding: 20px; }
            .brand-name { font-size: 1.1rem; }
        }
    </style>
</head>
<body>
    <header class="nav">
        <a class="brand" href="{{ route('citas.index') }}" aria-label="Medícate o Muérete — inicio" id="logo">
            <svg class="brand-mark" viewBox="0 0 40 40" role="img" aria-hidden="true">
                <circle cx="20" cy="20" r="19" fill="#6b7a4a"/>
                <path d="M17 10h6v7h7v6h-7v7h-6v-7h-7v-6h7z" fill="#fbf6ea"/>
                <path d="M31.5 6.5c-6 .2-10 3.4-10.6 8.6 5.8-.3 10.2-3.1 10.6-8.6z" fill="#d9c9a0"/>
            </svg>
            <span class="brand-text">
                <span class="brand-name">Medícate <em>o</em> Muérete</span>
                <span class="brand-tag">Citas médicas</span>
            </span>
        </a>
        @auth
            <div class="nav-user">
                <span><strong>{{ auth()->user()->name }}</strong> · {{ ucfirst(auth()->user()->rol) }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost" id="btn-logout">Salir</button>
                </form>
            </div>
        @endauth
    </header>

    <main class="container">
        @if (session('success'))
            <div class="alert alert-success" id="msg-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error" id="msg-error" role="alert">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">© {{ date('Y') }} Medícate o Muérete</footer>
</body>
</html>
