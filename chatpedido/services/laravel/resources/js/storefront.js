// La navegación, búsqueda y paginación funcionan también sin JavaScript.
const replaceFailedImage = (image) => {
    if (image.dataset.fallbackApplied) return;
    image.dataset.fallbackApplied = 'true';
    image.src = image.dataset.fallback;
};

const bindImageFallbacks = (root) => {
    root.querySelectorAll('img[data-fallback]').forEach((image) => {
        if (image.dataset.fallbackBound) return;
        image.dataset.fallbackBound = 'true';
        image.addEventListener('error', () => replaceFailedImage(image));
        if (image.complete && image.naturalWidth === 0) replaceFailedImage(image);
    });
};

bindImageFallbacks(document);

// ---------- Scroll infinito del directorio ----------
const initInfiniteScroll = () => {
    const grid = document.querySelector('.directory .grid');
    const pagination = document.querySelector('.pagination');
    const nextLink = pagination?.querySelector('a[rel="next"]');
    if (!grid || !pagination || !nextLink || !('IntersectionObserver' in window)) return;

    const total = parseInt(grid.dataset.total || '0', 10);
    const resultsCount = document.querySelector('.results-count');
    let nextUrl = nextLink.href;
    let loading = false;

    // Con JS el usuario no necesita la paginación clásica.
    pagination.style.display = 'none';

    const sentinel = document.createElement('div');
    sentinel.className = 'scroll-sentinel';
    sentinel.setAttribute('aria-hidden', 'true');
    sentinel.textContent = 'Cargando más tiendas…';
    grid.after(sentinel);

    const observer = new IntersectionObserver(async ([entry]) => {
        if (!entry.isIntersecting || loading || !nextUrl) return;
        loading = true;
        sentinel.classList.add('loading');

        try {
            const response = await fetch(nextUrl, { headers: { 'X-Requested-With': 'fetch' } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');

            doc.querySelectorAll('.grid > *').forEach((card) => {
                const node = document.importNode(card, true);
                grid.appendChild(node);
            });
            bindImageFallbacks(grid);

            nextUrl = doc.querySelector('.pagination a[rel="next"]')?.href || null;
            if (resultsCount && total) {
                resultsCount.textContent = `Mostrando ${grid.children.length} de ${total} tiendas`;
            }
            if (!nextUrl) {
                observer.disconnect();
                sentinel.remove();
            }
        } catch {
            // Si falla la carga automática se restaura la paginación clásica.
            observer.disconnect();
            sentinel.remove();
            pagination.style.display = '';
        }
        loading = false;
        sentinel.classList.remove('loading');
    }, { rootMargin: '400px 0px' });

    observer.observe(sentinel);
};

initInfiniteScroll();

// ---------- Cierre del dropdown de categorías ----------
const initCategoryDropdown = () => {
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('category-dropdown');
        if (dropdown && dropdown.open && !dropdown.contains(e.target)) {
            dropdown.removeAttribute('open');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const dropdown = document.getElementById('category-dropdown');
            if (dropdown && dropdown.open) {
                dropdown.removeAttribute('open');
                dropdown.querySelector('summary')?.focus();
            }
        }
    });
};

initCategoryDropdown();
