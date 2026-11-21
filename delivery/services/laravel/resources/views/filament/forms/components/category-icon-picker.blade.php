<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $statePath = $getStatePath();
        $groupedIcons = $getGroupedIcons();
    @endphp

    <style>
        .cat-icon-card {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            transition: all 0.15s ease;
        }

        .dark .cat-icon-card {
            background: #18181b;
            border-color: #27272a;
        }

        .cat-icon-card-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1 1 240px;
        }

        .cat-icon-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            min-width: 44px;
            min-height: 44px;
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            background: #f9fafb;
            font-size: 22px;
            line-height: 1;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .cat-icon-badge.has-icon {
            border-color: #7c3aed;
            background: #f5f3ff;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.15);
        }

        .dark .cat-icon-badge {
            background: #27272a;
            border-color: #3f3f46;
        }

        .dark .cat-icon-badge.has-icon {
            border-color: #a78bfa;
            background: rgba(124, 58, 237, 0.2);
        }

        .cat-icon-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
            flex: 1;
        }

        .cat-icon-title {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.35;
            word-break: break-word;
        }

        .dark .cat-icon-title {
            color: #f3f4f6;
        }

        .cat-icon-desc {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.35;
        }

        .dark .cat-icon-desc {
            color: #9ca3af;
        }

        .cat-icon-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .cat-icon-btn-open {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #7c3aed;
            background: #7c3aed;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(124, 58, 237, 0.2);
        }

        .cat-icon-btn-open:hover {
            background: #6d28d9;
            border-color: #6d28d9;
            transform: translateY(-1px);
        }

        .cat-icon-btn-clear {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #ef4444;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .cat-icon-btn-clear:hover {
            background: #fef2f2;
            border-color: #fca5a5;
        }

        .dark .cat-icon-btn-clear {
            background: #27272a;
            border-color: #3f3f46;
            color: #f87171;
        }

        .dark .cat-icon-btn-clear:hover {
            background: rgba(239, 68, 68, 0.15);
        }

        /* Modal Overlay & Container */
        .cat-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .cat-modal-window {
            position: relative;
            width: 100%;
            max-width: 620px;
            max-height: 90vh;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: catModalIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dark .cat-modal-window {
            background: #18181b;
            border-color: #27272a;
            color: #f3f4f6;
        }

        @keyframes catModalIn {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(8px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .cat-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
        }

        .dark .cat-modal-header {
            border-bottom-color: #27272a;
        }

        .cat-modal-header-text h3 {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .dark .cat-modal-header-text h3 {
            color: #f9fafb;
        }

        .cat-modal-header-text p {
            font-size: 12px;
            color: #6b7280;
            margin: 2px 0 0 0;
        }

        .dark .cat-modal-header-text p {
            color: #9ca3af;
        }

        .cat-modal-close-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 16px;
            line-height: 1;
        }

        .cat-modal-close-btn:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .dark .cat-modal-close-btn {
            background: #27272a;
            border-color: #3f3f46;
            color: #9ca3af;
        }

        .dark .cat-modal-close-btn:hover {
            background: #3f3f46;
            color: #ffffff;
        }

        .cat-modal-body {
            padding: 16px 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Barra de entrada y preview dentro del modal */
        .cat-modal-input-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .dark .cat-modal-input-row {
            background: #27272a;
            border-color: #3f3f46;
        }

        .cat-modal-preview-big {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            min-width: 52px;
            min-height: 52px;
            border-radius: 12px;
            background: #ffffff;
            border: 2px solid #7c3aed;
            font-size: 26px;
            line-height: 1;
            box-shadow: 0 2px 4px rgba(124, 58, 237, 0.15);
        }

        .dark .cat-modal-preview-big {
            background: #18181b;
            border-color: #a78bfa;
        }

        .cat-modal-input-wrap {
            position: relative;
            flex: 1;
        }

        .cat-modal-input {
            width: 100%;
            height: 42px;
            padding: 0 34px 0 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            font-size: 14px;
            color: #111827;
            transition: all 0.15s ease;
        }

        .cat-modal-input:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        }

        .dark .cat-modal-input {
            background: #18181b;
            border-color: #3f3f46;
            color: #f3f4f6;
        }

        .cat-modal-input-clear {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 50%;
        }

        .cat-modal-input-clear:hover {
            color: #4b5563;
        }

        /* Pestañas de categoría */
        .cat-modal-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            overflow-y: hidden;
            padding: 8px 4px 12px 4px;
            margin: 4px 0 8px 0;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            -webkit-overflow-scrolling: touch;
        }

        .cat-modal-tabs::-webkit-scrollbar {
            height: 5px;
        }

        .cat-modal-tabs::-webkit-scrollbar-track {
            background: transparent;
        }

        .cat-modal-tabs::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 9999px;
        }

        .cat-modal-tabs::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .dark .cat-modal-tabs {
            scrollbar-color: #3f3f46 transparent;
        }

        .dark .cat-modal-tabs::-webkit-scrollbar-thumb {
            background: #3f3f46;
        }

        .cat-modal-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            height: 34px;
            padding: 0 14px;
            border-radius: 9999px;
            font-size: 12.5px;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #4b5563;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.15s ease;
        }

        .cat-modal-tab-btn:hover {
            background: #f9fafb;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .cat-modal-tab-btn.is-active {
            background: #7c3aed;
            border-color: #7c3aed;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(124, 58, 237, 0.25);
        }

        .dark .cat-modal-tab-btn {
            background: #27272a;
            border-color: #3f3f46;
            color: #d1d5db;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .dark .cat-modal-tab-btn:hover {
            background: #3f3f46;
            border-color: #52525b;
        }

        .dark .cat-modal-tab-btn.is-active {
            background: #7c3aed;
            border-color: #7c3aed;
            color: #ffffff;
        }

        /* Lista de Emojis */
        .cat-modal-grid-container {
            max-height: 290px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding-right: 4px;
        }

        .cat-modal-group-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .dark .cat-modal-group-title {
            color: #9ca3af;
        }

        .cat-modal-icon-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .cat-modal-icon-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            font-size: 19px;
            line-height: 1;
            cursor: pointer;
            transition: all 0.12s ease;
        }

        .cat-modal-icon-btn:hover {
            background: #f3f4f6;
            border-color: #cbd5e1;
            transform: scale(1.12);
            z-index: 2;
        }

        .cat-modal-icon-btn.is-selected {
            border-color: #7c3aed;
            background: #f5f3ff;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.3);
            transform: scale(1.08);
        }

        .dark .cat-modal-icon-btn {
            background: #27272a;
            border-color: #3f3f46;
        }

        .dark .cat-modal-icon-btn:hover {
            background: #3f3f46;
        }

        .dark .cat-modal-icon-btn.is-selected {
            border-color: #a78bfa;
            background: rgba(124, 58, 237, 0.25);
        }

        /* Footer del modal */
        .cat-modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid #f3f4f6;
            background: #f9fafb;
        }

        .dark .cat-modal-footer {
            background: #27272a;
            border-top-color: #3f3f46;
        }

        .cat-modal-footer-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cat-modal-btn-cancel {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #374151;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .cat-modal-btn-cancel:hover {
            background: #f3f4f6;
        }

        .dark .cat-modal-btn-cancel {
            background: #18181b;
            border-color: #3f3f46;
            color: #d1d5db;
        }

        .cat-modal-btn-apply {
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #7c3aed;
            background: #7c3aed;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(124, 58, 237, 0.2);
        }

        .cat-modal-btn-apply:hover {
            background: #6d28d9;
            border-color: #6d28d9;
        }

        .cat-modal-btn-remove {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            border: 1px dashed #d1d5db;
            background: transparent;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .cat-modal-btn-remove:hover {
            color: #ef4444;
            border-color: #f87171;
            background: #fef2f2;
        }

        .dark .cat-modal-btn-remove {
            border-color: #3f3f46;
            color: #9ca3af;
        }

        .dark .cat-modal-btn-remove:hover {
            color: #f87171;
            background: rgba(239, 68, 68, 0.1);
        }

        /* Adaptación Responsiva para Móvil */
        @media (max-width: 640px) {
            .cat-icon-card {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                padding: 12px;
            }

            .cat-icon-card-left {
                width: 100%;
                flex: none;
            }

            .cat-icon-info {
                width: 100%;
            }

            .cat-icon-actions {
                width: 100%;
                display: flex;
                gap: 8px;
            }

            .cat-icon-btn-open {
                flex: 1;
                justify-content: center;
                padding: 10px 14px;
            }

            .cat-icon-btn-clear {
                justify-content: center;
                padding: 10px 12px;
            }

            .cat-modal-backdrop {
                padding: 8px;
            }

            .cat-modal-window {
                max-height: 94vh;
                border-radius: 14px;
            }

            .cat-modal-header {
                padding: 12px 14px;
            }

            .cat-modal-body {
                padding: 12px 14px;
                gap: 12px;
            }

            .cat-modal-input-row {
                padding: 8px 10px;
                gap: 8px;
            }

            .cat-modal-preview-big {
                width: 44px;
                height: 44px;
                min-width: 44px;
                min-height: 44px;
                font-size: 22px;
            }

            .cat-modal-tabs {
                gap: 6px;
                padding: 6px 2px 10px 2px;
                margin: 2px 0 6px 0;
            }

            .cat-modal-tab-btn {
                height: 32px;
                padding: 0 12px;
                font-size: 12px;
            }

            .cat-modal-footer {
                padding: 12px 14px;
                flex-direction: column-reverse;
                gap: 10px;
                align-items: stretch;
            }

            .cat-modal-footer-right {
                width: 100%;
                display: flex;
                gap: 8px;
            }

            .cat-modal-btn-cancel,
            .cat-modal-btn-apply {
                flex: 1;
                text-align: center;
                justify-content: center;
            }

            .cat-modal-btn-remove {
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <div
        x-data="{
            state: $wire.entangle('{{ $statePath }}'),
            isOpen: false,
            draftIcon: '',
            activeTab: 'all',
            groupedIcons: @js($groupedIcons),

            openModal() {
                this.draftIcon = this.state || '';
                this.activeTab = 'all';
                this.isOpen = true;
                // Prevenir scroll en el fondo mientras el modal está abierto
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.isOpen = false;
                document.body.style.overflow = '';
            },

            applyIcon() {
                this.state = this.draftIcon ? this.draftIcon.trim() : null;
                this.closeModal();
            },

            clearDirect() {
                this.state = null;
            },

            clearDraft() {
                this.draftIcon = '';
            },

            selectDraft(icon) {
                this.draftIcon = icon;
            },

            isTabVisible(groupName) {
                if (this.activeTab === 'all') return true;
                return this.activeTab === groupName;
            }
        }"
        class="w-full"
    >
        {{-- Tarjeta Principal en el Formulario (UI/UX Limpia y Clara) --}}
        <div class="cat-icon-card">
            <div class="cat-icon-card-left">
                <div
                    class="cat-icon-badge"
                    :class="state ? 'has-icon' : ''"
                    title="Ícono seleccionado para la categoría"
                >
                    <span x-show="state" x-text="state"></span>
                    <span x-show="!state" style="color: #9ca3af; font-size: 16px;">📁</span>
                </div>

                <div class="cat-icon-info">
                    <span class="cat-icon-title" x-text="state ? 'Ícono asignado: ' + state : 'Sin ícono asignado'"></span>
                    <span class="cat-icon-desc">
                        Aparecerá en el catálogo web y en las pestañas de navegación.
                    </span>
                </div>
            </div>

            <div class="cat-icon-actions">
                <button
                    type="button"
                    @click="openModal()"
                    class="cat-icon-btn-open"
                >
                    <span>🎨</span>
                    <span x-text="state ? 'Cambiar ícono' : 'Elegir ícono o emoji'"></span>
                </button>

                <button
                    x-show="state"
                    @click="clearDirect()"
                    type="button"
                    class="cat-icon-btn-clear"
                    title="Quitar ícono de la categoría"
                >
                    ✕ Quitar
                </button>
            </div>
        </div>

        {{-- MODAL (No se cierra al hacer clic fuera, solo por acción explícita) --}}
        <template x-teleport="body">
            <div
                x-show="isOpen"
                x-cloak
                class="cat-modal-backdrop"
                aria-modal="true"
                role="dialog"
            >
                {{-- Contenedor del Modal: @click.stop evita propagación y NO se cierra por hacer clic en el backdrop ni fuera --}}
                <div
                    @click.stop
                    class="cat-modal-window"
                >
                    {{-- Cabecera del Modal --}}
                    <div class="cat-modal-header">
                        <div class="cat-modal-header-text">
                            <h3>Seleccionar Ícono o Emoji</h3>
                            <p>Elige un ícono de los rubros recomendados o escribe cualquier emoji desde tu teclado.</p>
                        </div>
                        <button
                            type="button"
                            @click="closeModal()"
                            class="cat-modal-close-btn"
                            title="Cerrar ventana"
                        >
                            ✕
                        </button>
                    </div>

                    {{-- Cuerpo del Modal --}}
                    <div class="cat-modal-body">
                        {{-- Entrada manual y vista previa destacada --}}
                        <div class="cat-modal-input-row">
                            <div class="cat-modal-preview-big" title="Vista previa del ícono seleccionado">
                                <span x-show="draftIcon" x-text="draftIcon"></span>
                                <span x-show="!draftIcon" style="color:#9ca3af; font-size:14px; user-select:none;">—</span>
                            </div>

                            <div class="cat-modal-input-wrap">
                                <input
                                    type="text"
                                    x-model="draftIcon"
                                    placeholder="Escribe o pega cualquier emoji aquí..."
                                    maxlength="50"
                                    class="cat-modal-input"
                                />
                                <button
                                    x-show="draftIcon"
                                    type="button"
                                    @click="clearDraft()"
                                    class="cat-modal-input-clear"
                                    title="Borrar texto"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>

                        {{-- Pestañas por Rubro / Categoría Comercial --}}
                        <div class="cat-modal-tabs">
                            <button
                                type="button"
                                @click="activeTab = 'all'"
                                class="cat-modal-tab-btn"
                                :class="activeTab === 'all' ? 'is-active' : ''"
                            >
                                🌐 Todas las secciones
                            </button>

                            <template x-for="(icons, groupName) in groupedIcons" :key="groupName">
                                <button
                                    type="button"
                                    @click="activeTab = groupName"
                                    class="cat-modal-tab-btn"
                                    :class="activeTab === groupName ? 'is-active' : ''"
                                    x-text="groupName"
                                >
                                </button>
                            </template>
                        </div>

                        {{-- Cuadrícula de Íconos Organizados --}}
                        <div class="cat-modal-grid-container">
                            <template x-for="(icons, groupName) in groupedIcons" :key="groupName">
                                <div
                                    x-show="isTabVisible(groupName)"
                                    style="display: flex; flex-direction: column; gap: 4px;"
                                >
                                    <span class="cat-modal-group-title" x-text="groupName"></span>
                                    <div class="cat-modal-icon-grid">
                                        <template x-for="icon in icons" :key="icon">
                                            <button
                                                type="button"
                                                @click="selectDraft(icon)"
                                                class="cat-modal-icon-btn"
                                                :class="draftIcon === icon ? 'is-selected' : ''"
                                                :title="icon"
                                            >
                                                <span x-text="icon"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Footer del Modal --}}
                    <div class="cat-modal-footer">
                        <div>
                            <button
                                type="button"
                                @click="clearDraft()"
                                class="cat-modal-btn-remove"
                            >
                                Limpiar selección
                            </button>
                        </div>

                        <div class="cat-modal-footer-right">
                            <button
                                type="button"
                                @click="closeModal()"
                                class="cat-modal-btn-cancel"
                            >
                                Cancelar
                            </button>

                            <button
                                type="button"
                                @click="applyIcon()"
                                class="cat-modal-btn-apply"
                            >
                                Aplicar ícono
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-dynamic-component>
