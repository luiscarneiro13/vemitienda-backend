@extends('layouts.public')

@section('title', $payload['store']['name'])

@php
    $validTemplates = [
        'grid_2col', 'compact_list', 'editorial_cards', 'inline_selector',
        'flash_deals', 'dark_midnight', 'combo_wizard', 'family_combos',
        'brand_directory', 'healthy_macros',
    ];
    $template = $payload['settings']['template_id'] ?? 'grid_2col';
    if (! in_array($template, $validTemplates, true)) {
        $template = 'grid_2col';
    }
@endphp

@section('content')
<div x-data="publicCatalog(@js($payload), @js(asset('product-placeholder.svg')))" x-cloak class="catalog-page">
    <div class="mobile-frame">

        {{-- Cabecera de la tienda sticky --}}
        <header class="store-header">
            <!-- Top Bar: Avatar, Info & Quick Action (Stitch) -->
            <div class="store-head">
                <div class="store-head-left">
                    <div class="logo" aria-hidden="true" style="position:relative">
                        <span x-text="payload.store.initials"></span>
                        <template x-if="payload.store.logo_url">
                            <img :src="payload.store.logo_url" :alt="payload.store.name" x-on:error="$el.remove()"
                                 style="position:absolute;inset:0;width:100%;height:100%;border-radius:16px;object-fit:cover;background:var(--cat-lilac)">
                        </template>
                    </div>

                    <div class="store-info">
                        <div class="store-name-row">
                            <h1 x-text="payload.store.name"></h1>
                            <span class="store-status-badge">Abierto</span>
                        </div>
                        <div class="branch" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                            <span class="dot" aria-hidden="true"></span>
                            <span x-text="payload.branch.name + (payload.branch.city ? ' · ' + payload.branch.city : '')"></span>
                            <span x-show="payload.settings?.show_delivery_time && payload.settings?.delivery_time_text" class="delivery-time-pill" x-cloak>
                                <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span x-text="payload.settings.delivery_time_text"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Action WhatsApp Top Bar -->
                <a class="store-head-wa" :href="'https://wa.me/' + payload.branch.whatsapp" target="_blank" rel="noopener" title="Contactar por WhatsApp">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.541 1.936.82 2.791.82 3.181 0 5.767-2.587 5.768-5.766.001-3.182-2.585-5.806-5.768-5.806zm8.887 5.767c0 4.909-3.991 8.899-8.887 8.899-1.545 0-2.996-.398-4.269-1.096l-5.762 1.508 1.536-5.617c-.777-1.319-1.226-2.859-1.226-4.502 0-4.908 3.992-8.899 8.888-8.899 4.896 0 8.887 3.991 8.887 8.899z"></path>
                    </svg>
                </a>
            </div>

            {{-- Banner Promocional --}}
            @include('public-catalog.partials.promo-banner')

            {{-- Barra de Envío Gratis --}}
            @include('public-catalog.partials.free-shipping-bar')

            {{-- Buscador rápido --}}
            <div class="cat-search-wrap" x-show="payload.settings?.show_search && payload.categories.length > 0">
                <div class="cat-search-bar">
                    <svg class="cat-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <input type="search" x-model.debounce.150ms="searchQuery" placeholder="Buscar productos en el menú..." aria-label="Buscar productos">
                    <button x-show="searchQuery" type="button" @click="searchQuery = ''" class="cat-search-clear" aria-label="Limpiar búsqueda">&times;</button>
                </div>
            </div>

            {{-- Navegación por Chips de Categoría --}}
            @include('public-catalog.partials.category-nav')
        </header>

        {{-- Carrusel de Destacados --}}
        @include('public-catalog.partials.featured-carousel')

        <!-- Catálogo vacío -->
        <p x-show="payload.categories.length === 0" class="empty-catalog">Este catálogo aún no tiene productos.</p>

        <!-- Main Feed -->
        <main class="cat-main">
            <!-- Mensaje si no hay resultados de búsqueda -->
            <div x-show="searchQuery && !hasAnyProductVisible()" class="empty-search" x-cloak>
                <p>No encontramos productos para "<span x-text="searchQuery"></span>"</p>
                <button type="button" @click="searchQuery = ''" class="clear-search-btn">Ver todo el menú</button>
            </div>

            <template x-for="category in payload.categories" :key="category.id">
                <section :id="'cat-' + category.id" class="cat-section" x-show="hasCategoryVisible(category)">
                    <div class="cat-section-header">
                        <h2 class="cat-title">
                            <span x-show="category.icon" x-text="category.icon" style="margin-right:4px"></span>
                            <span x-text="category.name"></span>
                        </h2>
                        <span class="cat-desc" x-show="category.description" x-text="category.description"></span>
                    </div>

                    @include('public-catalog.templates.' . $template)
                </section>
            </template>
            {{-- Footer comentado temporalmente: se reabrirá luego con otra lógica de negocios --}}
            {{-- <p class="footer-note" x-text="'Precios en ' + payload.store.currency + ' · El pedido se confirma por WhatsApp con la tienda.'"></p> --}}
        </main>

    </div> {{-- .mobile-frame --}}

    {{-- Hoja de producto (Modal / Bottom Sheet) --}}
    <div x-show="modalProduct !== null" class="overlay" role="dialog" aria-modal="true" aria-labelledby="modal-product-title"
         x-transition.opacity @click.self="closeModal()" @keydown.escape.window="closeModal()">
        <template x-if="modalProduct !== null">
            <div class="sheet">
                <div class="sheet-img">
                    <img :src="modalProduct.image_url || placeholder" :alt="modalProduct.name" decoding="async" x-on:error="if ($el.src !== placeholder) $el.src = placeholder">
                    <span x-show="modalProduct.badge" class="pbadge" x-text="modalProduct.badge"></span>
                    <button type="button" class="sheet-close" @click="closeModal()" aria-label="Cerrar">&times;</button>
                </div>
                <div class="sheet-body">
                    <h2 id="modal-product-title" x-text="modalProduct.name"></h2>
                    <p class="pdesc" x-text="modalProduct.description || ''"></p>
                    <div style="display:flex;align-items:baseline;margin:8px 0 14px">
                        <span class="sheet-price" style="margin:0" x-text="money(modalProduct.price_cents)"></span>
                        <span x-show="modalProduct.compare_at_price_cents" class="compare-price" style="font-size:14px" x-text="money(modalProduct.compare_at_price_cents)"></span>
                    </div>

                    <template x-for="group in modalProduct.option_groups" :key="group.id">
                        <fieldset class="optgroup" :class="{remove: group.is_removal}">
                            <legend>
                                <span x-text="group.name"></span>
                                <span x-show="group.is_required" class="req">obligatorio</span>
                                <span x-show="group.is_removal" class="freenote">sin costo</span>
                                <span x-show="!group.is_removal && group.selection_type === 'multiple' && group.max_selections" class="maxnote" x-text="'máx. ' + group.max_selections"></span>
                            </legend>
                            <template x-for="option in group.options" :key="option.id">
                                <label class="opt" :class="{checked: isSelected(group, option.id)}">
                                    <input :type="group.selection_type === 'single' ? 'radio' : 'checkbox'"
                                           :name="'group-' + group.id"
                                           :checked="isSelected(group, option.id)"
                                           :disabled="isOptionDisabled(group, option.id)"
                                           @change="toggleOption(group, option.id)">
                                    <span class="oname" x-text="option.name"></span>
                                    <span x-show="!group.is_removal && option.price_cents > 0" class="oprice" x-text="'+' + money(option.price_cents)"></span>
                                    <span x-show="group.is_removal" class="oprice free">se quita</span>
                                </label>
                            </template>
                        </fieldset>
                    </template>

                    <div class="qty-row">
                        <span class="lbl">Cantidad</span>
                        <div class="stepper">
                            <button type="button" @click="modalQty = Math.max(1, modalQty - 1)" aria-label="Menos">−</button>
                            <span class="n" x-text="modalQty"></span>
                            <button type="button" @click="modalQty = Math.min(MAX_QTY, modalQty + 1)" aria-label="Más">+</button>
                        </div>
                    </div>

                    <div class="note">
                        <label>Nota (opcional)<input type="text" x-model="modalNote" maxlength="140" placeholder="Ej: sin cebolla, aderezo aparte"></label>
                    </div>

                    <button type="button" class="add-main" @click="addToCart()" :disabled="!canAddModal()"
                            x-text="'Agregar · ' + money(modalLineCents())"></button>
                </div>
            </div>
        </template>
    </div>

    {{-- Barra inferior flotante del pedido --}}
    <div x-show="cart.length > 0 && !cartOpen" class="cartbar-floating" x-transition>
        <div class="cartbar-card">
            <div class="cartbar-pill">
                <span class="count-badge" x-text="itemCount"></span>
                <div class="cartbar-subtotal">
                    <span class="lbl">Subtotal pedido</span>
                    <span class="val" x-text="money(totalCents)"></span>
                </div>
            </div>
            <button type="button" class="cartbar-cta" @click="cartOpen = true">
                <span>Ver pedido</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Panel del pedido y confirmación por WhatsApp --}}
    <div x-show="cartOpen" class="cartpanel" role="dialog" aria-modal="true" aria-labelledby="cart-panel-title"
         x-transition.opacity @click.self="cartOpen = false" @keydown.escape.window="cartOpen = false">
        <div class="cartbox">
            <h2 id="cart-panel-title">Tu pedido <button type="button" @click="cartOpen = false" aria-label="Cerrar">&times;</button></h2>

            <template x-for="item in cart" :key="item.key">
                <div class="citem">
                    <div class="top">
                        <span x-text="productOf(item).name"></span>
                        <span x-text="money(lineCents(item))"></span>
                    </div>
                    <div class="opts">
                        <template x-for="line in optionLines(item)" :key="line.text">
                            <span :class="line.kind" x-text="line.text"></span>
                        </template>
                    </div>
                    <p x-show="item.note" class="cnote" x-text="'Nota: ' + item.note"></p>
                    <div class="row2">
                        <button type="button" class="mini" @click="changeQty(item, -1)" aria-label="Menos">−</button>
                        <span class="qty" x-text="item.qty"></span>
                        <button type="button" class="mini" @click="changeQty(item, 1)" aria-label="Más">+</button>
                        <button type="button" class="del" @click="removeItem(item)">Eliminar</button>
                    </div>
                </div>
            </template>
            <p x-show="cart.length === 0" class="empty-cart">Tu pedido está vacío.<br>Agrega productos del catálogo.</p>

            <label class="note-lbl" for="general-order-note">Nota general (opcional)</label>
            <textarea id="general-order-note" class="general-note" x-model="generalNote" @input="persist()" maxlength="300" rows="2" placeholder="Ej: entregar con cambio para $20"></textarea>

            <div class="totalrow"><span>Total estimado</span><span x-text="money(totalCents)"></span></div>

            <button type="button" class="wa-send" @click="send()">Enviar pedido por WhatsApp</button>
            <p x-show="sent" class="sent-note">¿Ya lo enviaste? Puedes vaciar el pedido cuando quieras.</p>
            <button type="button" class="clearbtn" @click="clearCart()">Vaciar pedido</button>
            <div x-show="sent" class="msg-preview" x-text="buildMessage()"></div>
        </div>
    </div>
</div>
@endsection
