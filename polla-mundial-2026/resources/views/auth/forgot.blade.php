<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña — Polla Mundial 2026</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{background:#0A0E1A;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;font-family:'Barlow',sans-serif;}
        .card{width:100%;max-width:400px;background:rgba(255,255,255,.06);border:1px solid rgba(200,146,42,.3);border-radius:16px;padding:36px 32px}
        .title{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:#F5F7FA;text-align:center;margin-bottom:6px}
        .sub{font-size:13px;color:rgba(245,247,250,.55);text-align:center;margin-bottom:28px;line-height:1.5}
        .label{display:block;font-size:12px;font-weight:500;color:rgba(245,247,250,.55);letter-spacing:.5px;text-transform:uppercase;margin-bottom:7px}
        .input-wrap{position:relative;margin-bottom:20px}
        .icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:rgba(245,247,250,.35);font-size:16px;pointer-events:none}
        input{width:100%;padding:11px 13px 11px 38px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:8px;color:#F5F7FA;font-family:'Barlow',sans-serif;font-size:14px;outline:none}
        input:focus{border-color:rgba(200,146,42,.7)}
        input::placeholder{color:rgba(245,247,250,.28)}
        .btn{width:100%;padding:13px;border:none;border-radius:8px;background:#C8922A;color:#0A0E1A;font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;transition:all .2s;margin-bottom:14px}
        .btn:hover{background:#E8B84B}
        .back{display:block;text-align:center;font-size:13px;color:rgba(200,146,42,.8);text-decoration:none}
        .back:hover{color:#E8B84B}
        .alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
        .alert-success{background:rgba(29,158,117,.15);border:1px solid rgba(29,158,117,.4);color:#7FD9BC}
    </style>
</head>
<body>
    <div class="card">
        <div style="text-align:center;margin-bottom:20px">
            <div style="width:52px;height:52px;border-radius:50%;border:2px solid #C8922A;background:rgba(200,146,42,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                <i class="ti ti-lock-open" style="font-size:24px;color:#C8922A"></i>
            </div>
        </div>
        <h1 class="title">Recuperar contraseña</h1>
        <p class="sub">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>

        @if(session('success'))
        <div class="alert alert-success">
            <i class="ti ti-circle-check" style="font-size:16px;flex-shrink:0"></i>
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('password.request') }}" method="POST">
            @csrf
            <label class="label">Correo electrónico</label>
            <div class="input-wrap">
                <i class="ti ti-mail icon"></i>
                <input type="email" name="email" placeholder="tu@correo.com" value="{{ old('email') }}" required>
            </div>
            <button type="submit" class="btn">
                <i class="ti ti-send" style="font-size:15px;vertical-align:-2px;margin-right:6px"></i>
                Enviar enlace
            </button>
        </form>
        <a href="{{ route('login') }}" class="back">
            <i class="ti ti-arrow-left" style="font-size:13px;vertical-align:-1px;margin-right:4px"></i>
            Volver al inicio de sesión
        </a>
    </div>
</body>
</html>
