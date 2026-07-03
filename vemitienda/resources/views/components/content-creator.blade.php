@props(['categories' => [], 'tags' => [], 'status' => []])

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-gutter">

    {{-- ── Row 1, Col 1: Describe tu contenido ── --}}
    <div class="lg:col-span-4">
        <section class="space-y-6 h-full" data-purpose="content-description">
            <div class="bg-surface-container-lowest p-4 lg:p-6 rounded-xl border border-outline-variant shadow-sm h-full flex flex-col">
                <h3 class="text-headline-section text-on-surface mb-4 lg:mb-6 flex items-center gap-2">
                    <span class="hidden lg:flex items-center justify-center w-6 h-6 rounded-full bg-primary text-on-primary text-xs font-bold">1</span>
                    <span class="lg:hidden bg-tertiary-container text-on-tertiary p-1.5 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1">auto_awesome</span>
                    </span>
                    Describe tu contenido
                </h3>
                <div class="space-y-4 flex-1">
                    <div>
                        <label class="text-label-bold text-on-surface-variant mb-2 block">
                            Cuéntale a la IA sobre qué quieres publicar
                        </label>
                        <div class="relative">
                            <textarea id="ai-prompt"
                                class="w-full h-24 lg:h-56 p-4 text-body-md border border-outline-variant rounded-lg resize-none bg-surface-container-low"
                                style="outline:none;font-family:'Hanken Grotesk',sans-serif"
                                placeholder="Escribe aquí..."
                                maxlength="5000"
                                oninput="document.getElementById('prompt-count').textContent=this.value.length+'/5000'"></textarea>
                            <span class="absolute bottom-3 right-3 text-label-sm text-outline" id="prompt-count">0/5000</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 lg:grid-cols-1 lg:gap-4">
                        <div>
                            <label class="text-label-bold text-on-surface-variant mb-2 block">Tono de voz</label>
                            <select id="ai-tone" class="w-full text-body-md border border-outline-variant rounded-lg bg-surface-container-low"
                                style="padding:10px 14px;outline:none;font-family:'Hanken Grotesk',sans-serif">
                                <option>Profesional</option>
                                <option>Informal</option>
                                <option>Entusiasta</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-label-bold text-on-surface-variant mb-2 block">Objetivo</label>
                            <select id="ai-objective" class="w-full text-body-md border border-outline-variant rounded-lg bg-surface-container-low"
                                style="padding:10px 14px;outline:none;font-family:'Hanken Grotesk',sans-serif">
                                <option>Educar</option>
                                <option>Vender</option>
                                <option>Engagement</option>
                            </select>
                        </div>
                        <div class="hidden lg:block">
                            <label class="text-label-bold text-on-surface-variant mb-2 block">Audiencia objetivo</label>
                            <input type="text" id="ai-audience"
                                class="w-full text-body-md border border-outline-variant rounded-lg bg-surface-container-low"
                                style="padding:10px 14px;outline:none;font-family:'Hanken Grotesk',sans-serif"
                                maxlength="100"
                                placeholder="Ej. Jóvenes 18-25 años">
                        </div>
                    </div>
                    <button type="button" id="generate-ai-btn" onclick="generateContent()"
                        class="w-full bg-primary hover:bg-primary-dark text-on-primary h-12 lg:h-auto lg:py-3 rounded-lg flex items-center justify-center gap-2 text-button-text transition-all mt-6 shadow-md active:scale-[0.98]"
                        style="border:none;cursor:pointer">
                        <span class="material-symbols-outlined text-lg" id="generate-ai-btn-icon">auto_awesome</span>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="generate-ai-btn-spinner" style="display:none"></span>
                        <span id="generate-ai-btn-label">Generar con IA</span>
                    </button>
                </div>
            </div>
        </section>
    </div>

    {{-- ── Row 1, Col 2: Edita tu contenido ── --}}
    <div class="lg:col-span-8">
        <section class="space-y-6 h-full" data-purpose="content-editor">
            <div class="bg-surface-container-lowest p-4 lg:p-6 rounded-xl border border-outline-variant shadow-sm h-full flex flex-col">
                <h3 class="text-headline-section text-on-surface mb-4 flex items-center gap-2">
                    <span class="hidden lg:flex items-center justify-center w-6 h-6 rounded-full bg-primary text-on-primary text-xs font-bold">2</span>
                    <span class="lg:hidden bg-primary-container text-on-primary p-1.5 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">edit_note</span>
                    </span>
                    Edita tu contenido
                </h3>

                {{-- Título del artículo --}}
                <div class="mb-4">
                    <label class="text-label-bold text-on-surface-variant mb-2 block">Título del artículo *</label>
                    <input type="text" name="name" id="post-title" required
                        value="{{ old('name') }}"
                        placeholder="Escribe el título aquí..."
                        class="w-full text-body-md border border-outline-variant rounded-lg bg-surface-container-low"
                        style="padding:10px 14px;outline:none;font-family:'Hanken Grotesk',sans-serif;font-weight:600">
                </div>

                {{-- Tabs --}}
                <div class="flex border-b border-outline-variant mb-6">
                    <button type="button" onclick="switchTab(this,'tab-texto')"
                        class="cc-tab px-4 py-2 text-button-text text-primary border-b-2 border-primary"
                        style="background:transparent;border-left:none;border-right:none;border-top:none;cursor:pointer">
                        Texto Principal
                    </button>
                    <button type="button" onclick="switchTab(this,'tab-variantes')"
                        class="cc-tab px-4 py-2 text-button-text text-on-surface-variant hover:text-on-surface border-b-2 border-transparent"
                        style="background:transparent;border-left:none;border-right:none;border-top:none;cursor:pointer">
                        Variantes
                    </button>
                    <button type="button" onclick="switchTab(this,'tab-media')"
                        class="cc-tab px-4 py-2 text-button-text text-on-surface-variant hover:text-on-surface border-b-2 border-transparent"
                        style="background:transparent;border-left:none;border-right:none;border-top:none;cursor:pointer">
                        Media
                    </button>
                </div>

                {{-- Tab: Texto Principal --}}
                <div id="tab-texto" class="space-y-6 flex-1 overflow-y-auto custom-scrollbar pr-1">
                    <div>
                        <p class="text-label-bold text-on-surface-variant mb-3">Texto del post</p>
                        <div class="border border-outline-variant rounded-lg overflow-hidden flex flex-col">
                            <div class="bg-surface-container-low border-b border-outline-variant p-2 flex items-center gap-1">
                                <button type="button" onclick="fmt('bold')"
                                    class="p-1.5 hover:bg-surface-variant rounded text-on-surface-variant"
                                    style="background:transparent;border:none;cursor:pointer">
                                    <span class="material-symbols-outlined text-sm">format_bold</span>
                                </button>
                                <button type="button" onclick="fmt('italic')"
                                    class="p-1.5 hover:bg-surface-variant rounded text-on-surface-variant"
                                    style="background:transparent;border:none;cursor:pointer">
                                    <span class="material-symbols-outlined text-sm">format_italic</span>
                                </button>
                                <div class="w-px h-4 bg-outline-variant mx-1"></div>
                                <button type="button" onclick="fmt('insertUnorderedList')"
                                    class="p-1.5 hover:bg-surface-variant rounded text-on-surface-variant"
                                    style="background:transparent;border:none;cursor:pointer">
                                    <span class="material-symbols-outlined text-sm">format_list_bulleted</span>
                                </button>
                                <button type="button"
                                    class="p-1.5 hover:bg-surface-variant rounded text-on-surface-variant"
                                    style="background:transparent;border:none;cursor:pointer">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                </button>
                                <button type="button"
                                    class="p-1.5 hover:bg-surface-variant rounded text-on-surface-variant"
                                    style="background:transparent;border:none;cursor:pointer">
                                    <span class="material-symbols-outlined text-sm">sentiment_satisfied</span>
                                </button>
                                <button type="button"
                                    class="ml-auto text-[10px] border border-outline-variant px-2 py-1 rounded bg-white hover:bg-surface-container-low"
                                    style="cursor:pointer">
                                    IA Rewrite
                                </button>
                            </div>
                            <div id="post-editor"
                                contenteditable="true"
                                class="p-4 text-body-md min-h-[160px] lg:min-h-[350px] bg-white leading-relaxed text-on-surface focus:outline-none"
                                style="font-family:'Hanken Grotesk',sans-serif"
                                oninput="syncBody();updateCharCount()">
                                {!! old('body') !!}
                            </div>
                            <textarea name="body" id="body-hidden" style="display:none">{{ old('body') }}</textarea>
                            <div class="bg-surface-container-low text-[10px] text-right p-1 px-3 text-outline border-t border-outline-variant">
                                <span id="char-count">0 caracteres</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Variantes --}}
                <div id="tab-variantes" style="display:none" class="flex-1">
                    <div class="border border-outline-variant rounded-lg p-4">
                        <p class="text-label-bold text-on-surface-variant mb-3">Etiquetas del artículo</p>
                        <select name="tags[]" class="select2" multiple style="width:100%;font-size:13px">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                    {{ $tag->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tab: Media --}}
                <div id="tab-media" style="display:none" class="flex-1">
                    <div>
                        <p class="text-label-bold text-on-surface-variant mb-3">Contenido Multimedia</p>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="relative group rounded-lg overflow-hidden border border-outline-variant aspect-video" id="media-current" style="display:none">
                                <img id="media-preview" class="w-full h-full object-cover" src="">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <label for="image-input" style="cursor:pointer" class="text-white text-xs font-bold flex items-center gap-1 bg-white/20 px-3 py-1.5 rounded-full backdrop-blur-sm">
                                        <span class="material-symbols-outlined text-sm">edit</span> Editar
                                    </label>
                                </div>
                            </div>
                            <label for="image-input"
                                id="upload-zone"
                                class="border-2 border-dashed border-outline-variant rounded-lg flex flex-col items-center justify-center p-4 text-center group cursor-pointer hover:border-primary hover:bg-primary/5 transition-all aspect-video">
                                <input type="file" name="image" id="image-input" accept="image/*" style="display:none" onchange="previewMedia(this)">
                                <span class="material-symbols-outlined text-primary mb-1">upload</span>
                                <p class="text-[11px] text-primary">Cambiar imagen</p>
                                <p class="text-[9px] text-outline mt-0.5">PNG, JPG hasta 10MB</p>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    {{-- ── Row 2, Col 3: Vista previa ── --}}
    <div class="lg:col-span-8">
        <div class="bg-surface-container-lowest p-4 lg:p-6 rounded-xl border border-outline-variant shadow-sm h-full" data-purpose="preview-card">
            <div class="flex items-center justify-between mb-4 lg:mb-6">
                <h3 class="text-headline-section text-on-surface flex items-center gap-2">
                    <span class="hidden lg:flex items-center justify-center w-6 h-6 rounded-full bg-primary text-on-primary text-xs font-bold">3</span>
                    <span class="lg:hidden bg-secondary-container text-on-secondary-container p-1.5 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                    </span>
                    Vista previa
                </h3>
                <div class="hidden lg:flex items-center gap-1 border border-outline-variant rounded-lg px-3 py-1.5 bg-surface-container-low text-label-sm cursor-pointer hover:bg-surface-variant transition-colors">
                    <span>LinkedIn Feed</span>
                    <span class="material-symbols-outlined text-xs">expand_more</span>
                </div>
            </div>

            {{-- Selector de redes --}}
            <div class="flex gap-3 mb-4 lg:mb-6 overflow-x-auto pb-2 custom-scrollbar no-scrollbar">
                <button type="button" onclick="selectNetwork(this)"
                    class="network-btn w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-lg shrink-0 outline outline-2 outline-offset-2 outline-blue-600"
                    style="border:none;cursor:pointer">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                </button>
                <button type="button" onclick="selectNetwork(this)"
                    class="network-btn w-10 h-10 rounded-lg text-white flex items-center justify-center shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                    style="background:linear-gradient(135deg,#f59e0b,#ec4899,#7c3aed);border:none;cursor:pointer">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8A3.6 3.6 0 0 0 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6A3.6 3.6 0 0 0 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/></svg>
                </button>
                <button type="button" onclick="selectNetwork(this)"
                    class="network-btn w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                    style="border:none;cursor:pointer">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.04c-5.5 0-10 4.49-10 10.02 0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 0 0 8.44-9.9c0-5.53-4.5-10.02-10-10.02z"/></svg>
                </button>
                <button type="button" onclick="selectNetwork(this)"
                    class="network-btn w-10 h-10 rounded-lg bg-black text-white flex items-center justify-center shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                    style="border:none;cursor:pointer">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </button>
            </div>

            {{-- Feed preview --}}
            <div class="border border-outline-variant rounded-xl overflow-hidden bg-white max-w-[420px] mx-auto shadow-md">
                <div class="p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-primary rounded flex items-center justify-center shadow-inner">
                            <span class="material-symbols-outlined text-white">store</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xs font-bold text-on-surface leading-none">Ve mi tienda</h4>
                            <p class="text-[10px] text-on-surface-variant">12,345 seguidores</p>
                            <p class="text-[9px] text-outline flex items-center gap-0.5">Ahora •
                                <span class="material-symbols-outlined text-[10px]">public</span>
                            </p>
                        </div>
                    </div>
                    <p class="text-[11px] text-on-surface leading-relaxed mb-3" id="preview-text">
                        El contenido del artículo aparecerá aquí mientras escribes...
                    </p>
                    <p class="text-[11px] text-blue-600 font-bold" id="preview-tags">#VemiTienda</p>
                </div>
                <div id="preview-img-wrap" style="display:none">
                    <img id="preview-img" class="w-full aspect-video object-cover" src="">
                </div>
                <div class="p-3 px-4 border-t border-outline-variant flex justify-between text-on-surface-variant">
                    <button type="button" class="flex items-center gap-1.5 text-[10px] hover:text-primary" style="background:transparent;border:none;cursor:pointer">
                        <span class="material-symbols-outlined text-sm">thumb_up</span> Recomendar
                    </button>
                    <button type="button" class="flex items-center gap-1.5 text-[10px] hover:text-primary" style="background:transparent;border:none;cursor:pointer">
                        <span class="material-symbols-outlined text-sm">chat_bubble</span> Comentar
                    </button>
                    <button type="button" class="flex items-center gap-1.5 text-[10px] hover:text-primary" style="background:transparent;border:none;cursor:pointer">
                        <span class="material-symbols-outlined text-sm">share</span> Compartir
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 2, Col 4: Programar publicación ── --}}
    <div class="lg:col-span-4">
        <div class="bg-surface-container-lowest p-4 lg:p-6 rounded-xl border border-outline-variant shadow-sm h-full" data-purpose="scheduler">
            <h3 class="text-headline-section text-on-surface mb-4 lg:mb-6 flex items-center gap-2">
                <span class="hidden lg:flex items-center justify-center w-6 h-6 rounded-full bg-primary text-on-primary text-xs font-bold">4</span>
                <span class="lg:hidden bg-primary text-on-primary p-1.5 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">schedule</span>
                </span>
                Programar publicación
            </h3>

            {{-- Mobile: toggle buttons Ahora / Después --}}
            <div class="grid grid-cols-2 gap-2 mb-4 lg:hidden">
                <label class="flex items-center justify-center gap-2 h-10 rounded-lg border border-outline-variant text-on-surface-variant font-label-bold text-label-sm cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary-container/10 has-[:checked]:text-primary">
                    <input type="radio" name="schedule" value="now" class="sr-only"
                        onchange="document.getElementById('form-status').value='2'">
                    <span class="material-symbols-outlined text-[18px]">bolt</span>
                    Ahora
                </label>
                <label class="flex items-center justify-center gap-2 h-10 rounded-lg border border-outline-variant text-on-surface-variant font-label-bold text-label-sm cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary-container/10 has-[:checked]:text-primary">
                    <input type="radio" name="schedule" value="scheduled" checked class="sr-only">
                    <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    Después
                </label>
            </div>

            <div class="space-y-2">
                <label class="hidden lg:flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-surface-container-low transition-colors border border-transparent hover:border-outline-variant">
                    <input type="radio" name="schedule" value="now" class="w-4 h-4 accent-primary"
                        onchange="document.getElementById('form-status').value='2'">
                    <span class="text-button-text text-on-surface group-hover:text-primary">Publicar ahora</span>
                </label>
                <div class="space-y-2">
                    <label class="hidden lg:flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-surface-container-low transition-colors border border-transparent hover:border-outline-variant">
                        <input type="radio" name="schedule" value="scheduled" checked class="w-4 h-4 accent-primary">
                        <span class="text-button-text text-on-surface group-hover:text-primary">Programar para después</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3 lg:pl-10 pr-3">
                        <div class="relative">
                            <input type="text" name="scheduled_date" placeholder="dd/mm/aaaa"
                                class="w-full text-label-sm p-2.5 border border-outline-variant rounded-lg bg-surface-container-low"
                                style="outline:none;font-family:'Hanken Grotesk',sans-serif;padding-right:2rem">
                            <span class="material-symbols-outlined absolute right-2.5 top-2 text-outline text-sm">calendar_month</span>
                        </div>
                        <div class="relative">
                            <input type="text" name="scheduled_time" placeholder="09:00 AM"
                                class="w-full text-label-sm p-2.5 border border-outline-variant rounded-lg bg-surface-container-low"
                                style="outline:none;font-family:'Hanken Grotesk',sans-serif;padding-right:2rem">
                            <span class="material-symbols-outlined absolute right-2.5 top-2 text-outline text-sm">schedule</span>
                        </div>
                    </div>
                </div>

                {{-- Categoría --}}
                <div class="pt-2">
                    <label class="text-label-bold text-on-surface-variant mb-2 block">Categoría</label>
                    <select name="category_id"
                        class="w-full text-body-md border border-outline-variant rounded-lg bg-surface-container-low"
                        style="padding:10px 14px;outline:none;font-family:'Hanken Grotesk',sans-serif">
                        <option value="">— Sin categoría —</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="status" id="form-status" value="1">

                <div class="pt-3 flex shadow-sm">
                    <button type="submit"
                        class="flex-1 bg-primary hover:bg-primary-dark text-on-primary h-12 lg:h-auto lg:py-2.5 rounded-xl lg:rounded-l-xl lg:rounded-r-none flex items-center justify-center gap-2 text-button-text transition-all lg:border-r lg:border-white/20"
                        style="border-top:none;border-bottom:none;border-left:none;cursor:pointer"
                        onclick="document.getElementById('form-status').value='2'">
                        <span class="material-symbols-outlined text-lg">calendar_today</span>
                        Programar Publicación
                    </button>
                    <button type="button"
                        class="hidden lg:flex bg-primary hover:bg-primary-dark text-on-primary px-2 rounded-r-xl transition-all border-l border-white/10"
                        style="border-top:none;border-right:none;border-bottom:none;cursor:pointer">
                        <span class="material-symbols-outlined text-lg">expand_more</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 3: Sugerencias IA ── --}}
    <section class="lg:col-span-12 pt-4 lg:pt-6 pb-8" data-purpose="ai-suggestions">
        <div class="flex items-center justify-between mb-4 lg:mb-6">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-2xl">auto_awesome</span>
                <h3 class="text-display-title text-on-surface">Sugerencias inteligentes de la IA</h3>
            </div>
            <button type="button"
                class="hidden lg:flex text-primary hover:bg-primary-container/10 p-2 rounded-full transition-all items-center gap-2"
                style="background:transparent;border:none;cursor:pointer">
                <span class="material-symbols-outlined text-xl">refresh</span>
                Actualizar sugerencias
            </button>
        </div>
        <div class="flex gap-4 overflow-x-auto no-scrollbar pb-2 snap-x -mx-4 px-4 lg:grid lg:grid-cols-3 lg:gap-6 lg:overflow-visible lg:mx-0 lg:px-0 lg:snap-none">
            <div class="snap-center shrink-0 w-[260px] lg:w-auto bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm flex items-start gap-5 hover:border-primary hover:shadow-md cursor-pointer transition-all group">
                <div class="w-12 h-12 rounded-xl bg-primary-fixed text-on-primary-fixed flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">insights</span>
                </div>
                <div>
                    <p class="text-headline-section text-on-surface mb-2">Agregar cifra de impacto</p>
                    <p class="text-body-md text-on-surface-variant leading-relaxed">La IA sugiere agregar datos estadísticos para mayor impacto. "El 85% de las fallas críticas son prevenibles".</p>
                    <button type="button" class="mt-4 text-primary text-label-bold flex items-center gap-1 group-hover:translate-x-1 transition-transform" style="background:transparent;border:none;cursor:pointer">
                        Aplicar sugerencia <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </button>
                </div>
            </div>
            <div class="snap-center shrink-0 w-[260px] lg:w-auto bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm flex items-start gap-5 hover:border-tertiary hover:shadow-md cursor-pointer transition-all group">
                <div class="w-12 h-12 rounded-xl bg-tertiary-fixed text-on-tertiary-fixed flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">campaign</span>
                </div>
                <div>
                    <p class="text-headline-section text-on-surface mb-2">Incluir llamado a la acción</p>
                    <p class="text-body-md text-on-surface-variant leading-relaxed">La IA sugiere un CTA más directo: "Solicita tu auditoría de mantenimiento gratuita hoy".</p>
                    <button type="button" class="mt-4 text-tertiary text-label-bold flex items-center gap-1 group-hover:translate-x-1 transition-transform" style="background:transparent;border:none;cursor:pointer">
                        Aplicar sugerencia <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </button>
                </div>
            </div>
            <div class="snap-center shrink-0 w-[260px] lg:w-auto bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm flex items-start gap-5 hover:border-secondary hover:shadow-md cursor-pointer transition-all group">
                <div class="w-12 h-12 rounded-xl bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">question_answer</span>
                </div>
                <div>
                    <p class="text-headline-section text-on-surface mb-2">Agregar pregunta interactiva</p>
                    <p class="text-body-md text-on-surface-variant leading-relaxed">La IA sugiere: "¿Sabías que un motor descuidado gasta un 30% más de energía? Cuéntanos tu caso."</p>
                    <button type="button" class="mt-4 text-secondary text-label-bold flex items-center gap-1 group-hover:translate-x-1 transition-transform" style="background:transparent;border:none;cursor:pointer">
                        Aplicar sugerencia <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

