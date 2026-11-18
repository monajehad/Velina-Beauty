@extends('layouts.app')

@section('title', __('store_name'))

@section('content')
<div class="px-4 pt-4">

    {{-- Top bar: logo + language switcher --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}?v=2" alt="AL QAMAR / GENERAL TRADING" class="w-10 h-10 rounded-full object-cover">
            <div>
                <div class="text-sm font-semibold text-maroon">{{ __('store_name') }}</div>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <a href="{{ route('catalog.cart', ['locale' => app()->getLocale()]) }}" aria-label="{{ __('cart') }}"
               class="relative me-2 w-9 h-9 rounded-full bg-cream border border-rose text-maroon flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 002 1.8h8.2a2 2 0 002-1.6L21 8H6M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
                <span id="header-cart-count" class="hidden absolute -top-1 -end-1 min-w-[18px] h-[18px] px-1 rounded-full bg-maroon text-white text-[10px] font-bold flex items-center justify-center digits-en">0</span>
            </a>
            <a href="{{ route('lang.switch', 'en') }}"
               class="px-3 py-1.5 text-xs rounded-full {{ app()->getLocale() === 'en' ? 'bg-maroon text-white' : 'bg-cream text-maroon border border-rose' }}">
                EN
            </a>
            <a href="{{ route('lang.switch', 'fa') }}"
               class="px-3 py-1.5 text-xs rounded-full {{ app()->getLocale() === 'fa' ? 'bg-maroon text-white' : 'bg-cream text-maroon border border-rose' }}">
                فا
            </a>
        </div>
    </div>

    {{-- Category tabs --}}
    <div class="flex gap-2 my-3 overflow-x-auto pb-2">
        <a href="{{ route('catalog.index', ['locale' => app()->getLocale()]) }}"
           class="flex-shrink-0 text-center text-xs py-2 px-4 rounded-full whitespace-nowrap
                 {{ !request('category') ? 'bg-maroon text-white' : 'bg-cream text-maroon border border-rose' }}">
            {{ __('all_categories') }}
        </a>
        @foreach ($categories as $cat)
            <a href="{{ route('catalog.index', ['locale' => app()->getLocale(), 'category' => $cat->slug]) }}"
               class="flex-shrink-0 text-center text-xs py-2 px-4 rounded-full whitespace-nowrap
                     {{ request('category') === $cat->slug ? 'bg-maroon text-white' : 'bg-cream text-maroon border border-rose' }}">
                {{ $cat->icon }} {{ $cat->name }}
            </a>
        @endforeach
    </div>
    @if (request('category'))
        <a href="{{ route('catalog.index', ['locale' => app()->getLocale()]) }}" class="text-xs text-rose underline">{{ __('show_all_products') }}</a>
    @endif

    {{-- Product grid: responsive columns with equal height cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4 mt-3">
        @forelse ($products as $product)
            <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm flex flex-col" data-product-card
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-barcode="{{ $product->barcode }}"
                 data-price="{{ $product->price }}"
                 data-image="{{ $product->imageUrl() }}"
                 data-thumb="{{ $product->thumbUrl() }}">
                {{-- Product image fills the top of the card (click opens view modal) --}}
                <div class="relative aspect-[6/7] overflow-hidden bg-[#F1E7E0] cursor-pointer product-image"
                     data-id="{{ $product->id }}"
                     data-name="{{ $product->name }}"
                     data-barcode="{{ $product->barcode }}"
                     data-price="{{ $product->price }}"
                     data-image="{{ $product->imageUrl() }}">
                    <img src="{{ $product->thumbUrl() }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async">
                </div>

                {{-- Footer: name + round "+" button, which turns into a − qty + stepper once added --}}
                <div class="mt-auto flex items-center justify-between gap-2 px-3 py-2.5 min-h-[52px]">
                    <div class="text-sm md:text-base font-extrabold text-black truncate">{{ $product->name }}</div>

                    <button type="button" aria-label="{{ __('add') }}"
                            class="add-btn flex-shrink-0 w-8 h-8 rounded-full bg-maroon text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    </button>

                    <div class="qty-stepper hidden flex-shrink-0 items-center gap-1.5 h-8 px-2.5 rounded-full bg-maroon text-white">
                        <button type="button" class="qty-minus text-lg leading-none font-semibold px-0.5">−</button>
                        {{-- Tap the number to type a quantity directly --}}
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="4" value="0"
                               aria-label="{{ __('quantity') }}"
                               class="qty-value w-9 h-6 rounded-md bg-white/15 focus:bg-white focus:text-maroon text-center text-sm font-semibold digits-en border-0 outline-none p-0">
                        <button type="button" class="qty-plus text-lg leading-none font-semibold px-0.5">+</button>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-sm text-gray-500 py-10">{{ __('no_products') }}</p>
        @endforelse
    </div>
</div>

 {{-- Sticky bottom bar: Review Order button only --}}
<div class="fixed bottom-0 inset-x-0 safe-bottom">
    <div class="max-w-7xl mx-auto px-4 p-3">
        <a id="review-order-btn" href="{{ route('catalog.cart', ['locale' => app()->getLocale()]) }}"
           class="w-full flex items-center justify-between bg-[#2E7D4F] text-white rounded-2xl px-5 py-4 shadow-lg">
            <span class="text-base font-bold"><span id="cart-count" class="digits-en">0</span> {{ __('pcs') }}</span>
            <span class="flex items-center gap-1.5 text-base font-bold">
                {{ __('view_cart') }}
                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
            </span>
        </a>
    </div>
</div>

 {{-- Product Detail Modal (View Only) --}}
<div id="product-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="relative h-80 bg-gray-100 rounded-t-2xl flex items-center justify-center overflow-hidden">
            <img id="modal-image" src="" alt="" class="w-full h-full object-contain p-1">
            <button id="modal-close" class="absolute top-3 right-3 bg-white/90 rounded-full p-2 text-gray-600 hover:bg-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-4">
            <div class="text-[11px] text-gray-500 digits-en mb-1" id="modal-barcode"></div>
            <h3 id="modal-name" class="text-lg font-semibold text-gray-800"></h3>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cart lives in sessionStorage (shared with the cart page): it survives switching
    // categories and opening the cart page, and is gone once the tab is closed.
    const CART_KEY = 'alqamar_cart';
    const ORDER_NO_KEY = 'alqamar_order_no';
    // A manual refresh starts a new cart; moving between categories keeps it.
    let cart = {};
    const navEntry = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
    const isReload = navEntry ? navEntry.type === 'reload'
                              : (performance.navigation && performance.navigation.type === 1);
    if (isReload) {
        try { sessionStorage.removeItem(CART_KEY); sessionStorage.removeItem(ORDER_NO_KEY); } catch (e) {}
    } else {
        try { cart = JSON.parse(sessionStorage.getItem(CART_KEY)) || {}; } catch (e) { cart = {}; }
    }

    function saveCart() {
        try { sessionStorage.setItem(CART_KEY, JSON.stringify(cart)); } catch (e) {}
    }

    // Product Modal elements (View Only)
    const productModal = document.getElementById('product-modal');
    const modalImage = document.getElementById('modal-image');
    const modalName = document.getElementById('modal-name');
    const modalBarcode = document.getElementById('modal-barcode');
    const modalClose = document.getElementById('modal-close');

    const reviewOrderBtn = document.getElementById('review-order-btn');

    // Piece count in the bottom bar and on the header cart icon
    function updateTotal() {
        const pcs = Object.values(cart).reduce((n, item) => n + item.qty, 0);
        document.getElementById('cart-count').textContent = pcs;

        const badge = document.getElementById('header-cart-count');
        badge.textContent = pcs > 999 ? '999+' : pcs;
        badge.classList.toggle('hidden', pcs === 0);

        // Bottom bar is dimmed and inactive while the cart is empty
        reviewOrderBtn.classList.toggle('opacity-50', pcs === 0);
        reviewOrderBtn.classList.toggle('pointer-events-none', pcs === 0);
    }

    function openProductModal(product) {
        modalImage.src = product.image;
        modalName.textContent = product.name;
        modalBarcode.textContent = 'BC: ' + product.barcode;
        productModal.classList.remove('hidden');
        productModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeProductModal() {
        productModal.classList.add('hidden');
        productModal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // Open product modal on image click (view only)
    document.querySelectorAll('.product-image').forEach(function (img) {
        img.addEventListener('click', function () {
            const product = {
                id: img.dataset.id,
                name: img.dataset.name,
                barcode: img.dataset.barcode,
                price: img.dataset.price,
                image: img.dataset.image,
            };
            openProductModal(product);
        });
    });

    // Close product modal
    if (modalClose) modalClose.addEventListener('click', closeProductModal);
    productModal.addEventListener('click', function (e) {
        if (e.target === productModal) closeProductModal();
    });

    // Card controls: "+" adds 1 to the cart and shows the stepper; going back to 0 removes it.
    const cardRenders = [];
    document.querySelectorAll('[data-product-card]').forEach(function (card) {
        const id = card.dataset.id;
        const addBtn = card.querySelector('.add-btn');
        const stepper = card.querySelector('.qty-stepper');
        const qtyEl = card.querySelector('.qty-value');

        function render(q) {
            qtyEl.value = q;
            addBtn.classList.toggle('hidden', q > 0);
            stepper.classList.toggle('hidden', q === 0);
            stepper.classList.toggle('flex', q > 0);
        }

        function setQty(q) {
            q = Math.max(0, q);
            if (q > 0) {
                cart[id] = {
                    name: card.dataset.name,
                    barcode: card.dataset.barcode,
                    price: parseFloat(card.dataset.price),
                    image: card.dataset.image,
                    thumb: card.dataset.thumb,
                    qty: q,
                };
            } else {
                delete cart[id];
            }
            render(q);
            updateTotal();
            saveCart();
        }

        const currentQty = () => (cart[id] ? cart[id].qty : 0);
        addBtn.addEventListener('click', () => setQty(1));
        card.querySelector('.qty-plus').addEventListener('click', () => setQty(currentQty() + 1));
        card.querySelector('.qty-minus').addEventListener('click', () => setQty(currentQty() - 1));

        // Typed quantity: accepts Arabic/Persian digits too; empty or 0 removes the item
        qtyEl.addEventListener('focus', () => qtyEl.select());
        qtyEl.addEventListener('keydown', (e) => { if (e.key === 'Enter') qtyEl.blur(); });
        qtyEl.addEventListener('change', function () {
            const digits = qtyEl.value
                .replace(/[٠-٩]/g, d => d.charCodeAt(0) - 0x0660)
                .replace(/[۰-۹]/g, d => d.charCodeAt(0) - 0x06F0)
                .replace(/\D/g, '');
            setQty(parseInt(digits, 10) || 0);
        });

        // Show items already picked in another category
        render(currentQty());
        cardRenders.push(() => render(currentQty()));
    });

    // Back from the cart page (browser Back): the page comes from memory with old
    // state, so re-read the cart that may have been edited there.
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        try { cart = JSON.parse(sessionStorage.getItem(CART_KEY)) || {}; } catch (err) { cart = {}; }
        cardRenders.forEach(r => r());
        updateTotal();
    });

    updateTotal();
});
</script>
@endpush