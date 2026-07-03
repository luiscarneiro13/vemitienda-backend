<div id="templates-modal-overlay" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" onclick="if (event.target === this) closeTemplatesModal()">
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm"></div>

    <div class="tpl-acrylic relative w-full max-w-md max-h-[85vh] flex flex-col rounded-xl overflow-hidden">
        {{-- Header --}}
        <div class="flex justify-between items-center px-4 py-3 border-b border-outline-variant/40">
            <h3 class="text-headline-section text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">dashboard_customize</span>
                Plantillas
            </h3>
            <button type="button"
                class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-full bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors"
                onclick="closeTemplatesModal()">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- ── Vista: listado de plantillas ── --}}
        <div id="templates-list-view" class="flex-1 flex flex-col overflow-hidden">
            <div class="px-4 pt-3 pb-2 flex items-center gap-3">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/60 text-[18px]">search</span>
                    <input id="templates-search" type="text" placeholder="Buscar plantilla..."
                        class="w-full pl-9 pr-3 py-2 text-body-md border border-outline-variant rounded-lg bg-surface-container-low"
                        style="outline:none" oninput="filterTemplates(this.value)">
                </div>
                <button type="button"
                    class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-on-surface text-surface rounded-full shadow-md hover:scale-105 active:scale-95 transition-all duration-200"
                    onclick="createNewTemplate()">
                    <span class="material-symbols-outlined font-bold">add</span>
                </button>
            </div>

            <div class="flex-1 max-h-[500px] overflow-y-auto px-4 pb-4 tpl-scrollbar">
                <div id="templates-list" class="grid grid-cols-1 gap-3"></div>
                <p id="templates-empty" class="hidden text-body-md text-on-surface-variant text-center py-8">No se encontraron plantillas.</p>
            </div>

            <div class="px-4 py-2 border-t border-outline-variant/40 bg-surface-container-low/50">
                <span id="templates-count" class="text-label-sm text-on-surface-variant/70">0 PLANTILLAS DISPONIBLES</span>
            </div>
        </div>

        {{-- ── Vista: detalle de la plantilla seleccionada ── --}}
        <div id="templates-detail-view" class="hidden flex-1 flex flex-col overflow-hidden">
            <div class="px-4 pt-3 pb-2 flex items-center gap-2">
                <button type="button"
                    class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-full bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors"
                    onclick="showTemplatesList()">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                </button>
                <div class="min-w-0 flex-1">
                    <input type="text" id="template-detail-title" readonly maxlength="150"
                        class="tpl-title-input w-full text-label-bold text-on-surface truncate"
                        style="outline:none"
                        placeholder="Título de la plantilla">
                    <p id="template-detail-meta" class="text-label-sm text-on-surface-variant/70"></p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-4 pb-2">
                <label class="text-label-bold text-on-surface-variant mb-2 block">Contenido de la plantilla</label>
                <textarea id="template-detail-textarea" readonly
                    class="w-full h-40 p-3 text-body-md border border-outline-variant rounded-lg resize-none bg-surface-container-low text-on-surface-variant"
                    style="outline:none"></textarea>
            </div>

            <div class="px-4 py-3 border-t border-outline-variant/40 bg-surface-container-low/50 flex items-center justify-between gap-2">
                <div>
                    <button type="button" id="template-delete-btn"
                        class="hidden px-3 py-1.5 rounded-lg text-button-text border border-error text-error hover:bg-error/10 transition-colors flex items-center gap-1"
                        onclick="deleteTemplate()">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                        Eliminar
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <div id="template-view-actions" class="flex items-center gap-2">
                        <button type="button" id="template-edit-btn"
                            class="px-3 py-1.5 rounded-lg text-button-text border border-outline-variant text-on-surface-variant hover:bg-surface-container-low transition-colors"
                            onclick="enableTemplateEdit()">
                            Editar
                        </button>
                        <button type="button"
                            class="px-3 py-1.5 rounded-lg text-button-text bg-primary text-on-primary hover:opacity-90 transition-opacity"
                            onclick="useTemplate()">
                            Usar
                        </button>
                    </div>
                    <button type="button" id="template-save-btn"
                        class="hidden px-3 py-1.5 rounded-lg text-button-text bg-primary text-on-primary hover:opacity-90 transition-opacity"
                        onclick="saveTemplateEdit()">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tpl-acrylic {
        background-color: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px) saturate(120%);
        -webkit-backdrop-filter: blur(20px) saturate(120%);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
    }

    .tpl-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
    }

    .tpl-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .tpl-scrollbar::-webkit-scrollbar { width: 6px; }
    .tpl-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .tpl-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.15); border-radius: 10px; }

    #template-detail-textarea:not([readonly]) {
        background-color: #ffffff;
        border-color: #003d9b;
    }

    .tpl-title-input {
        border: 1px solid transparent;
        background: transparent;
        padding: 2px 0;
        border-radius: 6px;
    }

    .tpl-title-input:not([readonly]) {
        background-color: #ffffff;
        border-color: #003d9b;
        padding: 2px 8px;
    }
