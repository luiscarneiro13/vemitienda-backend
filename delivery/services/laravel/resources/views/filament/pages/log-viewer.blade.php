<x-filament-panels::page>
    <div class="fi-section overflow-hidden rounded-2xl border border-[#e3deec] bg-white shadow-sm dark:border-[#2e293d] dark:bg-[#242034]">
        <div class="flex items-center justify-between border-b border-[#e3deec] bg-[#faf9fc] px-5 py-3 text-xs font-medium text-[#665f74] dark:border-[#2e293d] dark:bg-[#171422] dark:text-[#c5bed2]">
            <span>Archivo: <code class="font-mono text-[#6135bc] dark:text-[#c2abf0]">storage/logs/laravel.log</code></span>
            <span>Últimas 500 líneas</span>
        </div>
        <pre class="max-h-[70vh] overflow-auto p-5 font-mono text-xs leading-relaxed text-[#242034] dark:text-[#e3deec]">{{ $logContent }}</pre>
    </div>
</x-filament-panels::page>
