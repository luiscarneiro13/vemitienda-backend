@extends('layouts.public')

@section('title', 'Catálogo no disponible · ChatPedido')

@section('content')
    <main class="unavailable-page">
        <div class="unavailable-card">
            <a href="{{ route('home') }}" class="unavailable-brand" aria-label="ChatPedido, ir al inicio">
                <span class="unavailable-brand-mark" aria-hidden="true">
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
                <span>Chat<span class="unavailable-brand-accent">Pedido</span><span class="unavailable-brand-dot">.</span></span>
            </a>

            <div class="unavailable-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>

            <h1>Este enlace no es válido o el catálogo no está disponible en este momento.</h1>
            <p>Por favor, pide a la tienda un enlace actualizado por WhatsApp o explora otras tiendas disponibles.</p>

            <a href="{{ route('home') }}" class="unavailable-cta">
                <span>Explorar tiendas en ChatPedido</span>
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </main>
@endsection
