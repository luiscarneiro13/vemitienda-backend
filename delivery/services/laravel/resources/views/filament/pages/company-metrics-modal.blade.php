<div class="space-y-6 text-sm">
    {{-- Resumen de identidad --}}
    <div class="flex items-center justify-between pb-4 border-b border-[#e3deec] dark:border-[#2e293d]">
        <div>
            <h3 class="text-base font-bold text-[#242034] dark:text-white" style="font-family: var(--vmt-display, 'Space Grotesk', sans-serif);">{{ $record->name }}</h3>
            <p class="text-xs text-[#665f74] dark:text-gray-400">
                Slug: <code class="px-1.5 py-0.5 rounded bg-[#faf9fc] dark:bg-gray-800 text-[#242034] dark:text-gray-300 border border-[#e3deec] dark:border-gray-700">{{ $record->slug }}</code>
                @if($branch)
                    · Sucursal: <span class="font-medium text-[#242034] dark:text-gray-300">{{ $branch->name }}</span>
                    @if($branch->cityRecord) ({{ $branch->cityRecord->name }}) @endif
                @endif
            </p>
        </div>
        @if($catalogUrl)
            <a href="{{ $catalogUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-[10px] bg-primary-100 text-primary-600 dark:bg-primary-950/60 dark:text-primary-300 hover:bg-primary-200 transition">
                <span>Ver catálogo</span>
                <span aria-hidden="true">↗</span>
            </a>
        @endif
    </div>

    {{-- Grid de métricas clave (Uso por Clientes) --}}
    <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-[#665f74] dark:text-gray-400 mb-3">Métricas de Clientes (Pedidos y Visitas)</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            {{-- Pedidos WA --}}
            <div class="p-4 rounded-2xl border border-[#e3deec] dark:border-[#2e293d] bg-[#faf9fc] dark:bg-gray-800/40">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-[#665f74] dark:text-gray-400">Pedidos WhatsApp</span>
                    <span class="text-emerald-600 dark:text-emerald-400" aria-hidden="true">📱</span>
                </div>
                <div class="text-xl font-extrabold text-[#242034] dark:text-white" style="font-family: var(--vmt-display, 'Space Grotesk', sans-serif);">{{ number_format($totalOrders) }}</div>
                <span class="text-[11px] text-[#665f74] dark:text-gray-400">Enviados al chat</span>
            </div>

            {{-- Ventas estimadas --}}
            <div class="p-4 rounded-2xl border border-[#e3deec] dark:border-[#2e293d] bg-[#faf9fc] dark:bg-gray-800/40">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-[#665f74] dark:text-gray-400">Ventas Estimadas</span>
                    <span class="text-amber-600 dark:text-amber-400" aria-hidden="true">💰</span>
                </div>
                <div class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400" style="font-family: var(--vmt-display, 'Space Grotesk', sans-serif);">${{ number_format($totalCents / 100, 2) }}</div>
                <span class="text-[11px] text-[#665f74] dark:text-gray-400">Volumen total (GMV)</span>
            </div>

            {{-- Ticket Promedio --}}
            <div class="p-4 rounded-2xl border border-[#e3deec] dark:border-[#2e293d] bg-[#faf9fc] dark:bg-gray-800/40">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-[#665f74] dark:text-gray-400">Ticket Promedio</span>
                    <span class="text-blue-600 dark:text-blue-400" aria-hidden="true">🎫</span>
                </div>
                <div class="text-xl font-extrabold text-[#242034] dark:text-white" style="font-family: var(--vmt-display, 'Space Grotesk', sans-serif);">${{ number_format($avgTicket, 2) }}</div>
                <span class="text-[11px] text-[#665f74] dark:text-gray-400">Por pedido generado</span>
            </div>

            {{-- Visitas --}}
            <div class="p-4 rounded-2xl border border-[#e3deec] dark:border-[#2e293d] bg-[#faf9fc] dark:bg-gray-800/40">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-[#665f74] dark:text-gray-400">Visitas al Menú</span>
                    <span class="text-purple-600 dark:text-purple-400" aria-hidden="true">👁️</span>
                </div>
                <div class="text-xl font-extrabold text-[#242034] dark:text-white" style="font-family: var(--vmt-display, 'Space Grotesk', sans-serif);">{{ number_format($totalViews) }}</div>
                <span class="text-[11px] text-[#665f74] dark:text-gray-400">Aperturas de catálogo</span>
            </div>

            {{-- Tasa de Conversión --}}
            <div class="p-4 rounded-2xl border border-[#e3deec] dark:border-[#2e293d] bg-[#faf9fc] dark:bg-gray-800/40">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-[#665f74] dark:text-gray-400">Conversión</span>
                    <span class="text-cyan-600 dark:text-cyan-400" aria-hidden="true">📈</span>
                </div>
                <div class="text-xl font-extrabold {{ $convRate >= 10 ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#242034] dark:text-gray-300' }}" style="font-family: var(--vmt-display, 'Space Grotesk', sans-serif);">
                    {{ $convRate }}%
                </div>
                <span class="text-[11px] text-[#665f74] dark:text-gray-400">Visitas a pedidos</span>
            </div>

            {{-- Productos Activos --}}
            <div class="p-4 rounded-2xl border border-[#e3deec] dark:border-[#2e293d] bg-[#faf9fc] dark:bg-gray-800/40">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-[#665f74] dark:text-gray-400">Productos Menú</span>
                    <span class="text-orange-600 dark:text-orange-400" aria-hidden="true">🏷️</span>
                </div>
                <div class="text-xl font-extrabold text-[#242034] dark:text-white" style="font-family: var(--vmt-display, 'Space Grotesk', sans-serif);">{{ $activeProducts }} <span class="text-xs font-normal text-[#665f74] dark:text-gray-400">/ {{ $productsCount }}</span></div>
                <span class="text-[11px] text-[#665f74] dark:text-gray-400">Disponibles ahora</span>
            </div>
        </div>
    </div>

    {{-- Resumen de Uso por la Tienda --}}
    <div class="pt-2">
        <h4 class="text-xs font-bold uppercase tracking-wider text-[#665f74] dark:text-gray-400 mb-2">Actividad y Operación de la Tienda</h4>
        <dl class="divide-y divide-[#e3deec] dark:divide-[#2e293d] rounded-2xl border border-[#e3deec] dark:border-[#2e293d] bg-white dark:bg-gray-800/20 px-4">
            <div class="py-2.5 flex items-center justify-between">
                <dt class="text-xs text-[#665f74] dark:text-gray-400">Último pedido por WhatsApp</dt>
                <dd class="text-xs font-semibold text-[#242034] dark:text-gray-200">
                    {{ $lastOrder ? $lastOrder->diffForHumans() . ' (' . $lastOrder->format('d/m/Y H:i') . ')' : 'Sin pedidos registrados aún' }}
                </dd>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <dt class="text-xs text-[#665f74] dark:text-gray-400">Última actualización de la tienda</dt>
                <dd class="text-xs font-semibold text-[#242034] dark:text-gray-200">
                    {{ $record->updated_at ? $record->updated_at->diffForHumans() : 'N/A' }}
                </dd>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <dt class="text-xs text-[#665f74] dark:text-gray-400">WhatsApp de destino</dt>
                <dd class="text-xs font-semibold text-[#242034] dark:text-gray-200">
                    {{ $branch?->whatsapp_number ? '+' . $branch->whatsapp_number : 'No configurado' }}
                </dd>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <dt class="text-xs text-[#665f74] dark:text-gray-400">Estado en plataforma</dt>
                <dd class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $record->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $record->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    {{ $record->is_active ? 'Tienda Activa' : 'Tienda Inactiva' }}
                </dd>
            </div>
        </dl>
    </div>
</div>