</style>

<script>
    function openTemplatesModal() {
        showTemplatesList();
        loadTemplates();
        document.getElementById('templates-modal-overlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeTemplatesModal() {
        document.getElementById('templates-modal-overlay').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function tplCsrfHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        };
    }

    function buildTemplateCard(template) {
        var card = document.createElement('button');
        card.type = 'button';
        card.className = 'tpl-card min-h-[88px] text-left bg-surface-container-lowest hover:bg-surface-container-low p-3 rounded-lg relative overflow-hidden flex flex-col gap-1 border border-outline-variant/60 transition-all';

        var title = template.title || '';
        var description = template.description || '';
        var color = template.color || 'primary';

        card.dataset.id = template.id;
        card.dataset.title = title;
        card.dataset.search = (title + ' ' + description).toLowerCase();
        card.dataset.description = description;
        card.dataset.prompt = template.prompt || '';
        card.dataset.tone = template.tone || '';
        card.dataset.objective = template.objective || '';
        card.dataset.audience = template.audience || '';
        card.dataset.color = color;
        card.dataset.editable = template.editable ? '1' : '0';

        var colorBar = document.createElement('span');
        colorBar.className = 'absolute left-0 top-0 bottom-0 w-1 bg-' + color;
        card.appendChild(colorBar);

        var titleEl = document.createElement('span');
        titleEl.className = 'text-label-bold text-on-surface';
        titleEl.textContent = title;
        card.appendChild(titleEl);

        var descEl = document.createElement('span');
        descEl.className = 'text-body-md text-on-surface-variant line-clamp-2';
        descEl.textContent = description;
        card.appendChild(descEl);

        var metaEl = document.createElement('span');
        metaEl.className = 'mt-1 text-label-sm text-on-surface-variant/70 self-end';
        metaEl.textContent = (template.tone || '') + ' · ' + (template.objective || '');
        card.appendChild(metaEl);

        card.addEventListener('click', function () {
            selectTemplate(card);
        });

        return card;
    }

    async function loadTemplates() {
        var list = document.getElementById('templates-list');
        var countLabel = document.getElementById('templates-count');
        list.innerHTML = '';

        try {
            var response = await fetch('/admin/blog-templates', {
                headers: { 'Accept': 'application/json' },
            });

            if (!response.ok) {
                throw new Error('request failed');
            }

            var data = await response.json();
            var templates = data.templates || [];

            templates.forEach(function (template) {
                list.appendChild(buildTemplateCard(template));
            });

            countLabel.textContent = templates.length + ' PLANTILLAS DISPONIBLES';
            filterTemplates(document.getElementById('templates-search').value || '');
        } catch (error) {
            toastr.error('No se pudieron cargar las plantillas.');
            countLabel.textContent = '0 PLANTILLAS DISPONIBLES';
            document.getElementById('templates-empty').classList.remove('hidden');
        }
    }

    function filterTemplates(query) {
        var q = query.trim().toLowerCase();
        var cards = document.querySelectorAll('#templates-list .tpl-card');
        var visibleCount = 0;

        cards.forEach(function (card) {
            var matches = !q || card.dataset.search.indexOf(q) !== -1;
            card.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        document.getElementById('templates-empty').classList.toggle('hidden', visibleCount > 0);
    }

    function showTemplatesList() {
        document.getElementById('templates-detail-view').classList.add('hidden');
        document.getElementById('templates-list-view').classList.remove('hidden');
    }

    function updateDeleteButtonVisibility() {
        var detailView = document.getElementById('templates-detail-view');
        var deleteBtn = document.getElementById('template-delete-btn');
        var canDelete = detailView.dataset.editable === '1' && !!detailView.dataset.id;
        deleteBtn.classList.toggle('hidden', !canDelete);
    }

    function selectTemplate(card) {
        var detailView = document.getElementById('templates-detail-view');
        var titleInput = document.getElementById('template-detail-title');
        var textarea = document.getElementById('template-detail-textarea');

        detailView.dataset.id = card.dataset.id;
        detailView.dataset.editable = card.dataset.editable;
        detailView.dataset.color = card.dataset.color || 'primary';
        detailView.dataset.tone = card.dataset.tone;
        detailView.dataset.objective = card.dataset.objective;
        detailView.dataset.audience = card.dataset.audience;

        titleInput.value = card.dataset.title;
        titleInput.readOnly = true;
        titleInput.classList.remove('hidden');
        document.getElementById('template-detail-meta').textContent = card.dataset.tone + ' · ' + card.dataset.objective;

        textarea.value = card.dataset.prompt;
        textarea.readOnly = true;

        document.getElementById('template-view-actions').classList.remove('hidden');
        document.getElementById('template-save-btn').classList.add('hidden');
        updateDeleteButtonVisibility();

        document.getElementById('templates-list-view').classList.add('hidden');
        detailView.classList.remove('hidden');
    }

    function createNewTemplate() {
        var detailView = document.getElementById('templates-detail-view');
        var titleInput = document.getElementById('template-detail-title');
        var textarea = document.getElementById('template-detail-textarea');

        detailView.dataset.id = '';
        detailView.dataset.editable = '0';
        detailView.dataset.color = 'primary';
        detailView.dataset.tone = '';
        detailView.dataset.objective = '';
        detailView.dataset.audience = '';

        titleInput.value = '';
        titleInput.readOnly = false;
        titleInput.classList.add('hidden');
        document.getElementById('template-detail-meta').textContent = 'Escribe tu propio contenido';

        textarea.value = '';
        textarea.readOnly = false;

        document.getElementById('template-view-actions').classList.add('hidden');
        document.getElementById('template-save-btn').classList.remove('hidden');
        updateDeleteButtonVisibility();

        document.getElementById('templates-list-view').classList.add('hidden');
        detailView.classList.remove('hidden');

        textarea.focus();
    }

    function enableTemplateEdit() {
        var titleInput = document.getElementById('template-detail-title');
        var textarea = document.getElementById('template-detail-textarea');

        titleInput.readOnly = false;
        textarea.readOnly = false;
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);

        document.getElementById('template-view-actions').classList.add('hidden');
        document.getElementById('template-save-btn').classList.remove('hidden');
    }

    function deriveTitleFromContent(content) {
        var firstLine = (content || '').trim().split('\n')[0].trim();
        if (!firstLine) return 'Plantilla personalizada';
        return firstLine.length > 60 ? firstLine.slice(0, 60) + '…' : firstLine;
    }

    async function saveTemplateEdit() {
        var detailView = document.getElementById('templates-detail-view');
        var titleInput = document.getElementById('template-detail-title');
        var textarea = document.getElementById('template-detail-textarea');
        var saveBtn = document.getElementById('template-save-btn');

        if (saveBtn.disabled) return; // evita doble envío si el usuario hace doble clic

        var titleValue = titleInput.classList.contains('hidden') || !titleInput.value.trim()
            ? deriveTitleFromContent(textarea.value)
            : titleInput.value.trim();

        var body = {
            title: titleValue,
            description: (textarea.value || '').split('\n')[0].slice(0, 255),
            prompt: textarea.value,
            tone: detailView.dataset.tone || null,
            objective: detailView.dataset.objective || null,
            audience: detailView.dataset.audience || null,
            color: detailView.dataset.color || 'primary',
        };

        var isUpdate = !!detailView.dataset.id && detailView.dataset.editable === '1';
        var url = isUpdate ? ('/admin/blog-templates/' + detailView.dataset.id) : '/admin/blog-templates';
        var method = isUpdate ? 'PUT' : 'POST';

        var originalLabel = saveBtn.textContent;
        saveBtn.disabled = true;
        saveBtn.textContent = 'Guardando...';

        try {
            var response = await fetch(url, {
                method: method,
                headers: tplCsrfHeaders(),
                body: JSON.stringify(body),
            });

            var data = await response.json();

            if (!response.ok) {
                toastr.error(data.message || 'No se pudo guardar la plantilla.');
                return;
            }

            var template = data.template || {};
            detailView.dataset.id = template.id;
            detailView.dataset.editable = '1';
            detailView.dataset.color = template.color || detailView.dataset.color || 'primary';

            titleInput.value = template.title || titleValue;
            titleInput.readOnly = true;
            titleInput.classList.remove('hidden');
            textarea.readOnly = true;

            document.getElementById('template-detail-meta').textContent =
                (detailView.dataset.tone || 'Sin tono') + ' · ' + (detailView.dataset.objective || 'Sin objetivo');

            document.getElementById('template-save-btn').classList.add('hidden');
            document.getElementById('template-view-actions').classList.remove('hidden');
            updateDeleteButtonVisibility();

            loadTemplates(); // refresca el listado en segundo plano (sin salir de esta vista de detalle)

            toastr.success('Plantilla guardada.');
        } catch (error) {
            toastr.error('No se pudo guardar la plantilla.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = originalLabel;
        }
    }

    async function deleteTemplate() {
        var detailView = document.getElementById('templates-detail-view');

        if (!confirm('¿Eliminar esta plantilla? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            var response = await fetch('/admin/blog-templates/' + detailView.dataset.id, {
                method: 'DELETE',
                headers: tplCsrfHeaders(),
            });

            var data = await response.json();

            if (!response.ok) {
                toastr.error(data.message || 'No se pudo eliminar la plantilla.');
                return;
            }

            toastr.success('Plantilla eliminada.');
            showTemplatesList();
            loadTemplates();
        } catch (error) {
            toastr.error('No se pudo eliminar la plantilla.');
        }
    }

    function useTemplate() {
        var detailView = document.getElementById('templates-detail-view');
        var textarea = document.getElementById('template-detail-textarea');

        var promptField = document.getElementById('ai-prompt');
        var toneField = document.getElementById('ai-tone');
        var objectiveField = document.getElementById('ai-objective');
        var audienceField = document.getElementById('ai-audience');

        if (promptField) {
            promptField.value = textarea.value;
            var counter = document.getElementById('prompt-count');
            if (counter) counter.textContent = promptField.value.length + '/5000';
        }
        if (toneField) toneField.value = detailView.dataset.tone;
        if (objectiveField) objectiveField.value = detailView.dataset.objective;
        if (audienceField) audienceField.value = detailView.dataset.audience;

        closeTemplatesModal();
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeTemplatesModal();
    });
</script>
