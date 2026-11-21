<!DOCTYPE html>

<html class="h-full bg-slate-100" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" name="viewport"/>
<title>Shawarma House - Menú Digital</title>
<!-- Google Fonts: Plus Jakarta Sans -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind CSS v3 with plugins -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              DEFAULT: '#005b34',
              hover: '#004728',
              surface: '#ecfdf5',
              dark: '#00331d'
            },
            accent: {
              orange: '#fc7127',
              surface: '#fff7ed'
            }
          },
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
          },
          boxShadow: {
            'subtle': '0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03)',
            'float-bar': '0 12px 32px -4px rgba(0, 91, 52, 0.22), 0 4px 12px -2px rgba(0, 0, 0, 0.08)'
          }
        }
      }
    }
  </script>
<style data-purpose="custom-scroll">
    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }
    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    .tap-highlight-transparent {
      -webkit-tap-highlight-color: transparent;
    }
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-slate-100 flex justify-center min-h-full">
<!-- Mobile Frame Container (Max 430px modern device wrapper) -->
<div class="w-full max-w-[430px] bg-white min-h-screen flex flex-col relative pb-32 border-x border-gray-100 shadow-2xl">
<!-- BEGIN: Sticky Header & Restaurant Profile -->
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100">
<!-- Top Bar: Avatar, Info & Quick Action -->
<div class="px-4 pt-3.5 pb-2.5 flex items-center justify-between gap-3">
<div class="flex items-center gap-3">
<div class="w-11 h-11 rounded-2xl bg-brand text-white flex items-center justify-center font-extrabold text-sm shadow-sm ring-2 ring-emerald-50">
          SH
        </div>
<div>
<div class="flex items-center gap-1.5">
<h1 class="text-base font-bold text-gray-900 tracking-tight leading-none">Shawarma House</h1>
<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-brand">Abierto</span>
</div>
<div class="flex items-center gap-2 mt-1 text-[12px] text-gray-500 font-medium">
<span class="flex items-center gap-0.5 text-amber-500 font-bold">
<svg class="w-3.5 h-3.5 fill-current" viewbox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
              4.8
            </span>
<span>•</span>
<span>20-30 min</span>
<span>•</span>
<span class="text-emerald-700 font-semibold">Envío gratis</span>
</div>
</div>
</div>
<!-- Quick Search Toggle / Share -->
<a class="w-9 h-9 rounded-full bg-gray-50 border border-gray-200/60 flex items-center justify-center text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 transition tap-highlight-transparent" href="https://wa.me/?text=Hola%20Shawarma%20House,%20tengo%20una%20consulta" rel="noopener" target="_blank" title="Contactar por WhatsApp">
<svg class="w-4 h-4 fill-current" viewbox="0 0 24 24">
<path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.541 1.936.82 2.791.82 3.181 0 5.767-2.587 5.768-5.766.001-3.182-2.585-5.806-5.768-5.806zm8.887 5.767c0 4.909-3.991 8.899-8.887 8.899-1.545 0-2.996-.398-4.269-1.096l-5.762 1.508 1.536-5.617c-.777-1.319-1.226-2.859-1.226-4.502 0-4.908 3.992-8.899 8.888-8.899 4.896 0 8.887 3.991 8.887 8.899z"></path>
</svg>
</a>
</div>
<!-- Search Input -->
<div class="px-4 pb-2.5">
<div class="relative flex items-center">
<svg class="w-4 h-4 text-gray-400 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewbox="0 0 24 24">
<path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"></path>
</svg>
<input class="w-full bg-gray-50 border border-gray-200/80 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition" id="menu-search-input" oninput="filterMenuItems(this.value)" placeholder="Buscar shawarma, falafel, bebidas..." type="text"/>
</div>
</div>
<!-- Scrollable Sticky Category Tabs -->
<nav aria-label="Categorías del menú" class="flex gap-2 px-4 pb-2.5 overflow-x-auto no-scrollbar scroll-smooth">
<a class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold bg-brand text-white shadow-xs tap-highlight-transparent" href="#destacados">
        🔥 Populares
      </a>
<a class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200/70 transition tap-highlight-transparent" href="#shawarmas">
        Shawarmas
      </a>
<a class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200/70 transition tap-highlight-transparent" href="#platos">
        Platos &amp; Falafel
      </a>
<a class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200/70 transition tap-highlight-transparent" href="#acompanamientos">
        Bebidas &amp; Dips
      </a>
