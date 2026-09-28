<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Velina Beauty</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { maroon: '#681B2B', cream: '#F7F0E8' } } } }</script>
</head>
<body class="bg-cream min-h-screen flex items-center justify-center px-4">
    <div class="bg-white rounded-xl shadow-sm p-6 w-full max-w-sm">
        <div class="text-center mb-5">
            <div class="text-maroon text-xl font-semibold">Velina Beauty</div>
            <div class="text-xs text-gray-500">Admin Login</div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 text-xs rounded-lg p-3 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-3">
            @csrf
            <div>
                <label class="text-xs text-gray-600">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
            </div>
            <div class="relative">
                <label class="text-xs text-gray-600">Password</label>
                <div class="relative mt-1">
                    <input type="password" name="password" id="password" required
                           class="w-full border rounded-lg px-3 py-2 text-sm pr-10">
                    <button type="button" id="togglePassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-maroon"
                            aria-label="Toggle password visibility">
                        <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7 10.01.546 15.76 4.33 16.57 8.22M3 3l18 18"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-600">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <button type="submit" class="w-full bg-maroon text-white rounded-lg py-2 text-sm">
                Log in
            </button>
        </form>
    </div>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            if (password.type === 'password') {
                password.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                password.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
