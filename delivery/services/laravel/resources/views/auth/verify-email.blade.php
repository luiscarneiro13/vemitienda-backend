<x-guest-layout>
    <div class="mb-5 text-sm text-[#665f74] leading-relaxed">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div role="status" class="mb-5 rounded-[10px] bg-emerald-50 border border-emerald-200 px-4 py-3 font-medium text-sm text-emerald-800">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="inline-flex items-center min-h-[44px] px-3 underline text-sm font-medium text-[#665f74] hover:text-[#6135bc] rounded-[8px] focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#6135bc] transition-colors">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