</nav>
</header>
<!-- END: Sticky Header -->
<!-- BEGIN: Main Menu Feed -->
<main class="flex-1 space-y-6 pt-3">
<!-- 1. SECCIÓN DESTACADOS / POPULARES (Carrusel Compacto Horizontal) -->
<section class="space-y-2.5" id="destacados">
<div class="px-4 flex items-center justify-between">
<h2 class="text-sm font-bold text-gray-900 tracking-tight flex items-center gap-1.5">
<span>🌟</span> Destacados de hoy
        </h2>
<span class="text-[11px] font-medium text-gray-500">Los preferidos</span>
</div>
<div class="flex gap-3 px-4 overflow-x-auto no-scrollbar scroll-smooth pb-1">
<!-- Highlight Card 1 -->
<div class="shrink-0 w-52 bg-white rounded-2xl border border-gray-100 shadow-subtle p-2.5 flex flex-col justify-between group">
<div class="relative h-28 w-full rounded-xl overflow-hidden bg-gray-100 mb-2">
<img alt="Shawarma de Pollo" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCKyfuf7hhh8nKveiwtxdgq8vhX1suULIVd9FZzr3zNVRKu7D6E2Px0aMK7C2OHa-bbJc0Ozl1jbrHPmJ6VPJg5JPuw6cr0sDANf1Ie-4fIIheRossXKGtWRb3Yyx4jUo3RkkgkZstFWKfN7Jvvcroqq46kdhqJ2fDjCQMp_VDd-dZlRQO8-h9VRkn9WsObI-wqfRlX78k1FdN-Vg4PbHTxLCFMIZt-Dtc1xASk3mU"/>
<span class="absolute top-2 left-2 bg-black/60 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
              ★ Nº1 Ventas
            </span>
</div>
<div>
<h3 class="text-xs font-bold text-gray-900 line-clamp-1">Shawarma de pollo</h3>
<p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Pechuga marinada, ajo y vegetales</p>
</div>
<div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
<span class="text-xs font-extrabold text-gray-900">$5.00</span>
<button aria-label="Agregar Shawarma de Pollo" class="w-7 h-7 rounded-full bg-brand text-white flex items-center justify-center hover:bg-brand-hover active:scale-90 transition tap-highlight-transparent" onclick="handleAddToCart(this, 'Shawarma de pollo', 5.00)">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
</button>
</div>
</div>
<!-- Highlight Card 2 -->
<div class="shrink-0 w-52 bg-white rounded-2xl border border-gray-100 shadow-subtle p-2.5 flex flex-col justify-between group">
<div class="relative h-28 w-full rounded-xl overflow-hidden bg-gray-100 mb-2">
<img alt="Shawarma de Carne" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDLsmk-79MK6FNPpYuoJ0hUA-otmosG0ya8eTT8-3Vg96q7YNuV6OqIAvAu1z3y9HLB6Qhz7AvA_9gflOJvwO1Zxb6L5syIhQOPPk_QJjYfj3KnjujMOcer5GtqL3FKVktF3-YjfyPEc54duX_y3DsYqa2nrAagH3i6g0b8rSARX8feXNsbOof9wpS698zeKAT6CmrDCuoK-KZH6B0XvhYOOdGibAdjyWHBjB-Cnhw"/>
<span class="absolute top-2 left-2 bg-amber-600/90 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
              Receta Especial
            </span>
</div>
<div>
<h3 class="text-xs font-bold text-gray-900 line-clamp-1">Shawarma de carne</h3>
<p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Ternera especiada al sumac &amp; tahini</p>
</div>
<div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
<span class="text-xs font-extrabold text-gray-900">$6.00</span>
<button aria-label="Agregar Shawarma de Carne" class="w-7 h-7 rounded-full bg-brand text-white flex items-center justify-center hover:bg-brand-hover active:scale-90 transition tap-highlight-transparent" onclick="handleAddToCart(this, 'Shawarma de carne', 6.00)">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
</button>
</div>
</div>
<!-- Highlight Card 3 -->
<div class="shrink-0 w-52 bg-white rounded-2xl border border-gray-100 shadow-subtle p-2.5 flex flex-col justify-between group">
<div class="relative h-28 w-full rounded-xl overflow-hidden bg-gray-100 mb-2">
<img alt="Falafel Crujiente" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy" src="https://lh3.googleusercontent.com/aida-public/AB6AXuClIUDdmI3zPfwwUCdh70lW1KMj1TwGRFV4X3f9pq8eH9T4pv-mOQQU3T1yx1K8F9f48GCEcFKGCOgr1b-A12WSCTFWEiJ4wPs61kQdkeYvoH5HXOAb-lrXt9e0PYXLS_SEZYo0iZlcSRWKMW9md4ZRg_SUBhCDt1tedLDyAUlgUJH6pyGQ9xA_y-7A-Nt0YYSp-CLUsJcYUFSLnBjMXAXuRqI4VopUkw8kbJAjskk"/>
<span class="absolute top-2 left-2 bg-emerald-700/90 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
              Veggie
            </span>
