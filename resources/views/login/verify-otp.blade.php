{{-- resources/views/login/verify-otp.blade.php --}}

<x-layout>
    <x-slot name="title">
        Verify OTP - APCOM Solutions
    </x-slot>

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 via-white to-pink-100">
        <!-- Back Button -->
        <a href="{{ route('login') }}"
           class="absolute top-5 left-5 w-10 h-10 flex items-center justify-center rounded-full bg-white bg-opacity-70 shadow hover:bg-opacity-90 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        <!-- OTP Verification Container -->
        <div class="bg-white bg-opacity-90 rounded-lg shadow-md p-10 w-full max-w-md">
            <div class="flex flex-col items-center">
                <!-- Logo -->
                <img src="{{ asset('images/icon.png') }}" alt="Logo" class="h-20 w-20 object-contain mb-8">

                <h2 class="text-xl font-semibold text-gray-700 mb-2">Verify OTP</h2>
                <p class="text-gray-600 text-sm mb-6 text-center max-w-xs">
                    We've sent a verification code to your email.
                    Please enter the 6-digit code below.
                </p>

                <!-- OTP Form -->
                <form method="POST" action="{{ route('login.verify-otp') }}" class="w-full flex flex-col items-center">
                    @csrf

                    <!-- Hidden Email Field -->
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                    <!-- OTP Field -->
                    <div class="w-full max-w-xs mb-6">
                        <label for="otp" class="block text-gray-600 font-medium mb-2">Verification Code</label>
                        <div class="flex justify-between gap-2">
                            @for ($i = 1; $i <= 6; $i++)
                                <input type="text" name="otp_digit_{{ $i }}"
                                       id="otp_digit_{{ $i }}" maxlength="1"
                                       class="w-10 h-12 text-center font-bold text-lg border border-gray-200 rounded-md bg-white bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       oninput="moveToNext(this, {{ $i }})" {{ $i == 1 ? 'autofocus' : '' }}>
                            @endfor
                        </div>
                        @error('otp')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Fields -->
                    <div class="w-full max-w-xs mb-6">
                        <label for="password" class="block text-gray-600 font-medium mb-2">Password Baru</label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-4 py-3 rounded-md border border-gray-200 bg-white bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan password baru">

                        @error('password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="w-full max-w-xs mb-6">
                        <label for="password_confirmation" class="block text-gray-600 font-medium mb-2">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 rounded-md border border-gray-200 bg-white bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Konfirmasi password baru">
                    </div>

                    <!-- Timer and Resend -->
                    <div class="w-full max-w-xs mb-6 flex justify-between items-center">
                        <div class="text-gray-500 text-sm" x-data="{ secondsLeft: 300 }" x-init="setInterval(() => { if (secondsLeft > 0) secondsLeft--; }, 1000)">
                            <span x-text="Math.floor(secondsLeft / 60)"></span>:<span
                                x-text="(secondsLeft % 60).toString().padStart(2, '0')"></span> remaining
                        </div>
                        <form method="POST" action="{{ route('login.resend-otp') }}" class="inline">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                            <button type="submit" class="text-blue-500 hover:text-blue-700 text-sm">
                                Resend Code
                            </button>
                        </form>
                    </div>

                    <!-- Verify Button -->
                    <button type="submit"
                            class="w-full max-w-xs py-3 px-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold rounded-md hover:from-blue-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transform hover:-translate-y-0.5 transition-all duration-300">
                        Verify & Reset Password
                    </button>
                </form>

                <!-- Success Message for Resend OTP -->
                @if (session('success'))
                    <div class="mt-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded max-w-xs" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Error Message -->
                @if (session('error'))
                    <div class="mt-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded max-w-xs" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function moveToNext(field, position) {
                // Allow only numbers
                field.value = field.value.replace(/[^0-9]/g, '');

                // Move to next field if this one is filled
                if (field.value !== '') {
                    if (position < 6) {
                        document.getElementById('otp_digit_' + (position + 1)).focus();
                    } else {
                        field.blur(); // Remove focus on the last field
                    }
                }

                // Move to previous field on backspace if empty
                field.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && field.value === '' && position > 1) {
                        document.getElementById('otp_digit_' + (position - 1)).focus();
                    }
                });
            }
        </script>
    @endpush
</x-layout>
