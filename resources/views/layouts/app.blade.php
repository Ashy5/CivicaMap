<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CivicaMap')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @stack('styles')
    <style>
        body { background:#f5f7fb; color:#212529; }
        .navbar-brand { font-weight:800; letter-spacing:.2px; }
        .brand-icon { display:inline-flex; width:36px; height:36px; align-items:center; justify-content:center; border-radius:10px; background:#0d6efd; margin-right:8px; }
        .hero { background:linear-gradient(135deg,#0d6efd 0%,#084298 100%); color:white; }
        .hero-card { border:0; border-radius:24px; background:rgba(255,255,255,.12); backdrop-filter:blur(8px); }
        .card { border:0; border-radius:18px; }
        .feature-icon { width:58px; height:58px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.6rem; }
        .section-title { font-weight:800; }
        .btn { border-radius:10px; }
        .footer { background:#18212f; color:#ced4da; }
        .map-placeholder { min-height:520px; border-radius:18px; background:linear-gradient(135deg,#dbeafe,#e9f5ee); position:relative; overflow:hidden; }
        .map-placeholder:before,.map-placeholder:after { content:""; position:absolute; background:rgba(255,255,255,.75); transform:rotate(-18deg); }
        .map-placeholder:before { width:130%; height:70px; top:42%; left:-10%; }
        .map-placeholder:after { width:120%; height:45px; top:62%; left:-5%; transform:rotate(22deg); }
        .map-pin { position:absolute; z-index:2; font-size:2.2rem; }
        .auth-card { max-width:520px; margin:auto; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <span class="brand-icon"><i class="bi bi-map-fill"></i></span>CivicaMap
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i class="bi bi-house me-1"></i>Inicio</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('mapa') ? 'active' : '' }}" href="{{ route('mapa') }}"><i class="bi bi-geo-alt me-1"></i>Mapa</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('denuncias') ? 'active' : '' }}" href="{{ route('denuncias') }}"><i class="bi bi-megaphone me-1"></i>Denuncias</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('servicios') ? 'active' : '' }}" href="{{ route('servicios') }}"><i class="bi bi-grid me-1"></i>Servicios</a></li>
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0"><a class="btn btn-outline-light" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

@yield('content')

<footer class="footer mt-5 py-4">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-7">
                <h5 class="text-white mb-1"><i class="bi bi-map-fill me-2"></i>CivicaMap</h5>
                <p class="mb-0 small">Plataforma ciudadana orientada a la participación, seguridad y convivencia comunitaria.</p>
            </div>
            <div class="col-md-5 text-md-end small">
                <span>Proyecto académico · ODS 16</span>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
