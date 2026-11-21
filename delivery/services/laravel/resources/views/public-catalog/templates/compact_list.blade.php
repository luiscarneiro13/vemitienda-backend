{{-- Template: Lista Compacta Express (compact_list) --}}
<div class="plist-compact">
    <template x-for="product in category.products" :key="product.id">
        <article class="pcard-compact" :class="{'soldout': !product.is_available}"
                 x-show="isProductVisible(product)"
                 @click="product.is_available && openProduct(product)">
            <div class="thumb">
                <img :src="product.image_url || placeholder" :alt="product.name" width="120" height="120" loading="lazy" decoding="async" x-on:error="if ($el.src !== placeholder) $el.src = placeholder">
                <span x-show="!product.is_available" class="soldout-badge">Agotado</span>
            </div>
            <div class="info">
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                    <h3 x-text="product.name"></h3>
                    <span x-show="product.badge" class="pbadge" style="position:static;display:inline-block;padding:1px 5px;font-size:9px" x-text="product.badge"></span>
                </div>
                <p class="desc" x-show="product.description" x-text="product.description"></p>
                <div class="price-group">
                    <span class="price font-bold" style="font-weight:700" x-text="money(product.price_cents)"></span>
                    <span x-show="product.compare_at_price_cents" class="compare-price" x-text="money(product.compare_at_price_cents)"></span>
                </div>
            </div>
            <button x-show="product.is_available" type="button"
                    @click.stop="openProduct(product)"
                    class="pcard-add-btn" :aria-label="'Agregar ' + product.name">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </article>
    </template>
</div>
