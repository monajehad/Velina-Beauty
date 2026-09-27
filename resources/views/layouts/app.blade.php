<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('store_name'))</title>

    {{-- Tailwind via CDN: no build step needed, so the page works on any device --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: '#681B2B',
                        cream: '#F7F0E8',
                        rose: '#C28795',
                    }
                }
            }
        }
    </script>

    {{-- JsBarcode: draws the barcode client-side from the stored number, no server image needed --}}
    <script src="{{ asset('js/JsBarcode.all.min.js') }}"></script>

    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; }
        /* Old-phone friendly: avoid heavy shadows/animations, keep it simple */
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 0px); }
        .safe-top { padding-top: env(safe-area-inset-top, 0px); }
        /* Force English digits everywhere */
        .digits-en { font-variant-numeric: tabular-nums; font-feature-settings: "tnum"; direction: ltr; unicode-bidi: isolate; }
        .digits-en * { direction: ltr; unicode-bidi: isolate; }
    </style>
    @stack('styles')
</head>
<body class="bg-cream text-gray-800 safe-top">
    <div class="max-w-7xl mx-auto pb-28 px-4">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