</div>
<div>
<h3 class="text-xs font-bold text-gray-900 line-clamp-1">Falafel x4 unidades</h3>
<p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Croquetas doradas con dip de tarator</p>
</div>
<div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
<span class="text-xs font-extrabold text-gray-900">$4.50</span>
<button aria-label="Agregar Falafel" class="w-7 h-7 rounded-full bg-brand text-white flex items-center justify-center hover:bg-brand-hover active:scale-90 transition tap-highlight-transparent" onclick="handleAddToCart(this, 'Falafel x4', 4.50)">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
</button>
</div>
</div>
</div>
</section>
<!-- Upsell Banner Compacto y Elegante -->
<div class="px-4">
<div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50/60 rounded-2xl p-3 border border-emerald-100 flex items-center justify-between gap-3">
<div class="flex items-center gap-2.5">
<span class="text-xl">🍟</span>
<div>
<h3 class="text-xs font-bold text-emerald-950">Hazlo Combo Especial (+$2.50)</h3>
<p class="text-[11px] text-emerald-800/80">Suma papas rústicas y bebida 350ml fría.</p>
</div>
</div>
<button class="shrink-0 bg-brand hover:bg-brand-hover text-white text-[11px] font-bold px-3 py-1.5 rounded-lg active:scale-95 transition tap-highlight-transparent" onclick="handleAddToCart(this, 'Upgrade Combo Papas + Bebida', 2.50)">
          + Agregar
        </button>
</div>
</div>
<!-- 2. SECCIÓN SHAWARMAS (Horizontal List Cards de alta escaneabilidad) -->
<section class="px-4 space-y-3" id="shawarmas">
<div class="border-b border-gray-100 pb-1.5 flex items-baseline justify-between">
<h2 class="text-base font-bold text-gray-900">Shawarmas</h2>
<span class="text-[11px] text-gray-400 font-medium">Enrollados al fuego</span>
</div>
<!-- Item Card: Shawarma de Pollo -->
<article class="menu-item-row bg-white rounded-2xl p-3 border border-gray-100 shadow-subtle hover:border-gray-200 transition flex gap-3 items-center justify-between" data-item-name="shawarma pollo">
<div class="flex-1 min-w-0 pr-1">
<div class="flex items-center gap-1.5 mb-0.5">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
<span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">100% Pechuga</span>
</div>
<h3 class="text-sm font-bold text-gray-900 tracking-tight">Shawarma de pollo</h3>
<p class="text-xs text-gray-500 line-clamp-2 mt-0.5 leading-snug">
            Pan pita artesanal, pollo marinado en especias orientales, vegetales crocantes y crema de ajo libanesa casera.
          </p>
<div class="mt-2 text-sm font-bold text-gray-900">
            $5.00
          </div>
</div>
<div class="relative shrink-0 w-24 h-24 rounded-xl overflow-hidden bg-gray-100">
<img alt="Shawarma de pollo" class="w-full h-full object-cover" loading="lazy" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCKyfuf7hhh8nKveiwtxdgq8vhX1suULIVd9FZzr3zNVRKu7D6E2Px0aMK7C2OHa-bbJc0Ozl1jbrHPmJ6VPJg5JPuw6cr0sDANf1Ie-4fIIheRossXKGtWRb3Yyx4jUo3RkkgkZstFWKfN7Jvvcroqq46kdhqJ2fDjCQMp_VDd-dZlRQO8-h9VRkn9WsObI-wqfRlX78k1FdN-Vg4PbHTxLCFMIZt-Dtc1xASk3mU"/>
<button aria-label="Agregar al carrito" class="absolute bottom-1.5 right-1.5 w-7 h-7 bg-white text-brand rounded-full shadow-md flex items-center justify-center hover:bg-emerald-50 active:scale-90 transition tap-highlight-transparent font-bold" onclick="handleAddToCart(this, 'Shawarma de pollo', 5.00)">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
</button>
</div>
</article>
<!-- Item Card: Shawarma de Carne -->
<article class="menu-item-row bg-white rounded-2xl p-3 border border-gray-100 shadow-subtle hover:border-gray-200 transition flex gap-3 items-center justify-between" data-item-name="shawarma carne res ternera">
<div class="flex-1 min-w-0 pr-1">
<div class="flex items-center gap-1.5 mb-0.5">
<span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
<span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Res Selecta</span>
</div>
<h3 class="text-sm font-bold text-gray-900 tracking-tight">Shawarma de carne</h3>
<p class="text-xs text-gray-500 line-clamp-2 mt-0.5 leading-snug">
            Tiras finas de ternera macerada con sumac, perejil fresco, cebolla morada y salsa de tahini artesanal.
          </p>
