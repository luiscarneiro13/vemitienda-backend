@extends('layouts.adminlte.index')
@section('content')

<form id="content-form" action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
@csrf

{{-- Toolbar --}}
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-2">
    <div class="flex items-baseline gap-2">
        <h2 class="text-sm font-bold text-slate-900">Crear Nuevo Contenido</h2>
        <span class="hidden sm:inline text-slate-300 text-xs">·</span>
        <p class="hidden sm:block text-[11px] text-slate-500">Crea, edita y publica artículos para tu blog</p>
    </div>
    <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
        <button type="button"
            style="background:transparent;border:1px solid #e2e8f0"
            class="order-0 col-span-2 sm:col-span-1 sm:order-0 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer"
            onclick="openTemplatesModal()">
            <span class="material-symbols-outlined" style="font-size:14px">dashboard_customize</span>
            Plantillas
        </button>
        <button type="submit"
            style="background:transparent;border:1px solid #0052cc"
            class="order-1 col-span-2 sm:order-2 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-[#0052cc] font-semibold hover:bg-blue-50 transition-colors cursor-pointer"
            onclick="document.getElementById('form-status').value='1'">
            <span class="material-symbols-outlined" style="font-size:14px">save</span>
            Guardar Borrador
        </button>
        <a href="{{ route('blog.index') }}"
            style="background:transparent;border:1px solid #e2e8f0;text-decoration:none"
            class="order-2 sm:order-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-slate-600 hover:bg-slate-50 transition-colors">
            <span class="material-symbols-outlined" style="font-size:14px">close</span>
            Cancelar
        </a>
        <button type="submit"
            style="background:#0052cc;border:none"
            class="order-3 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-white font-semibold hover:bg-[#003d99] transition-colors cursor-pointer"
            onclick="document.getElementById('form-status').value='2'">
            <span class="material-symbols-outlined" style="font-size:14px">publish</span>
            Publicar
        </button>
    </div>
</div>

{{-- Componente principal --}}
<x-content-creator :categories="$categories" :tags="$tags" :status="$status" />

</form>

<x-templates-modal />

@endsection
