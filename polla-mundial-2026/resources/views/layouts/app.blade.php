<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Polla Mundial 2026') — JGCV</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --dark:#0A0E1A; --navy:#0D1B3E; --gold:#C8922A; --gold-l:#E8B84B;
            --red:#CC0000;  --white:#F5F7FA; --muted:rgba(245,247,250,.55);
            --card:rgba(255,255,255,.06); --border:rgba(200,146,42,.25);
            --input:rgba(255,255,255,.08); --input-b:rgba(255,255,255,.15);
            --sidebar-w:220px;
        }
        body{background:var(--dark);color:var(--white);font-family:'Barlow',sans-serif;display:flex;min-height:100vh}

        .sidebar{
            width:var(--sidebar-w);min-height:100vh;background:rgba(13,27,62,.85);
            border-right:1px solid var(--border);display:flex;flex-direction:column;
            position:sticky;top:0;height:100vh;flex-shrink:0;
        }
        .sidebar-logo{padding:24px 20px 16px;border-bottom:1px solid var(--border);}
        .logo-badge{display:flex;align-items:center;gap:8px;margin-bottom:4px;}
        .logo-star{
            width:36px;height:36px;border-radius:50%;border:1.5px solid var(--gold);
            background:rgba(200,146,42,.12);display:flex;align-items:center;justify-content:center;
            font-size:16px;flex-shrink:0;
        }
        .logo-title{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:var(--white);line-height:1}
        .logo-sub{font-size:10px;color:var(--muted);letter-spacing:1.5px;text-transform:uppercase;padding-left:44px}
        .sidebar-user{
            padding:14px 20px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;gap:10px;
        }
        .avatar{
            width:34px;height:34px;border-radius:50%;background:rgba(200,146,42,.2);
            border:1.5px solid var(--gold);display:flex;align-items:center;justify-content:center;
            font-size:13px;font-weight:600;color:var(--gold);flex-shrink:0;
        }
        .user-name{font-size:13px;font-weight:500;color:var(--white);line-height:1.2}
        .user-role{font-size:11px;color:var(--muted)}
        .sidebar-nav{flex:1;padding:12px 0;}
        .nav-section{padding:8px 20px 4px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--muted)}
        .nav-link{
            display:flex;align-items:center;gap:10px;padding:10px 20px;
            color:var(--muted);text-decoration:none;font-size:13px;font-weight:500;
            transition:all .2s;border-left:3px solid transparent;
        }
        .nav-link:hover{color:var(--white);background:rgba(255,255,255,.05)}
        .nav-link.active{color:var(--gold);border-left-color:var(--gold);background:rgba(200,146,42,.08)}
        .nav-link i{font-size:17px}
        .sidebar-footer{padding:16px 20px;border-top:1px solid var(--border)}
        .btn-logout{
            width:100%;padding:9px;border:1px solid rgba(204,0,0,.4);border-radius:8px;
            background:rgba(204,0,0,.08);color:#ff9999;font-size:13px;font-family:'Barlow',sans-serif;
            cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;
            transition:all .2s;font-weight:500;
        }
        .btn-logout:hover{background:rgba(204,0,0,.18);border-color:rgba(204,0,0,.7)}

        .main{flex:1;display:flex;flex-direction:column;min-width:0}
        .topbar{
            padding:16px 28px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;
            background:rgba(10,14,26,.7);
        }
        .page-title{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:2px;color:var(--white)}
        .topbar-right{display:flex;align-items:center;gap:12px}
        .badge-fase{
            padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;
            background:rgba(200,146,42,.15);border:1px solid rgba(200,146,42,.35);
            color:var(--gold);letter-spacing:1px;text-transform:uppercase;
        }
        .content{padding:28px;flex:1}

        .card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px 24px}
        .card-sm{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:16px}
        .grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
        .grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px}
        .grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-bottom:24px}
        .stat-card{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:18px 20px;}
        .stat-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
        .stat-value{font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:1px;color:var(--gold);line-height:1}
        .stat-sub{font-size:12px;color:var(--muted);margin-top:4px}

        .table-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse}
        th{padding:10px 14px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid var(--border)}
        td{padding:11px 14px;font-size:13px;border-bottom:1px solid rgba(255,255,255,.05)}
        tr:hover td{background:rgba(255,255,255,.03)}
        .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
        .badge-gold{background:rgba(200,146,42,.15);color:var(--gold);border:1px solid rgba(200,146,42,.3)}
        .badge-green{background:rgba(29,158,117,.15);color:#5DCAA5;border:1px solid rgba(29,158,117,.3)}
        .badge-red{background:rgba(204,0,0,.15);color:#ff9999;border:1px solid rgba(204,0,0,.3)}
        .badge-blue{background:rgba(0,102,204,.15);color:#85B7EB;border:1px solid rgba(0,102,204,.3)}

        .btn{padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Barlow',sans-serif;border:none;transition:all .2s;display:inline-flex;align-items:center;gap:6px}
        .btn-gold{background:var(--gold);color:#0A0E1A}
        .btn-gold:hover{background:var(--gold-l)}
        .btn-outline{background:transparent;border:1px solid var(--border);color:var(--white)}
        .btn-outline:hover{background:rgba(255,255,255,.06)}
        .btn-sm{padding:6px 12px;font-size:12px}
        .btn-danger{background:rgba(204,0,0,.15);border:1px solid rgba(204,0,0,.4);color:#ff9999}
        .btn-danger:hover{background:rgba(204,0,0,.28)}

        .alert{padding:12px 16px;border-radius:8px;font-size:13px;display:flex;align-items:center;gap:8px;margin-bottom:20px}
        .alert-success{background:rgba(29,158,117,.15);border:1px solid rgba(29,158,117,.4);color:#7FD9BC}
        .alert-error{background:rgba(204,0,0,.15);border:1px solid rgba(204,0,0,.4);color:#ff9999}

        .equipo-cell{display:flex;align-items:center;gap:8px}
        .bandera{width:24px;height:16px;border-radius:2px;object-fit:cover;background:rgba(255,255,255,.1)}
        .bandera-placeholder{width:24px;height:16px;border-radius:2px;background:rgba(255,255,255,.12);display:inline-block}

        .form-input-dark{
            background:var(--input);border:1px solid var(--input-b);border-radius:8px;
            color:var(--white);font-family:'Barlow',sans-serif;font-size:14px;
            padding:9px 13px;outline:none;transition:border-color .2s;width:100%
        }
        .form-input-dark:focus{border-color:rgba(200,146,42,.7)}
        .form-input-dark::placeholder{color:rgba(245,247,250,.28)}
        .form-label{display:block;font-size:12px;color:var(--muted);margin-bottom:6px;font-weight:500;letter-spacing:.3px}
        textarea.form-input-dark{resize:vertical;min-height:80px;font-family:'Barlow',sans-serif}
        .score-input{
            width:48px;text-align:center;background:var(--input);border:1px solid var(--input-b);
            border-radius:8px;color:var(--white);font-size:18px;font-weight:600;
            padding:8px 4px;outline:none;font-family:'Bebas Neue',sans-serif;
        }
        .score-input:focus{border-color:rgba(200,146,42,.7)}

        @media(max-width:768px){
            .sidebar{display:none}
            .grid-4,.grid-3{grid-template-columns:repeat(2,1fr)}
            .grid-2{grid-template-columns:1fr}
            .content{padding:16px}
        }
        body::before{
            content:'';position:fixed;inset:0;pointer-events:none;z-index:0;
            background:repeating-linear-gradient(105deg,transparent 0,transparent 38px,rgba(200,146,42,.03) 38px,rgba(200,146,42,.03) 40px);
        }
        .main,.sidebar{position:relative;z-index:1}

        /* ── Fix select options ── */
.form-input-dark option {
    background: #0D1B3E;
    color: #F5F7FA;
}

select.form-input-dark {
    color: #F5F7FA;
}

select.form-input-dark option:hover,
select.form-input-dark option:checked {
    background: #C8922A;
    color: #0A0E1A;
}
    </style>
    @yield('styles')
</head>
<body>
    @php $rol = session('usuario_rol', 'participante'); @endphp

    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-badge">
                <div class="logo-star">
                    <svg width="18" height="18" viewBox="0 0 28 28" fill="none">
                        <circle cx="14" cy="14" r="12" stroke="#C8922A" stroke-width="1.5"/>
                        <path d="M14 4L16.5 10.5L23.5 10.5L18 14.5L20 21L14 17L8 21L10 14.5L4.5 10.5L11.5 10.5Z" fill="#C8922A"/>
                    </svg>
                </div>
                <span class="logo-title">Polla Mundial</span>
            </div>
            <div class="logo-sub">FIFA 2026 · JGCV</div>
        </div>

        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(session('usuario_nombre', 'U'), 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ session('usuario_nombre', 'Usuario') }}</div>
                <div class="user-role">{{ ucfirst(session('usuario_rol', 'participante')) }}</div>
            </div>
        </div>

        <nav class="sidebar-nav">

            {{-- ── Links exclusivos del administrador ── --}}
            @if($rol === 'admin')
                <div class="nav-section">Administración</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i> Dashboard
                </a>
                <a href="{{ route('admin.equipos') }}" class="nav-link {{ request()->routeIs('admin.equipos') ? 'active' : '' }}">
                    <i class="ti ti-shirt"></i> Equipos
                </a>
                <a href="{{ route('admin.grupos') }}" class="nav-link {{ request()->routeIs('admin.grupos') ? 'active' : '' }}">
                    <i class="ti ti-grid-dots"></i> Grupos
                </a>
                <a href="{{ route('admin.partidos') }}" class="nav-link {{ request()->routeIs('admin.partidos') ? 'active' : '' }}">
                    <i class="ti ti-ball-football"></i> Partidos
                </a>
                <a href="{{ route('admin.goleadores') }}" class="nav-link {{ request()->routeIs('admin.goleadores') ? 'active' : '' }}">
                    <i class="ti ti-shoe"></i> Goleadores
                </a>
                <a href="{{ route('admin.premios') }}" class="nav-link {{ request()->routeIs('admin.premios') ? 'active' : '' }}">
                    <i class="ti ti-cash-banknote"></i> Premios
                </a>
                <a href="{{ route('admin.notificaciones') }}" class="nav-link {{ request()->routeIs('admin.notificaciones') ? 'active' : '' }}">
                    <i class="ti ti-bell"></i> Notificaciones
                </a>
                <a href="{{ route('admin.resultados') }}" class="nav-link {{ request()->routeIs('admin.resultados') ? 'active' : '' }}">
                    <i class="ti ti-calculator"></i> Cierre y puntos
                </a>
                <a href="{{ route('admin.usuarios') }}" class="nav-link {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}">
                    <i class="ti ti-users"></i> Usuarios
                </a>
                <div class="nav-section" style="margin-top:8px">Vista pública</div>
            @endif
            {{-- ── Links visibles para todos (admin y participante) ── --}}

            <a href="{{ route('menu') }}" class="nav-link {{ request()->routeIs('menu') ? 'active' : '' }}">
                <i class="ti ti-home"></i> Inicio
            </a>
            <a href="{{ route('pronosticos') }}" class="nav-link {{ request()->routeIs('pronosticos') ? 'active' : '' }}">
                <i class="ti ti-writing"></i> Pronósticos
            </a>

            <div class="nav-section">Predicciones</div>
            <a href="{{ route('predicciones.grupos') }}" class="nav-link {{ request()->routeIs('predicciones.grupos') ? 'active' : '' }}">
                <i class="ti ti-list-numbers"></i> Posiciones de grupos
            </a>
            <a href="{{ route('predicciones.especiales') }}" class="nav-link {{ request()->routeIs('predicciones.especiales') ? 'active' : '' }}">
                <i class="ti ti-tournament"></i> Clasificados y campeón
            </a>
            <a href="{{ route('predicciones.desempate') }}" class="nav-link {{ request()->routeIs('predicciones.desempate') ? 'active' : '' }}">
                <i class="ti ti-award"></i> Premios individuales
            </a>

            <div class="nav-section">Clasificación</div>
            <a href="{{ route('ranking') }}" class="nav-link {{ request()->routeIs('ranking') ? 'active' : '' }}">
                <i class="ti ti-trophy"></i> Ranking
            </a>
            <a href="{{ route('resultados') }}" class="nav-link {{ request()->routeIs('resultados') ? 'active' : '' }}">
                <i class="ti ti-calendar-stats"></i> Resultados
            </a>

        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="ti ti-logout" style="font-size:16px"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <h1 class="page-title">@yield('page-title', 'Inicio')</h1>
            <div class="topbar-right">
                <span class="badge-fase">
                    <i class="ti ti-calendar-event" style="font-size:12px;vertical-align:-1px;margin-right:4px"></i>
                    Mundial 2026 · 11 Jun – 19 Jul
                </span>
            </div>
        </div>
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ti ti-circle-check" style="font-size:16px;flex-shrink:0"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0"></i>
                    {{ $errors->first() }}
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    @yield('scripts')
</body>
</html>