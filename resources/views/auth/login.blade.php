<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - JARA Todo List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-100 font-sans min-h-screen flex items-center justify-center p-4 antialiased">
    <div class="max-w-md w-full">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-indigo-600 items-center justify-center text-white text-2xl font-bold shadow-lg shadow-indigo-200 mb-3">
                J
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">JARA Todo List</h1>
            <p class="text-sm text-slate-500 mt-1">Silakan masuk ke akun Anda untuk melanjutkan</p>
        </div>

        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-8">
            <!-- Flash Message -->
            @if(session('success'))
                <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-3.5 text-emerald-800 text-sm flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-rose-800 text-sm flex items-center space-x-2">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('email') ? 'border-rose-400 focus:ring-rose-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }} text-slate-900 text-sm focus:outline-none focus:ring-4 transition placeholder-slate-400"
                            placeholder="nama@email.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                    </div>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('password') ? 'border-rose-400 focus:ring-rose-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }} text-slate-900 text-sm focus:outline-none focus:ring-4 transition placeholder-slate-400"
                        placeholder="••••••••">
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                        <span class="ml-2 text-xs text-slate-600">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-200 transition duration-150 ease-in-out">
                    Masuk ke Aplikasi
                </button>
            </form>

            <!-- Test Credentials Hint Box -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Akun Demo (Testing):</p>
                <div class="space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200/60 font-mono">
                    <div class="flex justify-between items-center">
                        <span><strong>Admin:</strong> admin@jara.test</span>
                        <span class="text-slate-400">pass: password</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span><strong>User:</strong> user1@jara.test</span>
                        <span class="text-slate-400">pass: password</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Notice -->
        <p class="text-center text-xs text-slate-400 mt-6">
            Proyek Praktikum Pemrograman Komputer (PPK) &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
