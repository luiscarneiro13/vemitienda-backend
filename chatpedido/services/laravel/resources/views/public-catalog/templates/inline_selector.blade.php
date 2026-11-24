{{-- Template: Opciones Inline Modernas (inline_selector) --}}
<div class="plist-inline">
    <template x-for="product in category.products" :key="product.id">
        <article class="pcard-inline" :class="{'soldout': !product.is_available}"
                 x-show="isProductVisible(product)"
                 @click="product.is_available && openProduct(product)">
            <div style="display:flex;gap:12px;align-items:center">
                <div style="position:relative;width:80px;height:80px;border-radius:12px;overflow:hidden;flex-shrink:0;background:var(--cat-paper)">
                    <img :src="product.image_url || placeholder" :alt="product.name" width="80" height="80" loading="lazy" decoding="async" style="width:100%;height:100%;object-fit:cover" x-on:error="if ($el.src !== placeholder) $el.src = placeholder">
                    <span x-show="!product.is_available" class="soldout-badge">Agotado</span>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                        <h3 style="font-size:14px;font-weight:700;margin:0" x-text="product.name"></h3>
                        <span x-show="product.badge" class="pbadge" style="position:static;display:inline-block;padding:1px 5px;font-size:9px" x-text="product.badge"></span>
                    </div>
                    <p class="desc" style="font-size:11px;color:var(--cat-muted);margin:2px 0 4px" x-show="product.description" x-text="product.description"></p>
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
            </div>
            {{-- Quick Options preview chips if product has options --}}
            <template x-if="product.option_groups && product.option_groups.length > 0">
                <div style="display:flex;gap:6px;overflow-x:auto;margin-top:8px;padding-top:6px;border-top:1px dashed var(--cat-line)">
                    <template x-for="grp in product.option_groups" :key="grp.id">
                        <span style="font-size:10px;font-weight:600;background:var(--cat-paper);padding:2px 6px;border-radius:6px;white-space:nowrap;color:var(--cat-muted)" x-text="grp.name + (grp.is_required ? ' *' : '')"></span>
                    </template>
                </div>
            </template>
        </article>
    </template>
</div>
