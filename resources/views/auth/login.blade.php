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
            <div>
                <label class="text-xs text-gray-600">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-600">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <button type="submit" class="w-full bg-maroon text-white rounded-lg py-2 text-sm">
                Log in
            </button>
        </form>
    </div>
</body>
</html>