<div class="mt-2 text-sm font-bold text-gray-900">
            $6.00
          </div>
</div>
<div class="relative shrink-0 w-24 h-24 rounded-xl overflow-hidden bg-gray-100">
<img alt="Shawarma de carne" class="w-full h-full object-cover" loading="lazy" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDLsmk-79MK6FNPpYuoJ0hUA-otmosG0ya8eTT8-3Vg96q7YNuV6OqIAvAu1z3y9HLB6Qhz7AvA_9gflOJvwO1Zxb6L5syIhQOPPk_QJjYfj3KnjujMOcer5GtqL3FKVktF3-YjfyPEc54duX_y3DsYqa2nrAagH3i6g0b8rSARX8feXNsbOof9wpS698zeKAT6CmrDCuoK-KZH6B0XvhYOOdGibAdjyWHBjB-Cnhw"/>
<button aria-label="Agregar al carrito" class="absolute bottom-1.5 right-1.5 w-7 h-7 bg-white text-brand rounded-full shadow-md flex items-center justify-center hover:bg-emerald-50 active:scale-90 transition tap-highlight-transparent font-bold" onclick="handleAddToCart(this, 'Shawarma de carne', 6.00)">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
</button>
</div>
</article>
</section>
<!-- 3. SECCIÓN PLATOS Y FALAFEL -->
<section class="px-4 space-y-3" id="platos">
<div class="border-b border-gray-100 pb-1.5 flex items-baseline justify-between">
<h2 class="text-base font-bold text-gray-900">Platos &amp; Falafel</h2>
<span class="text-[11px] text-gray-400 font-medium">Tradición oriental</span>
</div>
<!-- Item Card: Falafel x4 -->
<article class="menu-item-row bg-white rounded-2xl p-3 border border-gray-100 shadow-subtle hover:border-gray-200 transition flex gap-3 items-center justify-between" data-item-name="falafel unidades croquetas vegetariano">
<div class="flex-1 min-w-0 pr-1">
<div class="flex items-center gap-1.5 mb-0.5">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
<span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">100% Vegetariano</span>
</div>
<h3 class="text-sm font-bold text-gray-900 tracking-tight">Falafel x4 unidades</h3>
<p class="text-xs text-gray-500 line-clamp-2 mt-0.5 leading-snug">
            4 Croquetas doradas de garbanzo con hierbas frescas, acompañadas con dip tarator y ensalada árabe.
          </p>
<div class="mt-2 text-sm font-bold text-gray-900">
            $4.50
          </div>
