<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ChatPedido · Catálogo online y pedidos directos a tu WhatsApp</title>
    <meta name="description" content="Explora tiendas, arma tu orden en segundos con extras y notas, y envíala calculada al WhatsApp de la tienda.">
    <meta property="og:type" content="website">
    <meta property="og:title" content="ChatPedido · Catálogo online y pedidos directos a tu WhatsApp">
    <meta property="og:description" content="Explora tiendas, arma tu orden en segundos y envíala calculada al WhatsApp de la tienda.">
    <meta property="og:image" content="{{ asset('og-image-1200x630.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/storefront.css', 'resources/js/storefront.js'])
</head>
<body>
    <a class="skip" href="#tiendas">Saltar a las tiendas</a>
    <header class="header">
        <nav class="wrap nav" aria-label="Navegación principal">
            <a href="{{ route('home') }}" class="brand" aria-label="ChatPedido, inicio">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="120" height="120" rx="30" fill="#6135BC"/>
                        <path d="M26 34C26 25.16 33.16 18 42 18H78C86.84 18 94 25.16 94 34V60C94 68.84 86.84 76 78 76H46L30 88C28.2 89.3 26 88 26 85.8V74C26 74 26 34 26 34Z" fill="#7C4DE8"/>
                        <g transform="rotate(-6 42 34)">
                            <rect x="42" y="34" width="48" height="64" rx="10" fill="white"/>
                            <rect x="52" y="44" width="28" height="4" rx="2" fill="#665F74"/>
                            <rect x="50" y="54" width="22" height="4" rx="2" fill="#A098B2"/>
                            <path d="M56 70L66 70L60 84L76 68L66 68L70 56L56 70Z" fill="#10B981"/>
                            <circle cx="78" cy="85" r="5" fill="#FF6B00"/>
                        </g>
                    </svg>
                </span>
                <span>Chat<span class="brand-accent">Pedido</span><span class="brand-dot">.</span></span>
            </a>
            <div class="nav-links">
                <a href="#tiendas">Explorar tiendas</a>
                <a href="#como-funciona">Cómo funciona</a>
                <a class="login" href="{{ route('filament.admin.auth.login') }}">Iniciar sesión <span aria-hidden="true">↗</span></a>
            </div>
        </nav>
    </header>
    <main>
        <section class="hero wrap" aria-labelledby="hero-title">
            <div class="hero-panel">
                <div class="hero-copy">
                    <div class="eyebrow">Tus tiendas, a un clic</div>
                    <h1 id="hero-title">Tu próximo antojo<br>empieza <span>aquí.</span></h1>
                    <p>Descubre una tienda, arma tu pedido y envíalo por WhatsApp. Así de fácil.</p>
                    <a class="primary" href="#tiendas">Encontrar mi tienda <span aria-hidden="true">↓</span></a>
                </div>
                <div class="hero-art">
                    <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&amp;fit=crop&amp;w=1100&amp;q=85"
                         alt="" width="1100" height="760" fetchpriority="high" data-fallback="{{ asset('store-placeholder.svg') }}">
                    <span class="photo-label">Se ve bien. Sabe mejor.</span>
                    <div class="order-note" aria-hidden="true">
                        <small>MENOS ESCRIBIR, MÁS DISFRUTAR</small>
                        <strong>Tu pedido, a tu gusto.</strong>
                        <div class="note-line"><span class="check">✓</span> Listo para compartir por WhatsApp</div>
                    </div>
                </div>
            </div>
        </section>
        <section id="tiendas" class="directory wrap" aria-labelledby="stores-title">
            <div class="section-heading">
                <div><h2 id="stores-title">¿Qué se te antoja hoy?</h2><p class="sub">Encuentra tus favoritos o descubre algo nuevo.</p></div>
                <form action="{{ route('home') }}#tiendas" method="get" role="search" class="search">
                    @if ($category !== '')<input type="hidden" name="category" value="{{ $category }}">@endif
                    <label class="sr-only" for="store-search">Buscar por tienda o tipo de comida</label>
                    <input id="store-search" name="q" type="search" maxlength="100" value="{{ $search }}" placeholder="Busca una tienda o un antojo…">
                    <button type="submit" aria-label="Buscar tiendas">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.5" cy="10.5" r="7"></circle><path d="m16 16 5 5"></path></svg>
                    </button>
                </form>
            </div>
            @error('q')<p class="search-error" role="alert">{{ $message }}</p>@enderror
            @if ($categories->isNotEmpty())
            <div class="filters-bar" role="group" aria-label="Filtrar tiendas por categoría">
                <details class="category-dropdown" id="category-dropdown">
                    <summary class="category-btn">
                        <svg class="cat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        <span class="category-btn-label">{{ $category !== '' ? 'Categoría: ' . $category : 'Todas las categorías' }}</span>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </summary>
                    <div class="category-menu" aria-label="Categorías disponibles">
                        <a class="category-item {{ $category === '' ? 'active' : '' }}" href="{{ route('home', array_filter(['q' => $search])) }}#tiendas" @if ($category === '') aria-current="true" @endif>
                            <span>Todas las categorías</span>
                            @if ($category === '')<span class="check-icon" aria-hidden="true">✓</span>@endif
                        </a>
                        @foreach ($categories as $option)
                            <a class="category-item {{ $category === $option ? 'active' : '' }}" href="{{ route('home', array_filter(['q' => $search, 'category' => $option])) }}#tiendas" @if ($category === $option) aria-current="true" @endif>
                                <span>{{ $option }}</span>
                                @if ($category === $option)<span class="check-icon" aria-hidden="true">✓</span>@endif
                            </a>
                        @endforeach
                    </div>
                </details>
                @if ($category !== '')
                    <a class="category-clear-chip" href="{{ route('home', array_filter(['q' => $search])) }}#tiendas" aria-label="Quitar filtro de categoría {{ $category }}">
                        <span>{{ $category }}</span>
                        <span class="chip-close" aria-hidden="true">&times;</span>
                    </a>
                @endif
            </div>
            @endif
            <p class="results" role="status" aria-live="polite">
                @if ($stores->count())
                    <span class="results-count">Mostrando {{ $stores->firstItem() }}–{{ $stores->lastItem() }} de {{ $stores->total() }} tiendas</span>
                @else
                    <span>No hay tiendas para mostrar en esta página.</span>
                @endif
                @if ($search !== '' || $category !== '')
                    <a href="{{ route('home') }}#tiendas">Limpiar filtros</a>
                @endif
            </p>
            <div class="grid" data-total="{{ $stores->total() }}">
                @foreach ($stores as $store)
                    @php($branch = $store->branches->sole())
                    <a class="card" href="{{ route('public.catalog.show', ['companySlug' => $store->slug, 'branchUuid' => $branch->uuid]) }}" aria-label="Ver catálogo de {{ $store->name }}, {{ $branch->name }}">
                        <div class="card-photo">
                            <img src="{{ $store->coverImageUrl() ?? asset('store-placeholder.svg') }}" alt="" loading="lazy" decoding="async" width="760" height="400" referrerpolicy="no-referrer" data-fallback="{{ asset('store-placeholder.svg') }}">
                            @if ($store->store_category)<span class="category-badge">{{ $store->store_category }}</span>@endif
                        </div>
                        <div class="card-body">
                            <div class="store-title">
                                <span class="store-logo" aria-hidden="true">{{ mb_strtoupper(mb_substr($store->name, 0, 2)) }}</span>
                                <div><h3>{{ $store->name }}</h3><div class="location">{{ $branch->name }}@if ($branch->cityRecord) · {{ $branch->cityRecord->name }}@endif</div></div>
                            </div>
                            <p class="description">{{ $store->store_description ?: 'Explora nuestro catálogo y arma tu pedido.' }}</p>
                            <div class="card-bottom"><span>Ver catálogo</span><span class="arrow" aria-hidden="true">↗</span></div>
                        </div>
                    </a>
                @endforeach
            </div>
            @if ($stores->isEmpty())
                <div class="empty">
                    <h3>No encontramos tiendas</h3>
                    <p class="sub">Prueba con otro nombre o vuelve a explorar todas las tiendas.</p>
                    <a class="primary" href="{{ route('home') }}#tiendas">Ver todas las tiendas</a>
                </div>
            @endif
            @if ($stores->hasPages())
                <nav class="pagination" aria-label="Páginas de tiendas">
                    @if ($stores->onFirstPage())
                        <span class="page disabled" aria-disabled="true">← Anterior</span>
                    @else
                        <a class="page" href="{{ $stores->previousPageUrl() }}" rel="prev">← Anterior</a>
                    @endif
                    <span class="page-info">Página {{ $stores->currentPage() }} de {{ $stores->lastPage() }}</span>
                    @if ($stores->hasMorePages())
                        <a class="page" href="{{ $stores->nextPageUrl() }}" rel="next">Siguiente →</a>
                    @else
                        <span class="page disabled" aria-disabled="true">Siguiente →</span>
                    @endif
                </nav>
            @endif
        </section>
        <section class="how" id="como-funciona" aria-labelledby="how-title">
            <div class="wrap how-inner">
                <div><h2 id="how-title">De «¿qué pido?»<br>a «ya lo tengo listo».</h2><p class="sub">Sin crear una cuenta para elegir.</p></div>
                <ol class="steps">
                    <li><span class="step-number">1</span><strong>Encuentra tu tienda</strong><p>Explora su catálogo y sus productos.</p></li>
                    <li><span class="step-number">2</span><strong>Arma tu pedido</strong><p>Elige cantidades, opciones y extras.</p></li>
                    <li><span class="step-number">3</span><strong>Sigue por WhatsApp</strong><p>Envía el pedido y confirma con la tienda.</p></li>
                </ol>
            </div>
        </section>
    </main>
    <footer class="wrap footer">
        <div><strong>Chat<span class="brand-accent">Pedido</span><span class="brand-dot">.</span></strong> · Pedidos directos a tu WhatsApp sin fricción.</div>
        <div>¿Administras un negocio? <a href="{{ route('filament.admin.auth.login') }}">Entra a tu panel ↗</a></div>
    </footer>
</body>
</html>
