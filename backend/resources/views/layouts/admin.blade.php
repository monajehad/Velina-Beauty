<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Velina Beauty</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { maroon: '#681B2B', cream: '#F7F0E8', rose: '#C28795' } } } }</script>
</head>
<body class="bg-cream min-h-screen">
    <div class="flex flex-col md:flex-row">
        <aside class="bg-maroon text-white md:w-56 md:min-h-screen p-4">
            <div class="font-semibold mb-4">Velina Beauty — Admin</div>
            <nav class="space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-2 py-1.5 rounded hover:bg-white/10">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block px-2 py-1.5 rounded hover:bg-white/10">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="block px-2 py-1.5 rounded hover:bg-white/10">Categories</a>
                <a href="{{ route('catalog.index', ['locale' => 'en']) }}" target="_blank" class="block px-2 py-1.5 rounded hover:bg-white/10">View storefront ↗</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <button class="block w-full text-left px-2 py-1.5 rounded hover:bg-white/10">Log out</button>
                </form>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3 mb-4">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