</div>
<div class="relative shrink-0 w-24 h-24 rounded-xl overflow-hidden bg-gray-100">
<img alt="Falafel x4" class="w-full h-full object-cover" loading="lazy" src="https://lh3.googleusercontent.com/aida-public/AB6AXuClIUDdmI3zPfwwUCdh70lW1KMj1TwGRFV4X3f9pq8eH9T4pv-mOQQU3T1yx1K8F9f48GCEcFKGCOgr1b-A12WSCTFWEiJ4wPs61kQdkeYvoH5HXOAb-lrXt9e0PYXLS_SEZYo0iZlcSRWKMW9md4ZRg_SUBhCDt1tedLDyAUlgUJH6pyGQ9xA_y-7A-Nt0YYSp-CLUsJcYUFSLnBjMXAXuRqI4VopUkw8kbJAjskk"/>
<button aria-label="Agregar al carrito" class="absolute bottom-1.5 right-1.5 w-7 h-7 bg-white text-brand rounded-full shadow-md flex items-center justify-center hover:bg-emerald-50 active:scale-90 transition tap-highlight-transparent font-bold" onclick="handleAddToCart(this, 'Falafel x4', 4.50)">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
</button>
</div>
</article>
<!-- Item Card: Hummus con Pita -->
<article class="menu-item-row bg-white rounded-2xl p-3 border border-gray-100 shadow-subtle hover:border-gray-200 transition flex gap-3 items-center justify-between" data-item-name="hummus pita entrada dip garbanzo">
<div class="flex-1 min-w-0 pr-1">
<div class="flex items-center gap-1.5 mb-0.5">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
<span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Entrada Casera</span>
</div>
<h3 class="text-sm font-bold text-gray-900 tracking-tight">Hummus con pan pita</h3>
<p class="text-xs text-gray-500 line-clamp-2 mt-0.5 leading-snug">
            Crema suave de garbanzo con tahini, aceite de oliva virgen extra, pimentón dulce y 2 panes pita calientes.
          </p>
<div class="mt-2 text-sm font-bold text-gray-900">
            $3.50
          </div>
</div>
<div class="relative shrink-0 w-24 h-24 rounded-xl overflow-hidden bg-emerald-50 flex items-center justify-center">
<span class="text-3xl">🫓</span>
<button aria-label="Agregar al carrito" class="absolute bottom-1.5 right-1.5 w-7 h-7 bg-white text-brand rounded-full shadow-md flex items-center justify-center hover:bg-emerald-50 active:scale-90 transition tap-highlight-transparent font-bold" onclick="handleAddToCart(this, 'Hummus con pan pita', 3.50)">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
</button>
</div>
</article>
</section>
<!-- 4. SECCIÓN BEBIDAS & ACOMPAÑAMIENTOS -->
<section class="px-4 space-y-3" id="acompanamientos">
<div class="border-b border-gray-100 pb-1.5 flex items-baseline justify-between">
<h2 class="text-base font-bold text-gray-900">Bebidas &amp; Refrescos</h2>
<span class="text-[11px] text-gray-400 font-medium">Para acompañar</span>
</div>
<!-- Item Card: Limonada con menta -->
<article class="menu-item-row bg-white rounded-2xl p-3 border border-gray-100 shadow-subtle hover:border-gray-200 transition flex gap-3 items-center justify-between" data-item-name="limonada menta bebida fresca limon">
<div class="flex-1 min-w-0 pr-1">
<div class="flex items-center gap-1.5 mb-0.5">
<span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
<span class="text-[10px] font-bold uppercase tracking-wider text-teal-700">Natural Fría</span>
</div>
<h3 class="text-sm font-bold text-gray-900 tracking-tight">Limonada con menta 400ml</h3>
<p class="text-xs text-gray-500 line-clamp-2 mt-0.5 leading-snug">
            Jugo natural de limón recién exprimido, hojas de hierbabuena fresca y hielo frappé.
          </p>
<div class="mt-2 text-sm font-bold text-gray-900">
            $2.00
          </div>
