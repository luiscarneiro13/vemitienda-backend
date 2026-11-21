const MAX_QTY = 99;
const ITEM_NOTE_MAX = 140;
const GENERAL_NOTE_MAX = 300;

export default (payload, placeholder) => ({
    payload,
    placeholder: placeholder || '/product-placeholder.svg',
    MAX_QTY,
    index: {},
    activeCategory: null,
    cart: [],
    generalNote: '',
    modalProduct: null,
    modalSelections: {},
    modalQty: 1,
    modalNote: '',
    cartOpen: false,
    sent: false,
    categoryDropdownOpen: false,
    searchQuery: '',

    init() {
        this.payload.categories.forEach((category) => {
            category.products.forEach((product) => {
                product._search = `${product.name || ''} ${product.description || ''}`.toLowerCase();
                this.index[product.id] = product;
            });
        });
        this.activeCategory = this.payload.categories[0]?.id ?? null;
        this.restore();
        if (typeof window !== 'undefined' && 'IntersectionObserver' in window) {
            setTimeout(() => this.observeSections(), 100);
        }
    },

    isProductVisible(product) {
        if (!this.searchQuery.trim()) return true;
        const q = this.searchQuery.toLowerCase().trim();
        if (typeof product._search === 'string') {
            return product._search.includes(q);
        }
        return (product.name && product.name.toLowerCase().includes(q)) ||
               (product.description && product.description.toLowerCase().includes(q));
    },

    hasCategoryVisible(category) {
        return category.products.some((p) => this.isProductVisible(p));
    },

    hasAnyProductVisible() {
        return this.payload.categories.some((c) => this.hasCategoryVisible(c));
    },

    observeSections() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const id = parseInt(entry.target.id.replace('cat-', ''), 10);
                    if (id) {
                        this.activeCategory = id;
                    }
                }
            });
        }, { rootMargin: '-100px 0px -60% 0px' });
        document.querySelectorAll('.cat-section').forEach((el) => observer.observe(el));
    },

    get activeCategoryName() {
        return this.payload.categories.find((c) => c.id === this.activeCategory)?.name || '';
    },

    scrollToCategory(id) {
        this.activeCategory = id;
        this.categoryDropdownOpen = false;
        document.getElementById('cat-' + id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    get storageKey() {
        return `catalog-cart:${this.payload.branch.uuid}`;
    },

    get itemCount() {
        return this.cart.reduce((sum, item) => sum + item.qty, 0);
    },

    get totalCents() {
        return this.cart.reduce((sum, item) => sum + this.lineCents(item), 0);
    },

    get freeShippingThresholdCents() {
        return Math.round((parseFloat(this.payload?.settings?.free_shipping_threshold) || 20) * 100);
    },

    get freeShippingRemainingCents() {
        return Math.max(0, this.freeShippingThresholdCents - this.totalCents);
    },

    get freeShippingPercent() {
        if (this.freeShippingThresholdCents <= 0) return 100;
        return Math.min(100, Math.round((this.totalCents / this.freeShippingThresholdCents) * 100));
    },

    get isFreeShippingUnlocked() {
        return this.totalCents >= this.freeShippingThresholdCents;
    },

    copiedCoupon: false,
    copyCoupon(code) {
        if (!code) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(() => {
                this.copiedCoupon = true;
                setTimeout(() => { this.copiedCoupon = false; }, 2000);
            });
        }
    },

    // ---------- Persistencia ----------
    restore() {
        try {
            const saved = JSON.parse(localStorage.getItem(this.storageKey) || '{}');
            this.generalNote = typeof saved.generalNote === 'string' ? saved.generalNote.slice(0, GENERAL_NOTE_MAX) : '';
            this.cart = (Array.isArray(saved.cart) ? saved.cart : []).filter((item) => this.isValidItem(item));
        } catch (error) {
            this.cart = [];
            this.generalNote = '';
        }
        this.persist();
    },

    persist() {
        localStorage.setItem(this.storageKey, JSON.stringify({ cart: this.cart, generalNote: this.generalNote }));
    },

    isValidItem(item) {
        const product = this.index[item?.productId];
        if (!product || !product.is_available) return false;
        if (!Number.isInteger(item.qty) || item.qty < 1 || item.qty > MAX_QTY) return false;
        if (!Array.isArray(item.optionIds) || typeof item.key !== 'string') return false;
        const validIds = product.option_groups.flatMap((group) => group.options.map((option) => option.id));
        if (!item.optionIds.every((id) => validIds.includes(id))) return false;
        return this.selectionsAreValid(product, this.groupSelections(product, item.optionIds));
    },

    // ---------- Reglas de opciones ----------
    groupSelections(product, optionIds) {
        const selections = {};
        product.option_groups.forEach((group) => {
            selections[group.id] = group.options.filter((o) => optionIds.includes(o.id)).map((o) => o.id);
        });
        return selections;
    },

    selectionsAreValid(product, selections) {
        return product.option_groups.every((group) => {
            const count = (selections[group.id] || []).length;
            if (group.is_required && count < 1) return false;
            if (group.selection_type === 'single' && count > 1) return false;
            if (group.selection_type === 'multiple' && group.max_selections && count > group.max_selections) return false;
            return true;
        });
    },

    // ---------- Modal ----------
    openProduct(product) {
        if (!product.is_available) return;
        this.modalSelections = {};
        product.option_groups.forEach((group) => {
            this.modalSelections[group.id] = [];
        });
        this.modalQty = 1;
        this.modalNote = '';
        this.modalProduct = product;
    },

    closeModal() {
        this.modalProduct = null;
    },

    isSelected(group, optionId) {
        return (this.modalSelections[group.id] || []).includes(optionId);
    },

    isOptionDisabled(group, optionId) {
        if (group.selection_type !== 'multiple' || !group.max_selections) return false;
        const current = this.modalSelections[group.id] || [];
        return current.length >= group.max_selections && !current.includes(optionId);
    },

    toggleOption(group, optionId) {
        const current = this.modalSelections[group.id] || [];
        if (group.selection_type === 'single') {
            this.modalSelections[group.id] = [optionId];
            return;
        }
        if (current.includes(optionId)) {
            this.modalSelections[group.id] = current.filter((id) => id !== optionId);
            return;
        }
        if (group.max_selections && current.length >= group.max_selections) return;
        this.modalSelections[group.id] = [...current, optionId];
    },

    modalOptionIds() {
        return Object.values(this.modalSelections).flat().sort((a, b) => a - b);
    },

    canAddModal() {
        return this.modalProduct !== null && this.selectionsAreValid(this.modalProduct, this.modalSelections);
    },

    modalLineCents() {
        if (this.modalProduct === null) return 0;
        return this.unitCents(this.modalProduct, this.modalOptionIds()) * this.modalQty;
    },

    // ---------- Carrito ----------
    cleanNote(text, max) {
        return String(text || '').replace(/\s+/g, ' ').trim().slice(0, max);
    },

    addToCart() {
        if (!this.canAddModal()) return;
        const optionIds = this.modalOptionIds();
        const note = this.cleanNote(this.modalNote, ITEM_NOTE_MAX);
        const key = `${this.modalProduct.id}|${optionIds.join(',')}|${note}`;
        const existing = this.cart.find((item) => item.key === key);
        if (existing) {
            existing.qty = Math.min(MAX_QTY, existing.qty + this.modalQty);
        } else {
            this.cart.push({ key, productId: this.modalProduct.id, qty: this.modalQty, optionIds, note });
        }
        this.sent = false;
        this.persist();
        this.closeModal();
    },

    changeQty(item, delta) {
        const qty = item.qty + delta;
        if (qty < 1) {
            this.removeItem(item);
            return;
        }
        item.qty = Math.min(MAX_QTY, qty);
        this.persist();
    },

    removeItem(item) {
        this.cart = this.cart.filter((entry) => entry.key !== item.key);
        if (this.cart.length === 0) this.cartOpen = false;
        this.persist();
    },

    clearCart() {
        if (!window.confirm('¿Vaciar el pedido?')) return;
        this.cart = [];
        this.generalNote = '';
        this.sent = false;
        this.cartOpen = false;
        this.persist();
    },

    // ---------- Precios ----------
    productOf(item) {
        return this.index[item.productId];
    },

    unitCents(product, optionIds) {
        const extras = product.option_groups
            .filter((group) => !group.is_removal)
            .flatMap((group) => group.options)
            .filter((option) => optionIds.includes(option.id))
            .reduce((sum, option) => sum + option.price_cents, 0);
        return product.price_cents + extras;
    },

    lineCents(item) {
        return this.unitCents(this.productOf(item), item.optionIds) * item.qty;
    },

    money(cents) {
        try {
            return new Intl.NumberFormat('es-VE', { style: 'currency', currency: this.payload.store.currency }).format(cents / 100);
        } catch (error) {
            return (cents / 100).toFixed(2);
        }
    },

    optionLines(item) {
        const product = this.productOf(item);
        return product.option_groups
            .map((group) => {
                const chosen = group.options.filter((option) => item.optionIds.includes(option.id));
                if (chosen.length === 0) return null;
                if (group.is_removal) {
                    const names = chosen.map((option) => option.name.replace(/^sin\s+/i, ''));
                    return { text: `Sin: ${names.join(', ')}`, kind: 'rm' };
                }
                const labels = chosen.map((option) => (option.price_cents > 0 ? `${option.name} (+${this.money(option.price_cents)})` : option.name));
                const paid = chosen.some((option) => option.price_cents > 0);
                return { text: `${group.name}: ${labels.join(', ')}`, kind: paid ? 'ad' : '' };
            })
            .filter((line) => line !== null);
    },

    // ---------- WhatsApp ----------
    buildMessage() {
        const { store, branch } = this.payload;
        const lines = [
            `*Nuevo pedido* — ${store.name}`,
            `Sucursal: ${branch.name}${branch.city ? ` — ${branch.city}` : ''}`,
            '',
        ];
        this.cart.forEach((item, position) => {
            const product = this.productOf(item);
            lines.push(`${position + 1}) ${item.qty} x ${product.name} — ${this.money(this.lineCents(item))}`);
            this.optionLines(item).forEach((line) => lines.push(`   • ${line.text}`));
            if (item.note) lines.push(`   Nota: ${item.note}`);
        });
        lines.push('', `*Total estimado: ${this.money(this.totalCents)}*`);
        const generalNote = this.cleanNote(this.generalNote, GENERAL_NOTE_MAX);
        if (generalNote) lines.push('', `Nota general: ${generalNote}`);
        lines.push('', '_Pedido generado desde el catálogo web. La tienda confirmará el monto final._');
        return lines.join('\n');
    },

    whatsappUrl() {
        return `https://wa.me/${this.payload.branch.whatsapp}?text=${encodeURIComponent(this.buildMessage())}`;
    },

    trackOrder() {
        try {
            if (!this.payload.track_order_url) return;
            const body = JSON.stringify({
                total_cents: this.totalCents,
                items_count: this.itemCount
            });
            if (navigator.sendBeacon) {
                const blob = new Blob([body], { type: 'application/json' });
                navigator.sendBeacon(this.payload.track_order_url, blob);
            } else {
                fetch(this.payload.track_order_url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: body,
                    keepalive: true
                }).catch(() => {});
            }
        } catch (e) {
            // Silencioso para garantizar que no interrumpa el envío por WhatsApp
        }
    },

    send() {
        if (this.cart.length === 0) return;
        this.trackOrder();
        window.open(this.whatsappUrl(), '_blank', 'noopener');
        this.sent = true;
    },
});
