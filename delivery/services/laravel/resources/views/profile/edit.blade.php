<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-bold text-xl text-[#242034] leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 sm:p-8 bg-white border border-[#e3deec] shadow-[0_12px_32px_rgba(36,32,52,0.05)] rounded-[20px]">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-[#e3deec] shadow-[0_12px_32px_rgba(36,32,52,0.05)] rounded-[20px]">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-[#e3deec] shadow-[0_12px_32px_rgba(36,32,52,0.05)] rounded-[20px]">
                <div class="max-w-xl">
                    @include('profile.partials.api-tokens', ['tokens' => $tokens])
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-[#e3deec] shadow-[0_12px_32px_rgba(36,32,52,0.05)] rounded-[20px]">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
