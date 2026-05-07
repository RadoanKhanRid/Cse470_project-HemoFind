<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('A 6-digit verification code has been generated. Please check your system logs (or email) to complete your login.') }}
    </div>

    <form method="POST" action="{{ route('otp.verify.post') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('6-Digit OTP')" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" required autofocus placeholder="123456" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verify Code') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>