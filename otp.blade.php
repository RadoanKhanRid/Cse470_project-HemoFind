<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Please enter the 6-digit verification code sent to your registered device. For this demo, check the storage/logs/laravel.log file.') }}
    </div>

    <form method="POST" action="{{ route('otp.verify.post') }}" class="space-y-6">
        @csrf

        <!-- OTP Code -->
        <div>
            <x-input-label for="otp" :value="__('Verification Code')" />
            <x-text-input id="otp" class="block mt-1 w-full text-center text-2xl tracking-widest" type="text" name="otp" required autofocus autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Verify OTP') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
            {{ __('Back to Login') }}
        </a>
    </div>
</x-guest-layout>
