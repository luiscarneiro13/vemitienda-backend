{{-- Promo Banner --}}
<div x-show="payload.settings?.show_promo_banner && payload.settings?.promo_title" class="promo-banner" x-cloak>
    <div class="promo-content">
        <h4 x-text="payload.settings.promo_title"></h4>
        <p x-show="payload.settings?.promo_subtitle" x-text="payload.settings.promo_subtitle"></p>
    </div>
    <template x-if="payload.settings?.promo_code">
        <button type="button" class="promo-btn" @click="copyCoupon(payload.settings.promo_code)">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
            </svg>
            <span x-text="copiedCoupon ? '¡Copiado!' : payload.settings.promo_code"></span>
        </button>
    </template>
</div>
