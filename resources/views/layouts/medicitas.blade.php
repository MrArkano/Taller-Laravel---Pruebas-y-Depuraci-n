<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MediCitas') · MediCitas</title>
    <meta name="description" content="MediCitas: gestión de citas médicas de la clínica.">
    <style>
        :root {
            --bg: #0b1220; --panel: #111a2e; --panel-2: #16223b; --border: #223153;
            --text: #e6edf7; --muted: #8fa3c4; --accent: #22d3ee; --accent-2: #6366f1;
            --ok: #34d399; --warn: #fbbf24; --bad: #f87171;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', system-ui, -apple-system, Roboto, sans-serif;
               background: radial-gradient(1200px 600px at 10% -10%, #14305a 0%, var(--bg) 55%); color: var(--text); min-height: 100vh; }
        a { color: var(--accent); text-decoration: none; }
        .nav { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
               padding: 16px 28px; border-bottom: 1px solid var(--border); background: rgba(17,26,46,.8); backdrop-filter: blur(8px); }
        .brand { font-weight: 800; font-size: 1.25rem; letter-spacing: .3px; }
        .brand span { color: var(--accent); }
        .nav-user { display: flex; align-items: center; gap: 12px; color: var(--muted); font-size: .9rem; }
        .container { max-width: 1080px; margin: 32px auto; padding: 0 20px; }
        .card { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; padding: 24px;
                box-shadow: 0 20px 40px rgba(0,0,0,.25); }
        .head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
        h1 { margin: 0; font-size: 1.6rem; }
        .btn { display: inline-block; border: 0; cursor: pointer; padding: 10px 18px; border-radius: 10px; font-weight: 600; font-size: .95rem;
               background: linear-gradient(135deg, var(--accent), var(--accent-2)); color: #05101f; transition: transform .15s, box-shadow .15s; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(34,211,238,.25); }
        .btn-ghost { background: transparent; color: var(--text); border: 1px solid var(--border); }
        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: .95rem; }
        .alert-success { background: rgba(52,211,153,.12); border: 1px solid rgba(52,211,153,.4); color: var(--ok); }
        .alert-error { background: rgba(248,113,113,.12); border: 1px solid rgba(248,113,113,.4); color: var(--bad); }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        th, td { text-align: left; padding: 12px 10px; border-bottom: 1px solid var(--border); font-size: .92rem; vertical-align: top; }
        th { color: var(--muted); font-weight: 600; text-transform: uppercase; font-size: .72rem; letter-spacing: .08em; }
        tbody tr:hover { background: var(--panel-2); }
        .badge { display: inline-block; padding: 3px 11px; border-radius: 999px; font-size: .75rem; font-weight: 700; text-transform: capitalize; }
        .badge-pendiente { background: rgba(251,191,36,.15); color: var(--warn); border: 1px solid rgba(251,191,36,.4); }
        .badge-confirmada { background: rgba(52,211,153,.15); color: var(--ok); border: 1px solid rgba(52,211,153,.4); }
        .badge-cancelada { background: rgba(248,113,113,.15); color: var(--bad); border: 1px solid rgba(248,113,113,.4); }
        .empty { text-align: center; color: var(--muted); padding: 40px 10px; }
        label { display: block; margin: 14px 0 6px; color: var(--muted); font-size: .85rem; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 11px 12px; border-radius: 10px; border: 1px solid var(--border);
                                  background: var(--panel-2); color: var(--text); font: inherit; }
        input:focus, select:focus, textarea:focus { outline: 2px solid var(--accent); border-color: transparent; }
        .field-error { color: var(--bad); font-size: .82rem; margin-top: 5px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 18px; }
        .readonly { padding: 11px 12px; border-radius: 10px; background: var(--panel-2); border: 1px dashed var(--border); }
        @media (max-width: 720px) { .grid { grid-template-columns: 1fr; } .container { margin-top: 20px; } }
    </style>
</head>
<body>
    <header class="nav">
        <a class="brand" href="{{ route('citas.index') }}">Medi<span>Citas</span></a>
        @auth
            <div class="nav-user">
                <span>{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->rol) }}</span>
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
</body>
</html>
