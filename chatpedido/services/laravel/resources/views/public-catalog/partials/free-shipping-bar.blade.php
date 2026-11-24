{{-- Free Shipping Progress Bar --}}
<div x-show="payload.settings?.show_free_shipping_bar && payload.settings?.free_shipping_threshold > 0" class="free-shipping-bar" x-cloak>
    <div class="flex items-center justify-between" style="display:flex;align-items:center;justify-content:space-between">
        <span class="flex items-center gap-1.5" style="display:flex;align-items:center;gap:6px">
            <span style="font-size:14px">🛵</span>
            <span x-text="isFreeShippingUnlocked ? '¡Tienes envío gratis en este pedido!' : 'Te faltan ' + money(freeShippingRemainingCents) + ' para envío gratis'"></span>
        </span>
        <span style="font-size:11px;color:var(--cat-muted)" x-text="money(totalCents) + ' / ' + money(freeShippingThresholdCents)"></span>
    </div>
    <div class="free-shipping-track">
        <div class="free-shipping-fill" :style="'width: ' + freeShippingPercent + '%'"></div>
    </div>
</div>