</div>
<div class="relative shrink-0 w-24 h-24 rounded-xl overflow-hidden bg-teal-50 flex items-center justify-center">
<span class="text-3xl">🍋</span>
<button aria-label="Agregar al carrito" class="absolute bottom-1.5 right-1.5 w-7 h-7 bg-white text-brand rounded-full shadow-md flex items-center justify-center hover:bg-emerald-50 active:scale-90 transition tap-highlight-transparent font-bold" onclick="handleAddToCart(this, 'Limonada con menta', 2.00)">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
</button>
</div>
</article>
</section>
</main>
<!-- END: Main Menu Feed -->
<!-- BEGIN: Bottom Floating Bar Unificado (Clean Uber/Deliveroo Style) -->
<div class="fixed bottom-4 inset-x-0 mx-auto max-w-[430px] px-4 z-50 pointer-events-none">
<div class="pointer-events-auto bg-gray-900/95 backdrop-blur-md text-white rounded-2xl shadow-float-bar border border-gray-800/80 p-2.5 flex items-center justify-between transition-all duration-300 transform" id="unified-floating-bar">
<!-- Cart Info Pill -->
<div class="flex items-center gap-3 pl-1.5">
<div class="relative">
<div class="w-9 h-9 rounded-xl bg-brand text-white flex items-center justify-center font-bold text-xs shadow-inner">
<span id="floating-cart-count">1</span>
</div>
</div>
<div class="leading-tight">
<p class="text-[11px] text-gray-400 font-medium">Subtotal pedido</p>
<p class="text-sm font-extrabold text-white" id="floating-cart-total">$5.00</p>
</div>
</div>
<!-- Action Buttons Group -->
<div class="flex items-center gap-2">
<!-- Quick WhatsApp Button Icon (Discreet) -->
<button class="w-10 h-10 rounded-xl bg-gray-800 hover:bg-emerald-900 text-emerald-400 flex items-center justify-center border border-gray-700/60 active:scale-90 transition tap-highlight-transparent" onclick="sendOrderViaWhatsApp()" title="Pedir vía WhatsApp">
<svg class="w-4 h-4 fill-current" viewbox="0 0 24 24">
<path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.541 1.936.82 2.791.82 3.181 0 5.767-2.587 5.768-5.766.001-3.182-2.585-5.806-5.768-5.806zm8.887 5.767c0 4.909-3.991 8.899-8.887 8.899-1.545 0-2.996-.398-4.269-1.096l-5.762 1.508 1.536-5.617c-.777-1.319-1.226-2.859-1.226-4.502 0-4.908 3.992-8.899 8.888-8.899 4.896 0 8.887 3.991 8.887 8.899z"></path>
</svg>
</button>
<!-- Main Cart CTA -->
<button class="bg-brand hover:bg-brand-hover active:scale-95 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-sm tap-highlight-transparent" onclick="openCartModal()">
<span>Ver pedido</span>
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
</button>
</div>
</div>
</div>
<!-- END: Bottom Floating Bar -->
<!-- BEGIN: Order Details Modal / Drawer -->
<div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex flex-col justify-end hidden opacity-0 transition-opacity duration-200" id="cart-modal">
<div class="bg-white rounded-t-3xl max-h-[85vh] w-full max-w-[430px] mx-auto p-5 pb-8 flex flex-col shadow-2xl transform translate-y-full transition-transform duration-200" id="cart-modal-panel">
<div class="flex items-center justify-between pb-3 border-b border-gray-100">
<div>
<h2 class="text-base font-bold text-gray-900">Resumen de tu pedido</h2>
<p class="text-xs text-gray-500">Shawarma House • 20-30 min</p>
</div>
<button class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-800" onclick="closeCartModal()">
          ✕
        </button>
