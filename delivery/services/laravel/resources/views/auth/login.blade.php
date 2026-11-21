<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1.5 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center min-h-[44px] cursor-pointer select-none">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-[#c5bed2] text-[#6135bc] shadow-sm focus:ring-[#6135bc] focus:ring-offset-0" name="remember">
                <span class="ms-2.5 text-sm font-medium text-[#665f74]">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-3 mt-5">
            @if (Route::has('password.request'))
                <a class="inline-flex items-center min-h-[44px] px-2 underline text-sm font-medium text-[#665f74] hover:text-[#6135bc] rounded-[8px] focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#6135bc] transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
