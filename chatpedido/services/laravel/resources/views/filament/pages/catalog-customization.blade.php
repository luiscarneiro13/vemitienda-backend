<x-filament-panels::page class="catalog-customization-page">
    @php
        $templates = [
            [
                'id' => 'grid_2col',
                'name' => 'Cuadrícula 2x2 (Grid Visual)',
                'description' => '2 columnas con fotos amplias 4:3, badges y chips de categoría. Muy recomendada para comida rápida.',
                'image' => asset('images/templates/grid_2col.png'),
                'demo_url' => asset('templates-demo/grid_2col.html'),
                'badge' => 'Recomendada',
                'badge_style' => 'background: rgba(16, 185, 129, 0.15); color: #047857; border: 1px solid rgba(16, 185, 129, 0.3);',
            ],
            [
                'id' => 'compact_list',
                'name' => 'Lista Compacta Express',
                'description' => 'Filas verticales con miniatura cuadrada a la izquierda. Máxima velocidad de selección y lectura del menú.',
                'image' => asset('images/templates/compact_list.png'),
                'demo_url' => asset('templates-demo/compact_list.html'),
                'badge' => 'Alta Conversión',
                'badge_style' => 'background: rgba(59, 130, 246, 0.15); color: #1d4ed8; border: 1px solid rgba(59, 130, 246, 0.3);',
            ],
            [
                'id' => 'editorial_cards',
                'name' => 'Feed Editorial / Gourmet',
                'description' => 'Tarjetas panorámicas de ancho completo con presencia visual. Ideal para platos de autor y restaurantes.',
                'image' => asset('images/templates/editorial_cards.png'),
                'demo_url' => asset('templates-demo/editorial_cards.html'),
                'badge' => 'Gourmet',
                'badge_style' => 'background: rgba(245, 158, 11, 0.15); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);',
            ],
            [
                'id' => 'inline_selector',
                'name' => 'Opciones Inline Modernas',
                'description' => 'Tarjetas horizontales con selectores directos de variantes y navegación con emojis sin ventanas modales.',
                'image' => asset('images/templates/inline_selector.png'),
                'demo_url' => asset('templates-demo/inline_selector.html'),
                'badge' => 'Sin Modales',
                'badge_style' => 'background: rgba(139, 92, 246, 0.15); color: #6d28d9; border: 1px solid rgba(139, 92, 246, 0.3);',
            ],
            [
                'id' => 'flash_deals',
                'name' => 'Ofertas Flash & Urgencia',
                'description' => 'Contador regresivo en vivo, barras de agotamiento de stock y badges de descuento con precio anterior tachado.',
                'image' => asset('images/templates/flash_deals.png'),
                'demo_url' => asset('templates-demo/flash_deals.html'),
                'badge' => 'Ofertas Flash',
                'badge_style' => 'background: rgba(239, 68, 68, 0.15); color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.3);',
            ],
            [
                'id' => 'dark_midnight',
                'name' => 'Modo Oscuro (Midnight Craves)',
                'description' => 'Estética nocturna en negro profundo (#121212) con acentos neón y carrusel superior para delivery nocturno.',
                'image' => asset('images/templates/dark_midnight.png'),
                'demo_url' => asset('templates-demo/dark_midnight.html'),
                'badge' => 'Dark Mode',
                'badge_style' => 'background: rgba(244, 63, 94, 0.15); color: #be123c; border: 1px solid rgba(244, 63, 94, 0.3);',
            ],
            [
                'id' => 'combo_wizard',
                'name' => 'Wizard Paso a Paso (Arma tu Combo)',
                'description' => 'Flujo guiado para armar combos: proteína, acompañamiento, bebida y salsas con foto en cada opción.',
                'image' => asset('images/templates/combo_wizard.png'),
                'demo_url' => asset('templates-demo/combo_wizard.html'),
                'badge' => 'Arma tu Combo',
                'badge_style' => 'background: rgba(14, 165, 233, 0.15); color: #0369a1; border: 1px solid rgba(14, 165, 233, 0.3);',
            ],
            [
                'id' => 'family_combos',
                'name' => 'Combos Familiares & Catering',
                'description' => 'Tarjetas para compartir con viñetas de ítems incluidos y desglose de precio por persona.',
                'image' => asset('images/templates/family_combos.png'),
                'demo_url' => asset('templates-demo/family_combos.html'),
                'badge' => 'Para Grupos',
                'badge_style' => 'background: rgba(168, 85, 247, 0.15); color: #7e22ce; border: 1px solid rgba(168, 85, 247, 0.3);',
            ],
            [
                'id' => 'brand_directory',
                'name' => 'Directorio Multi-Marca / Cocinas',
                'description' => 'Agrupación visual por cocinas y marcas aliadas con encabezados destacados y filtros de concepto.',
                'image' => asset('images/templates/brand_directory.png'),
                'demo_url' => asset('templates-demo/brand_directory.html'),
                'badge' => 'Multi-Cocina',
                'badge_style' => 'background: rgba(20, 184, 166, 0.15); color: #0f766e; border: 1px solid rgba(20, 184, 166, 0.3);',
            ],
            [
                'id' => 'healthy_macros',
                'name' => 'Menú Fitness & Saludable',
                'description' => 'Filtros nutricionales (Keto, Alta Proteína, Sin Gluten) con desglose de Kcal, Proteínas y Carbos.',
                'image' => asset('images/templates/healthy_macros.png'),
                'demo_url' => asset('templates-demo/healthy_macros.html'),
                'badge' => 'Fitness & Salud',
                'badge_style' => 'background: rgba(132, 204, 22, 0.15); color: #4d7c0f; border: 1px solid rgba(132, 204, 22, 0.3);',
            ],
        ];
        $currentTemplate = $data['template_id'] ?? 'grid_2col';
    @endphp

    <style>
        /* Separación de la página y bloques principales */
        .catalog-customization-page {
            display: flex;
            flex-direction: column;
        }

        .catalog-customization-gallery-wrapper {
            margin-bottom: 36px !important;
        }

        /* Espaciado garantizado entre secciones de Filament */
        .catalog-customization-form .fi-sc.fi-sc-has-gap {
            display: flex !important;
            flex-direction: column !important;
            gap: 28px !important;
        }

        .catalog-customization-form .fi-grid-col {
            margin-bottom: 28px !important;
        }

        .catalog-customization-form .fi-grid-col:last-child {
            margin-bottom: 0 !important;
        }

        .catalog-customization-form section.fi-section {
            border-radius: 16px !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
            background: #ffffff !important;
            overflow: hidden !important;
            margin-bottom: 28px !important;
        }

        .dark .catalog-customization-form section.fi-section {
            border-color: #27272a !important;
            background: #18181b !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) !important;
        }

        /* Encabezado sin bordes divisorios hacia el contenido */
        .catalog-customization-form .fi-section-header {
            padding: 22px 24px 8px !important;
            border-bottom: none !important;
            margin-bottom: 0 !important;
        }

        .dark .catalog-customization-form .fi-section-header {
            border-bottom: none !important;
        }

        /* Eliminar la línea divisoria (border-top / hr) entre header y controles */
        .catalog-customization-form .fi-section-content-ctn {
            border-top: none !important;
        }

        .dark .catalog-customization-form .fi-section-content-ctn {
            border-top: none !important;
        }

        .catalog-customization-form hr,
        .catalog-customization-form .fi-section-content-ctn > hr,
        .catalog-customization-form .fi-section-header + hr {
            display: none !important;
        }

        .catalog-customization-form .fi-section-content {
            padding: 8px 24px 24px !important;
        }

        /* Grilla de 3 plantillas por fila en desktop */
        .catalog-templates-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 20px !important;
        }
        @media (max-width: 1100px) {
            .catalog-templates-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }
        @media (max-width: 640px) {
            .catalog-templates-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* Tarjeta de plantilla */
        .catalog-template-card {
            display: flex;
            flex-direction: column;
            border-radius: 14px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            background: #ffffff;
            transition: all 0.2s ease;
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .catalog-template-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        .catalog-template-card.is-selected {
            border-color: #6135bc !important;
            box-shadow: 0 0 0 3px rgba(97, 53, 188, 0.2), 0 8px 20px rgba(36, 32, 52, 0.08) !important;
            background: rgba(238, 232, 251, 0.35) !important;
        }
        .dark .catalog-template-card {
            background: #18181b;
            border-color: #27272a;
        }
        .dark .catalog-template-card.is-selected {
            border-color: #9f7be5 !important;
            box-shadow: 0 0 0 3px rgba(159, 123, 229, 0.25) !important;
            background: rgba(97, 53, 188, 0.12) !important;
        }

        /* Contenedor miniatura modo dispositivo (vista completa sin recortes) */
        .catalog-template-thumb-wrap {
            position: relative;
            width: 100%;
            height: 195px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid #f1f5f9;
            padding: 10px 12px;
            cursor: pointer;
            text-decoration: none;
        }
        .dark .catalog-template-thumb-wrap {
            background: #141416;
            border-bottom-color: #27272a;
        }
        .catalog-template-device-mockup {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.25s ease;
        }
        .catalog-template-device-mockup img {
            max-height: 175px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            border-radius: 6px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        }
        .catalog-template-thumb-wrap:hover .catalog-template-device-mockup {
            transform: scale(1.05);
        }

        /* Overlay al pasar el cursor sobre la miniatura */
        .catalog-template-hover-overlay {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 5;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            gap: 6px;
            text-align: center;
            padding: 10px;
        }
        .catalog-template-thumb-wrap:hover .catalog-template-hover-overlay {
            opacity: 1;
        }

        /* Badges de estilo */
        .catalog-template-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
            background-color: rgba(255, 255, 255, 0.94) !important;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 2px 8px rgba(36, 32, 52, 0.14);
            z-index: 6;
        }

        /* Ícono checkmark restringido y compacto */
        .catalog-template-check {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 26px !important;
            height: 26px !important;
            min-width: 26px !important;
            min-height: 26px !important;
            max-width: 26px !important;
            max-height: 26px !important;
            border-radius: 50% !important;
            background: #6135bc !important;
            color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 2px 6px rgba(36, 32, 52, 0.25) !important;
            flex-shrink: 0 !important;
            z-index: 10 !important;
        }
        .catalog-template-check svg {
            width: 14px !important;
            height: 14px !important;
            min-width: 14px !important;
            min-height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: block !important;
            stroke: #ffffff !important;
        }

        /* Cuerpo de la tarjeta */
        .catalog-template-body {
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: space-between;
        }
        .catalog-template-body h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            line-height: 1.3;
        }
        .dark .catalog-template-body h4 {
            color: #f3f4f6;
        }
        .catalog-template-body p {
            margin: 4px 0 0;
            font-size: 11.5px;
            color: #6b7280;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .dark .catalog-template-body p {
            color: #9ca3af;
        }

        /* Fila de botones de acción de cada tarjeta */
        .catalog-template-actions-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #f3f4f6;
        }
        .dark .catalog-template-actions-row {
            border-top-color: #27272a;
        }

        .catalog-template-btn-demo {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 600;
            text-decoration: none;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
            transition: all 0.15s ease;
        }
        .catalog-template-btn-demo:hover {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .dark .catalog-template-btn-demo {
            background: #27272a;
            color: #e4e4e7;
            border-color: #3f3f46;
        }
        .dark .catalog-template-btn-demo:hover {
            background: #3f3f46;
            color: #ffffff;
        }
        .catalog-template-btn-demo svg {
            width: 13px;
            height: 13px;
        }

        .catalog-template-btn-select {
            flex: 1;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: center;
        }
        .catalog-template-btn-select.is-active {
            background: #6135bc;
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(97, 53, 188, 0.3);
        }
        .catalog-template-btn-select.is-active:hover {
            background: #4c269c;
        }
        .catalog-template-btn-select.is-inactive {
            background: #ffffff;
            color: #6135bc;
            border: 1px solid #6135bc;
        }
        .catalog-template-btn-select.is-inactive:hover {
            background: #eee8fb;
        }
        .dark .catalog-template-btn-select.is-inactive {
            background: transparent;
            color: #c2abf0;
            border-color: #9f7be5;
        }
        .dark .catalog-template-btn-select.is-inactive:hover {
            background: rgba(97, 53, 188, 0.2);
        }
        .catalog-template-btn-demo:focus-visible,
        .catalog-template-btn-select:focus-visible,
        .catalog-template-thumb-wrap:focus-visible {
            outline: 2px solid #6135bc;
            outline-offset: 2px;
        }
        .catalog-template-btn-demo:active,
        .catalog-template-btn-select:active {
            transform: translateY(1px);
        }

        .catalog-customization-gallery-card {
            border-radius: 16px;
            border: 1px solid #e3deec;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(36, 32, 52, 0.03);
            background: #ffffff;
        }
        .dark .catalog-customization-gallery-card {
            border-color: #2e293d;
            background: #242034;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
        }
        .catalog-customization-gallery-title {
            font-family: var(--vmt-display, "Space Grotesk", sans-serif);
            font-size: 1.125rem;
            font-weight: 700;
            color: #242034;
            margin: 0;
        }
        .dark .catalog-customization-gallery-title {
            color: #faf9fc;
        }
        .catalog-customization-count-pill {
            border-radius: 9999px;
            background: #eee8fb;
            color: #6135bc;
            padding: 4px 12px;
            font-size: 11.5px;
            font-weight: 700;
            border: 1px solid #ded2f7;
        }
        .dark .catalog-customization-count-pill {
            background: rgba(97, 53, 188, 0.25);
            color: #ded2f7;
            border-color: rgba(194, 171, 240, 0.3);
        }

        /* Barra de acciones al final */
        .catalog-customization-actions {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid #e3deec;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }
        .dark .catalog-customization-actions {
            border-top-color: #2e293d;
        }
    </style>

    <div class="catalog-customization-gallery-wrapper">
        {{-- Galería Visual de las 10 Plantillas (Grilla 3 por fila) --}}
        <div class="catalog-customization-gallery-card">
            <div class="mb-6 flex flex-col gap-1.5" style="margin-bottom: 24px;">
                <div class="flex flex-wrap items-center justify-between gap-2" style="display: flex; align-items: center; justify-content: space-between;">
                    <h3 class="catalog-customization-gallery-title">Plantilla Visual del Menú</h3>
                    <span class="catalog-customization-count-pill">
                        10 Diseños Disponibles
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400" style="margin-top: 4px; font-size: 13px;">Haz clic en la imagen o en <strong>"Ver demo"</strong> para abrir la tienda de ejemplo interactiva en una pestaña nueva, o pulsa <strong>"Elegir"</strong> para aplicarla a tu tienda.</p>
            </div>

            <div class="catalog-templates-grid">
                @foreach ($templates as $tpl)
                    @php $isSelected = ($currentTemplate === $tpl['id']); @endphp
                    <div class="catalog-template-card {{ $isSelected ? 'is-selected' : '' }}">

                        {{-- Contenedor miniatura: abre la tienda demo en pestaña nueva al hacer clic --}}
                        <a href="{{ $tpl['demo_url'] }}" target="_blank" rel="noopener noreferrer" class="catalog-template-thumb-wrap" title="Haz clic para abrir la tienda de ejemplo en una nueva pestaña">
                            <div class="catalog-template-device-mockup">
                                <img src="{{ $tpl['image'] }}" alt="{{ $tpl['name'] }}" loading="lazy">
                            </div>

                            <span class="catalog-template-badge" style="{{ $tpl['badge_style'] }}">
                                {{ $tpl['badge'] }}
                            </span>

                            <div class="catalog-template-hover-overlay">
                                <span>Ver tienda demo ↗</span>
                            </div>

                            @if ($isSelected)
                                <div class="catalog-template-check" title="Plantilla seleccionada">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <div class="catalog-template-body">
                            <div>
                                <h4>{{ $tpl['name'] }}</h4>
                                <p>{{ $tpl['description'] }}</p>
                            </div>

                            {{-- Fila con botón de ver demo y botón de elegir --}}
                            <div class="catalog-template-actions-row">
                                <a href="{{ $tpl['demo_url'] }}" target="_blank" rel="noopener noreferrer" class="catalog-template-btn-demo" title="Ver tienda de ejemplo completa">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                    <span>Ver demo</span>
                                </a>

                                <button type="button"
                                        wire:click="selectTemplate('{{ $tpl['id'] }}')"
                                        class="catalog-template-btn-select {{ $isSelected ? 'is-active' : 'is-inactive' }}">
                                    {{ $isSelected ? '✓ Activa' : 'Elegir' }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Formulario de Configuraciones con espaciado vertical garantizado --}}
    <div class="catalog-customization-form">
        <form wire:submit="save">
            {{ $this->form }}

            <div class="catalog-customization-actions">
                <x-filament::button type="submit" size="lg">
                    Guardar cambios
                </x-filament::button>

                @if ($previewUrl)
                    <x-filament::button tag="a" :href="$previewUrl" target="_blank" color="gray" icon="heroicon-o-arrow-top-right-on-square" size="lg">
                        Ver catálogo público en vivo
                    </x-filament::button>
                @endif
            </div>
        </form>
    </div>
</x-filament-panels::page>
