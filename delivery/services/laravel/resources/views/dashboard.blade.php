<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-bold text-xl text-[#242034] leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-[#e3deec] overflow-hidden shadow-[0_12px_32px_rgba(36,32,52,0.05)] rounded-[20px]">
                <div class="p-6 sm:p-8 text-[#242034]">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
