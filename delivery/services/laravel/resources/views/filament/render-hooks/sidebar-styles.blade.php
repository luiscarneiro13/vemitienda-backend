<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --vmt-ink: #1a162b;
        --vmt-muted: #665f74;
        --vmt-purple: #6135bc;
        --vmt-purple-hover: #4c269c;
        --vmt-lilac: #eee8fb;
        --vmt-paper: #faf9fc;
        --vmt-line: #e3deec;
        --vmt-orange: #ff6b00;
        --vmt-green: #10b981;
        --vmt-display: "Space Grotesk", sans-serif;
        --vmt-body: "DM Sans", sans-serif;
    }

    /* Tipografía y fondo general del panel (super_admin y store_owner) */
    .fi-body {
        font-family: var(--vmt-body) !important;
        background-color: var(--vmt-paper);
        color: var(--vmt-ink);
    }

    .dark .fi-body {
        background-color: #171422;
        color: #faf9fc;
    }

    /* Logo ChatPedido (en login, topbar y sidebar) */
    .fi-logo {
        height: auto !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    .vmt-brand-logo, .cp-brand-logo {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: var(--vmt-display);
        font-weight: 700;
        font-size: 24px;
        line-height: 1;
        letter-spacing: -1px;
        color: var(--vmt-ink);
        user-select: none;
    }

    .dark .vmt-brand-logo, .dark .cp-brand-logo {
        color: #ffffff;
    }

    .vmt-brand-mark, .cp-brand-mark {
        width: 35px;
        height: 35px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .vmt-brand-mark svg, .cp-brand-mark svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .vmt-brand-accent, .cp-brand-accent {
        color: var(--vmt-purple);
    }

    .dark .vmt-brand-accent, .dark .cp-brand-accent {
        color: #a78bfa;
    }

    .vmt-brand-dot, .cp-brand-dot {
        color: var(--vmt-green);
    }

    /* Títulos y encabezados con Space Grotesk como en la web principal */
    .fi-header-heading,
    .fi-simple-header-heading,
    .fi-section-header-heading,
    .fi-wi-stats-overview-stat-value,
    .fi-modal-heading {
        font-family: var(--vmt-display) !important;
        letter-spacing: -0.03em;
    }

    /* Pantalla de Inicio de Sesión (fi-simple-layout) con estética ChatPedido */
    .fi-simple-layout {
        background:
            radial-gradient(circle at 15% 15%, rgba(238, 232, 251, 0.9) 0%, transparent 45%),
            radial-gradient(circle at 85% 85%, rgba(16, 185, 129, 0.15) 0%, transparent 45%),
            var(--vmt-paper);
    }

    .dark .fi-simple-layout {
        background:
            radial-gradient(circle at 15% 15%, rgba(97, 53, 188, 0.22) 0%, transparent 45%),
            radial-gradient(circle at 85% 85%, rgba(16, 185, 129, 0.08) 0%, transparent 45%),
            #171422;
    }

    .fi-simple-main {
        border-radius: 20px !important;
        border: 1px solid var(--vmt-line) !important;
        box-shadow: 0 16px 40px rgba(36, 32, 52, 0.07) !important;
    }

    .dark .fi-simple-main {
        border-color: #2e293d !important;
        background-color: #242034 !important;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    }

    .fi-simple-header .vmt-brand-logo {
        font-size: 28px;
    }

    .fi-simple-header .vmt-brand-mark {
        width: 40px;
        height: 40px;
    }

    /* Barra superior y menú lateral del panel */
    .fi-topbar {
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--vmt-line);
        box-shadow: none !important;
    }

    .dark .fi-topbar {
        background: rgba(36, 32, 52, 0.94) !important;
        border-bottom-color: #2e293d;
    }

    .fi-sidebar {
        background-color: #ffffff !important;
        border-right: 1px solid var(--vmt-line) !important;
    }

    .dark .fi-sidebar {
        background-color: #1d192b !important;
        border-right-color: #2e293d !important;
    }

    .fi-sidebar-item-btn {
        border-radius: 10px !important;
        transition: background-color 0.15s ease, color 0.15s ease !important;
    }

    .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn:hover {
        background-color: rgba(238, 232, 251, 0.55) !important;
    }

    .dark .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn:hover {
        background-color: rgba(97, 53, 188, 0.15) !important;
    }

    /* Ítem activo del menú lateral con fondo lila y texto/icono violeta de ChatPedido */
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
        background-color: var(--vmt-lilac) !important;
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-icon {
        color: var(--vmt-purple) !important;
        font-weight: 600 !important;
    }

    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
        background-color: rgba(97, 53, 188, 0.28) !important;
    }

    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label,
    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-icon {
        color: #ded2f7 !important;
    }

    /* Inputs y Selects con borde --vmt-line y focus ring violeta */
    .fi-input-wrp {
        border-radius: 10px !important;
        transition: box-shadow 0.15s ease, border-color 0.15s ease !important;
    }

    .fi-input-wrp:focus-within {
        box-shadow: 0 0 0 3px rgba(97, 53, 188, 0.18) !important;
    }

    /* Botones con radio de 10px, hover suave, press feedback y focus-visible accesible */
    .fi-btn {
        border-radius: 10px !important;
        font-weight: 600 !important;
        transition: background-color 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease !important;
    }

    .fi-btn:active:not(:disabled) {
        transform: translateY(1px);
    }

    .fi-btn:focus-visible {
        outline: 2px solid var(--vmt-purple) !important;
        outline-offset: 2px !important;
    }

    /* Badges con radio tipo píldora suave y peso tipográfico */
    .fi-badge {
        border-radius: 8px !important;
        font-weight: 600 !important;
        letter-spacing: -0.01em;
    }

    /* Encabezados de tabla con fondo papel lila suave */
    .fi-ta-header-cell {
        background-color: var(--vmt-paper);
        color: var(--vmt-muted);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .dark .fi-ta-header-cell {
        background-color: rgba(36, 32, 52, 0.6);
        color: #c5bed2;
    }

    /* Tarjetas, secciones, tablas y modales alineados al borde --line y radio de la web principal */
    .fi-section:not(.fi-section-not-contained):not(.fi-aside),
    .fi-ta-ctn,
    .fi-wi-stats-overview-stat {
        border-radius: 16px !important;
        border: 1px solid var(--vmt-line);
        box-shadow: 0 4px 16px rgba(36, 32, 52, 0.03) !important;
    }

    .dark .fi-section:not(.fi-section-not-contained):not(.fi-aside),
    .dark .fi-ta-ctn,
    .dark .fi-wi-stats-overview-stat {
        border-color: #2e293d;
    }

    .fi-modal-window {
        border-radius: 18px !important;
        border: 1px solid var(--vmt-line);
    }

    .dark .fi-modal-window {
        border-color: #2e293d;
        background-color: #242034 !important;
    }

    /* Grid equilibrado de 5 columnas en pantallas XL para StatsOverview */
    @media (min-width: 1280px) {
        .fi-wi-stats-overview-stats-ctn {
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        }
    }

    /* Reduce el espacio vertical entre grupos e items del menú lateral. */
    .fi-sidebar-nav,
    .fi-sidebar-nav-groups {
        row-gap: calc(var(--spacing) * 3) !important;
    }

    .fi-sidebar-group {
        row-gap: calc(var(--spacing) * 0.5) !important;
    }
</style>
