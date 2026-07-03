@extends('layouts.adminlte.index')
@section('content')

<form id="content-form" action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
@csrf

{{-- Toolbar de página --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-lg font-bold text-slate-800 leading-tight">Crear Nuevo Contenido</h2>
        <p class="text-xs text-slate-500 mt-0.5">Crea, edita y publica artículos para tu blog</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('blog.index') }}"
            style="background:transparent;border:1px solid #e2e8f0;text-decoration:none"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-slate-600 hover:bg-slate-50 transition-colors">
            <span class="material-symbols-outlined" style="font-size:15px">close</span>
            Cancelar
        </a>
        <button type="submit" name="action" value="draft"
            style="background:transparent;border:1px solid #0052cc"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-[#0052cc] font-semibold hover:bg-blue-50 transition-colors cursor-pointer">
            <span class="material-symbols-outlined" style="font-size:15px">save</span>
            Guardar Borrador
        </button>
        <button type="submit" name="action" value="publish"
            style="background:#0052cc;border:none"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-white font-semibold hover:bg-[#003d99] transition-colors cursor-pointer">
            <span class="material-symbols-outlined" style="font-size:15px">publish</span>
            Publicar
        </button>
    </div>
</div>

{{-- Grid principal --}}
<div class="flex gap-5" style="align-items:flex-start">

    {{-- Columna izquierda: Editor --}}
    <div class="flex-1 min-w-0 space-y-4">

        {{-- Título --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <input type="text" name="name" id="post-title"
                placeholder="Título del artículo..."
                value="{{ old('name') }}"
                required
                style="width:100%;border:none;outline:none;font-size:22px;font-weight:700;color:#0f172a;font-family:'Hanken Grotesk',sans-serif;background:transparent"
                class="placeholder-slate-300">
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-300" style="font-size:14px">link</span>
                <span class="text-[11px] text-slate-400" id="slug-preview">vemitienda.com/blog/<em>slug-generado</em></span>
            </div>
        </div>

        {{-- Editor de contenido --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-2 border-b border-slate-100 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-slate-400" style="font-size:14px">edit_note</span>
                <span class="text-xs font-semibold text-slate-500">Contenido</span>
            </div>
            <div id="quill-editor" style="min-height:340px; font-family:'Hanken Grotesk',sans-serif; font-size:14px"></div>
            <textarea name="body" id="body-hidden" style="display:none">{{ old('body') }}</textarea>
        </div>

        {{-- Extracto (auto) --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-1.5 mb-2">
                <span class="material-symbols-outlined text-slate-400" style="font-size:14px">short_text</span>
                <span class="text-xs font-semibold text-slate-500">Extracto (se genera automáticamente)</span>
            </div>
            <p id="excerpt-preview" class="text-xs text-slate-400 leading-relaxed italic">
                El extracto aparecerá aquí al escribir el contenido...
            </p>
        </div>

    </div>

    {{-- Columna derecha: Configuración --}}
    <div style="width:260px;flex-shrink:0" class="space-y-4">

        {{-- Publicación --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-1.5 mb-3">
                <span class="material-symbols-outlined text-slate-400" style="font-size:14px">tune</span>
                <span class="text-xs font-semibold text-slate-700">Publicación</span>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide block mb-1">Estado</label>
                    <select name="status" id="status-select"
                        style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:12px;color:#0f172a;outline:none;font-family:'Hanken Grotesk',sans-serif;background:#f8f9fb">
                        @foreach ($status as $s)
                            <option value="{{ $s['id'] }}" {{ old('status') == $s['id'] ? 'selected' : '' }}>
                                {{ $s['label'] === 'No' ? 'Borrador' : 'Publicado' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide block mb-1">Categoría</label>
                    <select name="category_id"
                        style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:12px;color:#0f172a;outline:none;font-family:'Hanken Grotesk',sans-serif;background:#f8f9fb">
                        <option value="">— Sin categoría —</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Etiquetas --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-1.5 mb-3">
                <span class="material-symbols-outlined text-slate-400" style="font-size:14px">label</span>
                <span class="text-xs font-semibold text-slate-700">Etiquetas</span>
            </div>
            <select name="tags[]" class="select2" multiple
                style="width:100%;font-size:12px">
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                        {{ $tag->label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Imagen destacada --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-1.5 mb-3">
                <span class="material-symbols-outlined text-slate-400" style="font-size:14px">image</span>
                <span class="text-xs font-semibold text-slate-700">Imagen destacada</span>
            </div>
            <label id="image-drop"
                style="display:block;border:2px dashed #e2e8f0;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:border-color 0.2s"
                onmouseover="this.style.borderColor='#0052cc'" onmouseout="this.style.borderColor='#e2e8f0'">
                <input type="file" name="image" accept="image/*" style="display:none" id="image-input"
                    onchange="previewImage(this)">
                <div id="image-placeholder">
                    <span class="material-symbols-outlined text-slate-300" style="font-size:32px;display:block">add_photo_alternate</span>
                    <p class="text-[11px] text-slate-400 mt-1">Haz clic o arrastra una imagen</p>
                    <p class="text-[10px] text-slate-300">PNG, JPG, WEBP</p>
                </div>
                <img id="image-preview" style="display:none;width:100%;border-radius:8px;object-fit:cover;max-height:140px">
            </label>
        </div>

    </div>

</div>

</form>

@endsection

@section('js')
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
<style>
    .ql-toolbar { border:none !important; border-bottom:1px solid #f1f5f9 !important; }
    .ql-container { border:none !important; }
    .ql-editor { font-family:'Hanken Grotesk',sans-serif; font-size:14px; color:#334155; line-height:1.7; }
    .ql-editor.ql-blank::before { color:#cbd5e1; font-style:normal; }
</style>
<script>
    // Editor Quill
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Empieza a escribir tu artículo...',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'code-block'],
                ['link', 'image'],
                ['clean']
            ]
        }
    })

    var existing = document.getElementById('body-hidden').value
    if (existing) quill.root.innerHTML = existing

    // Actualizar excerpt preview mientras escribe
    quill.on('text-change', function() {
        var text = quill.getText().trim()
        var preview = text.substring(0, 200)
        document.getElementById('excerpt-preview').textContent =
            preview ? preview + (text.length > 200 ? '...' : '') : 'El extracto aparecerá aquí al escribir el contenido...'
    })

    // Slug preview desde el título
    document.getElementById('post-title').addEventListener('input', function() {
        var slug = this.value.toLowerCase()
            .normalize('NFD').replace(/[̀-ͯ]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim().replace(/\s+/g, '-')
        document.getElementById('slug-preview').innerHTML =
            'vemitienda.com/blog/<em>' + (slug || 'slug-generado') + '</em>'
    })

    // Botones Publicar / Guardar Borrador setean el status
    document.querySelectorAll('[name="action"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var isPublish = this.value === 'publish'
            document.getElementById('status-select').value = isPublish ? '2' : '1'
            document.getElementById('body-hidden').value = quill.root.innerHTML
        })
    })

    // Preview imagen
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader()
            reader.onload = function(e) {
                document.getElementById('image-placeholder').style.display = 'none'
                var preview = document.getElementById('image-preview')
                preview.src = e.target.result
                preview.style.display = 'block'
            }
            reader.readAsDataURL(input.files[0])
        }
    }
</script>
@endsection
