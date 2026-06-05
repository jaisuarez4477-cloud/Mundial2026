<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Polla Copa Mundial 2026 — JGCV</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --fifa-dark:       #0A0E1A;
            --fifa-navy:       #0D1B3E;
            --fifa-blue:       #0066CC;
            --fifa-gold:       #C8922A;
            --fifa-gold-light: #E8B84B;
            --fifa-red:        #CC0000;
            --fifa-white:      #F5F7FA;
            --card-bg:         rgba(255,255,255,0.06);
            --card-border:     rgba(200,146,42,0.3);
            --input-bg:        rgba(255,255,255,0.08);
            --input-border:    rgba(255,255,255,0.15);
            --input-focus:     rgba(200,146,42,0.7);
            --text-main:       #F5F7FA;
            --text-muted:      rgba(245,247,250,0.55);
        }

        body {
            background: var(--fifa-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
            font-family: 'Barlow', sans-serif;
        }

        /* Fondo decorativo */
        .bg-stripes {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background: repeating-linear-gradient(
                105deg,
                transparent 0px, transparent 38px,
                rgba(200,146,42,0.04) 38px, rgba(200,146,42,0.04) 40px
            );
        }
        .bg-circle { position: fixed; border-radius: 50%; pointer-events: none; z-index: 0; }
        .bc1 { width:420px; height:420px; top:-120px; right:-100px;
                background: radial-gradient(circle, rgba(0,102,204,0.18) 0%, transparent 70%); }
        .bc2 { width:300px; height:300px; bottom:-80px; left:-80px;
                background: radial-gradient(circle, rgba(200,146,42,0.12) 0%, transparent 70%); }
        .bc3 { width:200px; height:200px; top:40%; left:5%;
                background: radial-gradient(circle, rgba(204,0,0,0.08) 0%, transparent 70%); }

        /* Tarjeta principal */
        .card {
            position: relative; z-index: 2;
            width: 100%; max-width: 420px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 36px 32px 32px;
        }

        .badge-mundial {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 20px;
        }
        .badge-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--fifa-gold); }
        .badge-text {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 11px; font-weight: 600; letter-spacing: 2.5px;
            color: var(--fifa-gold); text-transform: uppercase;
        }

        .logo-wrap { text-align: center; margin-bottom: 8px; }
        .logo-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 56px; height: 56px; border-radius: 50%;
            border: 2px solid var(--fifa-gold);
            background: rgba(200,146,42,0.1);
            margin-bottom: 12px;
        }

        .title-main {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 32px; letter-spacing: 2px;
            color: var(--text-main); text-align: center; line-height: 1;
            margin-bottom: 4px;
        }
        .title-sub {
            font-size: 13px; color: var(--text-muted);
            text-align: center; margin-bottom: 28px; letter-spacing: 0.3px;
        }

        /* Tabs */
        .tab-row {
            display: flex; background: rgba(255,255,255,0.05);
            border-radius: 8px; padding: 3px; gap: 3px; margin-bottom: 24px;
        }
        .tab-btn {
            flex: 1; padding: 8px; border: none; background: transparent; cursor: pointer;
            font-family: 'Barlow', sans-serif; font-size: 13px; font-weight: 500;
            color: var(--text-muted); border-radius: 6px; transition: all .2s;
        }
        .tab-btn.active { background: var(--fifa-gold); color: #0A0E1A; font-weight: 600; }

        /* Formulario */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 12px; font-weight: 500; color: var(--text-muted);
            letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 7px;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            color: rgba(245,247,250,0.35); font-size: 16px; pointer-events: none;
        }
        .form-input {
            width: 100%; padding: 11px 13px 11px 38px;
            background: var(--input-bg); border: 1px solid var(--input-border);
            border-radius: 8px; color: var(--text-main);
            font-family: 'Barlow', sans-serif; font-size: 14px;
            outline: none; transition: border-color .2s, background .2s;
        }
        .form-input::placeholder { color: rgba(245,247,250,0.28); }
        .form-input:focus {
            border-color: var(--input-focus);
            background: rgba(200,146,42,0.06);
        }
        .eye-btn {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; padding: 2px;
            color: rgba(245,247,250,0.35); font-size: 16px;
        }
        .eye-btn:hover { color: var(--fifa-gold); }

        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .forgot {
            text-align: right; margin-top: -8px; margin-bottom: 16px;
            font-size: 12px; color: var(--fifa-gold-light); cursor: pointer;
        }
        .forgot:hover { text-decoration: underline; }

        .terms {
            display: flex; align-items: flex-start; gap: 8px; margin-bottom: 16px;
            font-size: 12px; color: var(--text-muted); line-height: 1.4;
        }
        .terms input[type=checkbox] {
            margin-top: 2px; accent-color: var(--fifa-gold);
            flex-shrink: 0; width: 14px; height: 14px; cursor: pointer;
        }
        .terms a { color: var(--fifa-gold-light); }

        /* Botones */
        .btn-primary {
            width: 100%; padding: 13px; border: none; border-radius: 8px;
            background: var(--fifa-gold); color: #0A0E1A;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 16px; font-weight: 700; letter-spacing: 1.5px;
            text-transform: uppercase; cursor: pointer;
            transition: all .2s; margin-bottom: 16px;
        }
        .btn-primary:hover { background: var(--fifa-gold-light); transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0) scale(0.99); }
        .btn-primary:disabled { opacity: 0.6; pointer-events: none; }

        .divider {
            display: flex; align-items: center; gap: 10px; margin-bottom: 16px;
            font-size: 11px; color: var(--text-muted); letter-spacing: 1px;
            text-transform: uppercase;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.1);
        }

        .btn-google {
            width: 100%; padding: 11px; border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px; background: rgba(255,255,255,0.05);
            color: var(--text-main); font-family: 'Barlow', sans-serif;
            font-size: 13px; font-weight: 500; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all .2s; text-decoration: none;
        }
        .btn-google:hover { background: rgba(255,255,255,0.09); border-color: rgba(255,255,255,0.2); }

        /* Alertas */
        .alert {
            border-radius: 8px; padding: 10px 14px; margin-bottom: 16px;
            font-size: 13px; display: none; align-items: center; gap: 8px;
        }
        .alert-error  { background: rgba(204,0,0,0.15); border: 1px solid rgba(204,0,0,0.4); color: #ff9999; }
        .alert-success{ background: rgba(29,158,117,0.15); border: 1px solid rgba(29,158,117,0.4); color: #7FD9BC; }

        /* Indicador de fuerza de contraseña */
        .strength-bar  { height: 3px; border-radius: 2px; margin-top: 6px; background: rgba(255,255,255,0.1); overflow: hidden; }
        .strength-fill { height: 100%; width: 0; transition: width .3s, background .3s; border-radius: 2px; }
        .strength-lbl  { font-size: 11px; color: var(--text-muted); margin-top: 3px; }

        /* Footer stats */
        .footer-stats { display: flex; justify-content: center; gap: 28px; margin-top: 24px; position: relative; z-index: 2; }
        .stat-item { text-align: center; }
        .stat-num { font-family: 'Bebas Neue', sans-serif; font-size: 22px; color: var(--fifa-gold); letter-spacing: 1px; display: block; }
        .stat-lbl { font-size: 10px; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase; }

        @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .form-section { animation: fadeIn .25s ease forwards; }

        @media (max-width: 480px) {
            .card { padding: 28px 20px 24px; }
            .row-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="bg-stripes"></div>
    <div class="bg-circle bc1"></div>
    <div class="bg-circle bc2"></div>
    <div class="bg-circle bc3"></div>

    <div class="card">
        <div class="badge-mundial">
            <div class="badge-dot"></div>
            <span class="badge-text">FIFA World Cup 2026</span>
            <div class="badge-dot"></div>
        </div>

        <div class="logo-wrap">
            <div class="logo-icon">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <circle cx="14" cy="14" r="12" stroke="#C8922A" stroke-width="1.5"/>
                    <path d="M14 4 L16.5 10.5 L23.5 10.5 L18 14.5 L20 21 L14 17 L8 21 L10 14.5 L4.5 10.5 L11.5 10.5 Z" fill="#C8922A"/>
                </svg>
            </div>
            <h1 class="title-main">Polla Mundial</h1>
            <p class="title-sub">JGCV · Copa Mundial FIFA 2026</p>
        </div>

        <div class="tab-row">
            <button class="tab-btn active" id="tab-login" onclick="switchTab('login')">Iniciar sesión</button>
            <button class="tab-btn" id="tab-register" onclick="switchTab('register')">Registrarse</button>
        </div>

        {{-- Alertas --}}
        @if($errors->any())
        <div class="alert alert-error" style="display:flex">
            <i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif
        @if(session('success'))
        <div class="alert alert-success" style="display:flex">
            <i class="ti ti-circle-check" style="font-size:16px;flex-shrink:0"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <div id="alert-js" class="alert alert-error"></div>

        {{-- FORMULARIO LOGIN --}}
        <div id="form-login" class="form-section">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="ti ti-mail input-icon"></i>
                        <input type="email" name="email" class="form-input"
                               placeholder="tu@correo.com" value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock input-icon"></i>
                        <input type="password" name="password" class="form-input"
                               placeholder="••••••••" id="login-pass" style="padding-right:40px" required>
                        <button type="button" class="eye-btn" onclick="togglePass('login-pass',this)" aria-label="Mostrar contraseña">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="forgot">
                    <a href="{{ route('password.request') }}" style="color:var(--fifa-gold-light);text-decoration:none">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="ti ti-login" style="font-size:16px;vertical-align:-2px;margin-right:6px"></i>
                    Entrar al concurso
                </button>
            </form>
            <div class="divider">o continúa con</div>
            <a href="#">Google</a>
                <svg width="16" height="16" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.2l6.7-6.7C35.8 2.5 30.2 0 24 0 14.6 0 6.6 5.4 2.6 13.3l7.8 6C12.3 13.1 17.7 9.5 24 9.5z"/><path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8c4.4-4 7.1-10 7.1-17z"/><path fill="#FBBC05" d="M10.4 28.7A14.5 14.5 0 0 1 9.5 24c0-1.6.3-3.2.8-4.7l-7.8-6A24 24 0 0 0 0 24c0 3.9.9 7.5 2.6 10.7l7.8-6z"/><path fill="#34A853" d="M24 48c6.2 0 11.4-2 15.2-5.5l-7.5-5.8c-2 1.4-4.6 2.3-7.7 2.3-6.3 0-11.7-3.6-13.6-9l-7.8 6C6.6 42.6 14.6 48 24 48z"/></svg>
                Continuar con Google
            </a>
        </div>

        {{-- FORMULARIO REGISTRO --}}
        <div id="form-register" style="display:none" class="form-section">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="row-2">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <div class="input-wrap">
                            <i class="ti ti-user input-icon"></i>
                            <input type="text" name="nombre" class="form-input"
                                   placeholder="Juan" value="{{ old('nombre') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <div class="input-wrap">
                            <i class="ti ti-user input-icon"></i>
                            <input type="text" name="apellido" class="form-input"
                                   placeholder="García" value="{{ old('apellido') }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="ti ti-mail input-icon"></i>
                        <input type="email" name="email" class="form-input"
                               placeholder="tu@correo.com" value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">WhatsApp</label>
                    <div class="input-wrap">
                        <i class="ti ti-brand-whatsapp input-icon"></i>
                        <input type="tel" name="whatsapp" class="form-input"
                               placeholder="+57 300 123 4567" value="{{ old('whatsapp') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock input-icon"></i>
                        <input type="password" name="password" class="form-input"
                               placeholder="Mín. 8 caracteres" id="reg-pass"
                               oninput="checkStrength(this.value)"
                               style="padding-right:40px" required>
                        <button type="button" class="eye-btn" onclick="togglePass('reg-pass',this)" aria-label="Mostrar contraseña">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                    <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                    <div class="strength-lbl" id="strength-lbl"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar contraseña</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock input-icon"></i>
                        <input type="password" name="password_confirmation" class="form-input"
                               placeholder="Repite tu contraseña" id="reg-pass2"
                               style="padding-right:40px" required>
                        <button type="button" class="eye-btn" onclick="togglePass('reg-pass2',this)" aria-label="Mostrar contraseña">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="terms">
                    <input type="checkbox" id="chk-terms" name="terms" required>
                    <label for="chk-terms">Acepto los <a href="#">términos y condiciones</a> del concurso JGCV y el sistema de puntuación de la Polla Copa Mundial 2026.</label>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="ti ti-trophy" style="font-size:16px;vertical-align:-2px;margin-right:6px"></i>
                    Crear mi cuenta
                </button>
            </form>
        </div>
    </div>

    <div class="footer-stats">
        <div class="stat-item"><span class="stat-num">48</span><span class="stat-lbl">Equipos</span></div>
        <div class="stat-item"><span class="stat-num">104</span><span class="stat-lbl">Partidos</span></div>
        <div class="stat-item"><span class="stat-num">39</span><span class="stat-lbl">Días</span></div>
        <div class="stat-item"><span class="stat-num">—</span><span class="stat-lbl">Jugadores</span></div>
    </div>

    <script>
    // Detectar si hay errores de registro para mostrar tab correcto
    document.addEventListener('DOMContentLoaded', () => {
        @if($errors->any() && old('nombre'))
            switchTab('register');
        @endif
    });

    function switchTab(tab) {
        const isLogin = tab === 'login';
        document.getElementById('tab-login').classList.toggle('active', isLogin);
        document.getElementById('tab-register').classList.toggle('active', !isLogin);
        document.getElementById('form-login').style.display    = isLogin ? 'block' : 'none';
        document.getElementById('form-register').style.display = isLogin ? 'none'  : 'block';
        const el = isLogin
            ? document.getElementById('form-login')
            : document.getElementById('form-register');
        el.classList.remove('form-section');
        void el.offsetWidth;
        el.classList.add('form-section');
    }

    function togglePass(id, btn) {
        const inp = document.getElementById(id);
        const showing = inp.type === 'text';
        inp.type = showing ? 'password' : 'text';
        btn.querySelector('i').className = showing ? 'ti ti-eye' : 'ti ti-eye-off';
    }

    function checkStrength(v) {
        let score = 0;
        if (v.length >= 8)            score++;
        if (/[A-Z]/.test(v))          score++;
        if (/[0-9]/.test(v))          score++;
        if (/[^A-Za-z0-9]/.test(v))   score++;
        const levels = [
            { w: '0%',   bg: 'transparent',  txt: '' },
            { w: '25%',  bg: '#CC0000',       txt: 'Débil' },
            { w: '50%',  bg: '#E8B84B',       txt: 'Regular' },
            { w: '75%',  bg: '#639922',       txt: 'Buena' },
            { w: '100%', bg: '#1D9E75',       txt: 'Fuerte' },
        ];
        const l = levels[score] || levels[0];
        const fill = document.getElementById('strength-fill');
        const lbl  = document.getElementById('strength-lbl');
        fill.style.width      = l.w;
        fill.style.background = l.bg;
        lbl.textContent       = l.txt;
        lbl.style.color       = l.bg;
    }
    </script>
</body>
</html>
