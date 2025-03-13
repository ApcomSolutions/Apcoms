<x-layout>
    <x-slot name="title">
        Lupa Password - APCOM Solutions
    </x-slot>

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 via-white to-pink-100">
        <!-- Back Button -->
        <a href="{{ route('login') }}" class="absolute top-5 left-5 w-10 h-10 flex items-center justify-center rounded-full bg-white bg-opacity-70 shadow hover:bg-opacity-90 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        <!-- Forgot Password Container -->
        <div class="bg-white bg-opacity-90 rounded-lg shadow-md p-10 w-full max-w-md">
            <div class="flex flex-col items-center">
                <!-- Logo -->
                <img src="{{ asset('images/icon.png') }}" alt="Logo" class="h-20 w-20 object-contain mb-8">

                <h2 class="text-xl font-semibold text-gray-700 mb-6">Reset Password</h2>
                <p class="text-gray-600 text-sm mb-6 text-center max-w-xs">Masukkan alamat email admin Anda dan kami akan mengirimkan link dan kode OTP untuk reset password.</p>

                <!-- Forgot Password Form -->
                <form method="POST" action="{{ route('login.forgot-password') }}" class="w-full flex flex-col items-center">
                    @csrf

                    <!-- Email Field -->
                    <div class="w-full max-w-xs mb-6">
                        <label for="email" class="block text-gray-600 font-medium mb-2">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 rounded-md border border-gray-200 bg-white bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan email admin Anda">

                        @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Send Link & OTP Button -->
                    <button type="submit" class="w-full max-w-xs py-3 px-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold rounded-md hover:from-blue-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transform hover:-translate-y-0.5 transition-all duration-300">
                        Kirim Link & OTP Reset Password
                    </button>

                    <!-- Back to Login -->
                    <a href="{{ route('login') }}" class="mt-4 text-blue-500 hover:text-blue-700">
                        Kembali ke Login
                    </a>
                </form>

                <!-- Success Message -->
                @if (session('status'))
                    <div class="mt-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded max-w-xs" role="alert">
                        {{ session('status') }}
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
</x-layout>