</div>

<script>
    function fmt(cmd) { document.execCommand(cmd, false, null); }

    function syncBody() {
        document.getElementById('body-hidden').value = document.getElementById('post-editor').innerHTML;
        var text = document.getElementById('post-editor').innerText.trim();
        document.getElementById('preview-text').textContent = text.substring(0, 280) + (text.length > 280 ? '...' : '');
    }

    function updateCharCount() {
        var n = document.getElementById('post-editor').innerText.length;
        document.getElementById('char-count').textContent = n + ' caracteres';
    }

    function switchTab(btn, tabId) {
        ['tab-texto','tab-variantes','tab-media'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
        document.getElementById(tabId).style.display = 'block';
        document.querySelectorAll('.cc-tab').forEach(b => {
            b.style.color = '#434654';
            b.style.borderBottomColor = 'transparent';
            b.style.fontWeight = '400';
        });
        btn.style.color = '#003d9b';
        btn.style.borderBottomColor = '#003d9b';
        btn.style.fontWeight = '600';
    }

    function selectNetwork(btn) {
        document.querySelectorAll('.network-btn').forEach(b => {
            b.style.outline = 'none';
            b.style.opacity = '0.6';
        });
        btn.style.outline = '2px solid #003d9b';
        btn.style.outlineOffset = '2px';
        btn.style.opacity = '1';
    }

    function previewMedia(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-img-wrap').style.display = 'block';
                document.getElementById('media-preview').src = e.target.result;
                document.getElementById('media-current').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ── Generación de contenido con IA (Paso 1 → Paso 2) ──
    const AI_GENERATE_URL = "{{ route('blog.ai.generate') }}";
    const AI_STATUS_URL_BASE = "{{ url('admin/blog-ai/status') }}";
    const AI_POLL_INTERVAL_MS = 1500;
    const AI_POLL_MAX_ATTEMPTS = 40; // ~60s antes de darse por vencido

    function getAiBrief() {
        return {
            description: document.getElementById('ai-prompt').value.trim(),
            tone: document.getElementById('ai-tone').value,
            objective: document.getElementById('ai-objective').value,
            audience: document.getElementById('ai-audience').value.trim(),
            category_id: document.querySelector('[name="category_id"]').value || null,
        };
    }

    function validateAiBrief(brief) {
        if (!brief.description) {
            toastr.error('Describe el contenido antes de generar con IA.');
            return false;
        }
        if (!brief.audience) {
            toastr.error('Indica la audiencia objetivo antes de generar con IA.');
            return false;
        }
        return true;
    }

    function setGenerateAiButtonLoading(isLoading) {
        document.getElementById('generate-ai-btn').disabled = isLoading;
        document.getElementById('generate-ai-btn-icon').style.display = isLoading ? 'none' : 'inline-block';
        document.getElementById('generate-ai-btn-spinner').style.display = isLoading ? 'inline-block' : 'none';
        document.getElementById('generate-ai-btn-label').textContent = isLoading ? 'Generando contenido...' : 'Generar con IA';
    }

    async function generateContent() {
        const brief = getAiBrief();
        if (!validateAiBrief(brief)) return;

        setGenerateAiButtonLoading(true);

        try {
            const generationId = await requestAiGeneration(brief);
            const result = await pollAiGenerationResult(generationId);
            fillStepTwoFields(result);
            toastr.success('Contenido generado. Puedes editarlo antes de guardar.');
        } catch (error) {
            console.error('Error generando contenido con IA:', error);
            toastr.error(error.message || 'No se pudo generar el contenido. Intenta nuevamente.');
        } finally {
            setGenerateAiButtonLoading(false);
        }
    }

    async function requestAiGeneration(brief) {
        const response = await fetch(AI_GENERATE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(brief),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || 'No se pudo iniciar la generación de contenido.');
        }

        return data.id;
    }

    function pollAiGenerationResult(generationId) {
        return new Promise((resolve, reject) => {
            let attempts = 0;

            const interval = setInterval(async () => {
                attempts++;

                try {
                    const response = await fetch(`${AI_STATUS_URL_BASE}/${generationId}`, {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (!response.ok) {
                        throw new Error('No se pudo consultar el estado de la generación.');
                    }

                    const data = await response.json();

                    if (data.status === 'completed') {
                        clearInterval(interval);
                        resolve(data.data);
                    } else if (data.status === 'failed') {
                        clearInterval(interval);
                        reject(new Error(data.error || 'La generación de contenido falló.'));
                    } else if (attempts >= AI_POLL_MAX_ATTEMPTS) {
                        clearInterval(interval);
                        reject(new Error('La generación está tardando demasiado. Intenta nuevamente.'));
                    }
                } catch (error) {
                    clearInterval(interval);
                    reject(error);
                }
            }, AI_POLL_INTERVAL_MS);
        });
    }

    function fillStepTwoFields(result) {
        if (!result) return;

        if (result.title) {
            document.getElementById('post-title').value = result.title;
        }

        const editorHtml = buildEditorHtml(result);
        if (editorHtml) {
            document.getElementById('post-editor').innerHTML = editorHtml;
        }

        const hashtagsPreview = formatHashtags(result.hashtags);
        if (hashtagsPreview) {
            document.getElementById('preview-tags').textContent = hashtagsPreview;
        }

        syncBody();
        updateCharCount();
    }

    function buildEditorHtml(result) {
        const parts = [];

        if (result.content) {
            parts.push(`<p>${result.content.replace(/\n/g, '<br>')}</p>`);
        }

        const hashtagsPreview = formatHashtags(result.hashtags);
        if (hashtagsPreview) {
            parts.push(`<p>${hashtagsPreview}</p>`);
        }

        if (result.cta) {
            parts.push(`<p><strong>${result.cta}</strong></p>`);
        }

        return parts.join('');
    }

    function formatHashtags(hashtags) {
        if (!Array.isArray(hashtags) || !hashtags.length) return '';
        return hashtags.map(tag => tag.startsWith('#') ? tag : '#' + tag).join(' ');
    }

    syncBody(); updateCharCount();
</script>
