{{-- Featured Products Carousel --}}
<section x-show="payload.settings?.show_featured_carousel && payload.featured_products && payload.featured_products.length > 0 && !searchQuery" class="featured-section" x-cloak>
    <div class="featured-title">
        <span aria-hidden="true">⭐</span>
        <span>Los más pedidos</span>
    </div>
    <div class="featured-slider">
        <template x-for="product in payload.featured_products" :key="'feat-' + product.id">
            <article class="featured-card" :class="{'soldout': !product.is_available}" @click="product.is_available && openProduct(product)">
                <div class="fimg">
                    <img :src="product.image_url || placeholder" :alt="product.name" width="240" height="160" loading="lazy" decoding="async" x-on:error="if ($el.src !== placeholder) $el.src = placeholder">
                    <span x-show="product.badge" class="pbadge" x-text="product.badge"></span>
                    <span x-show="!product.is_available" class="soldout-badge">Agotado</span>
                </div>
                <div class="fbody">
                    <h4 class="fname" x-text="product.name"></h4>
                    <div class="prow" style="margin-top:6px">
                        <div class="price-group">
                            <span class="price font-bold" style="font-size:13px;font-weight:700" x-text="money(product.price_cents)"></span>
                            <span x-show="product.compare_at_price_cents" class="compare-price" x-text="money(product.compare_at_price_cents)"></span>
                        </div>
                        <button x-show="product.is_available" type="button" @click.stop="openProduct(product)" class="pcard-add-btn" :aria-label="'Agregar ' + product.name">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </article>
        </template>
    </div>
</section>
