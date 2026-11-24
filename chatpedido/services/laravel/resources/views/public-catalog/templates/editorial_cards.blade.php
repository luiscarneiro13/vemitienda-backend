{{-- Template: Feed Editorial / Gourmet (editorial_cards) --}}
<div class="plist-editorial">
    <template x-for="product in category.products" :key="product.id">
        <article class="pcard-editorial" :class="{'soldout': !product.is_available}"
                 x-show="isProductVisible(product)"
                 @click="product.is_available && openProduct(product)">
            <div class="hero-img">
                <img :src="product.image_url || placeholder" :alt="product.name" width="600" height="360" loading="lazy" decoding="async" x-on:error="if ($el.src !== placeholder) $el.src = placeholder">
                <span x-show="product.badge" class="pbadge" x-text="product.badge"></span>
                <span x-show="!product.is_available" class="soldout-badge">Agotado</span>
            </div>
            <div class="content">
                <h3 x-text="product.name"></h3>
                <p class="desc" x-show="product.description" x-text="product.description"></p>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;gap:12px">
                    <div class="price-group">
                        <span class="price font-bold" style="font-size:16px;font-weight:700" x-text="money(product.price_cents)"></span>
                        <span x-show="product.compare_at_price_cents" class="compare-price" style="font-size:13px" x-text="money(product.compare_at_price_cents)"></span>
                    </div>
                    <button x-show="product.is_available" type="button"
                            @click.stop="openProduct(product)"
                            class="pcard-add-btn" :aria-label="'Agregar ' + product.name">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </article>
    </template>
</div>