</div>
<!-- Item List Container -->
<div class="py-3 space-y-3 overflow-y-auto max-h-60 no-scrollbar divide-y divide-gray-50" id="modal-cart-items">
<!-- Injected via JavaScript -->
</div>
<!-- Total & Action -->
<div class="pt-3 border-t border-gray-100 space-y-3">
<div class="flex justify-between items-center text-sm">
<span class="text-gray-500">Total a pagar:</span>
<span class="text-lg font-extrabold text-gray-900" id="modal-cart-total">$5.00</span>
</div>
<button class="w-full bg-brand hover:bg-brand-hover text-white font-bold text-sm py-3.5 rounded-xl shadow-md flex items-center justify-center gap-2 active:scale-98 transition tap-highlight-transparent" onclick="sendOrderViaWhatsApp()">
<svg class="w-4 h-4 fill-current" viewbox="0 0 24 24">
<path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.541 1.936.82 2.791.82 3.181 0 5.767-2.587 5.768-5.766.001-3.182-2.585-5.806-5.768-5.806zm8.887 5.767c0 4.909-3.991 8.899-8.887 8.899-1.545 0-2.996-.398-4.269-1.096l-5.762 1.508 1.536-5.617c-.777-1.319-1.226-2.859-1.226-4.502 0-4.908 3.992-8.899 8.888-8.899 4.896 0 8.887 3.991 8.887 8.899z"></path>
</svg>
<span>Confirmar y pedir por WhatsApp</span>
</button>
</div>
</div>
</div>
<!-- END: Order Details Modal -->
</div>
<!-- BEGIN: Interactive Client Logic -->
<script data-purpose="app-logic">
  let cart = [
    { name: 'Shawarma de pollo', price: 5.00, count: 1 }
  ];

  function handleAddToCart(btn, itemName, itemPrice) {
    const existing = cart.find(i => i.name === itemName);
    if (existing) {
      existing.count += 1;
    } else {
      cart.push({ name: itemName, price: itemPrice, count: 1 });
    }

    // Micro feedback on the button
    const originalContent = btn.innerHTML;
    btn.innerHTML = `<span class="text-xs">✓</span>`;
    btn.classList.add('scale-110', 'bg-emerald-600');
    setTimeout(() => {
      btn.innerHTML = originalContent;
      btn.classList.remove('scale-110', 'bg-emerald-600');
      updateCartUI();
    }, 280);
  }

  function modifyQuantity(itemName, delta) {
    const item = cart.find(i => i.name === itemName);
    if (!item) return;

    item.count += delta;
    if (item.count <= 0) {
      cart = cart.filter(i => i.name !== itemName);
    }
    updateCartUI();
    renderModalItems();
  }

  function updateCartUI() {
    const totalItems = cart.reduce((sum, i) => sum + i.count, 0);
    const totalPrice = cart.reduce((sum, i) => sum + (i.price * i.count), 0);
    
    const countEl = document.getElementById('floating-cart-count');
    const totalEl = document.getElementById('floating-cart-total');
    const barEl = document.getElementById('unified-floating-bar');

    if (totalItems > 0) {
      countEl.textContent = totalItems;
      totalEl.textContent = `$${totalPrice.toFixed(2)}`;
      barEl.classList.remove('opacity-0', 'translate-y-8', 'pointer-events-none');
    } else {
      barEl.classList.add('opacity-0', 'translate-y-8', 'pointer-events-none');
      closeCartModal();
    }
  }

  function openCartModal() {
    renderModalItems();
    const modal = document.getElementById('cart-modal');
    const panel = document.getElementById('cart-modal-panel');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
      modal.classList.remove('opacity-0');
      panel.classList.remove('translate-y-full');
    });
  }

  function closeCartModal() {
    const modal = document.getElementById('cart-modal');
    const panel = document.getElementById('cart-modal-panel');
    modal.classList.add('opacity-0');
    panel.classList.add('translate-y-full');
    setTimeout(() => {
      modal.classList.add('hidden');
    }, 200);
  }

  function renderModalItems() {
    const container = document.getElementById('modal-cart-items');
    const modalTotal = document.getElementById('modal-cart-total');
    const totalPrice = cart.reduce((sum, i) => sum + (i.price * i.count), 0);

    modalTotal.textContent = `$${totalPrice.toFixed(2)}`;

    if (cart.length === 0) {
      container.innerHTML = `<p class="text-xs text-gray-400 text-center py-4">El carrito está vacío</p>`;
      return;
    }

    container.innerHTML = cart.map(item => `
      <div class="flex items-center justify-between py-2 text-xs">
        <div>
          <p class="font-bold text-gray-900">${item.name}</p>
          <p class="text-gray-500 font-medium">$${item.price.toFixed(2)} c/u</p>
        </div>
        <div class="flex items-center gap-2">
          <button onclick="modifyQuantity('${item.name}', -1)" class="w-6 h-6 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold flex items-center justify-center">-</button>
          <span class="font-bold text-sm w-4 text-center text-gray-900">${item.count}</span>
          <button onclick="modifyQuantity('${item.name}', 1)" class="w-6 h-6 rounded-md bg-emerald-50 hover:bg-emerald-100 text-brand font-bold flex items-center justify-center">+</button>
        </div>
      </div>
    `).join('');
  }

  function filterMenuItems(query) {
    const term = query.toLowerCase().trim();
    const rows = document.querySelectorAll('.menu-item-row');
    rows.forEach(row => {
      const text = row.getAttribute('data-item-name') || '';
      if (!term || text.includes(term)) {
        row.style.display = 'flex';
      } else {
        row.style.display = 'none';
      }
    });
  }

  function sendOrderViaWhatsApp() {
    if (cart.length === 0) return;
    const itemsList = cart.map(i => `• ${i.count}x ${i.name} ($${(i.price * i.count).toFixed(2)})`).join('%0A');
    const totalPrice = cart.reduce((sum, i) => sum + (i.price * i.count), 0);
    const text = `Hola%20Shawarma%20House,%20quisiera%20confirmar%20mi%20pedido:%0A%0A${itemsList}%0A%0A*Total%20a%20pagar:%20$${totalPrice.toFixed(2)}*%0A%0A¿Cuál%20es%20el%20tiempo%20estimado?`;
    window.open(`https://wa.me/?text=${text}`, '_blank');
  }

  // Initialize
  updateCartUI();
</script>
<!-- END: Interactive Client Logic -->
</body></html>