@extends('layouts.app')

@section('title', __('store_name'))

@section('content')
<div class="px-4 pt-4">

    {{-- Top bar: logo + language switcher --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="Velina Beauty" class="w-10 h-10">
            <div>
                <div class="text-sm font-semibold text-maroon">{{ __('store_name') }}</div>
                <div class="text-[10px] text-gray-500">{{ __('owner_name') }}</div>
            </div>
        </div>
        <div class="flex gap-1">
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
    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 mt-3">
        @forelse ($products as $product)
            <div class="product-card bg-white rounded-xl border border-[#e7ddd2] p-2" data-product-card>
                <div class="card-content flex flex-col">
                    {{-- Product Image (clickable for view modal) - half card height --}}
                    <div class="bg-[#F1E7E0] rounded-lg h-40 flex items-center justify-center overflow-hidden cursor-pointer product-image"
                         data-id="{{ $product->id }}"
                         data-name="{{ $product->name }}"
                         data-barcode="{{ $product->barcode }}"
                         data-price="{{ $product->price }}"
                         data-image="{{ $product->imageUrl() }}"
                         data-desc="{{ $product->name }}">
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    </div>

                    <div class="text-xs font-semibold text-center text-gray-800 mt-1 leading-tight">{{ $product->name }}</div>

                    {{-- Barcode drawn client-side by JsBarcode from the stored number --}}
                    <svg class="barcode w-3/4 mx-auto mt-1" style="height: 24px;" data-barcode="{{ $product->barcode }}"></svg>
                    <div class="text-[11px] text-center text-gray-500 digits-en">BC: #{{ $product->barcode }}</div>

                    <div class="text-sm text-center text-maroon font-medium my-1 digits-en">{{ number_format($product->price, 3) }} {{ __('price_prefix') }}</div>

                    {{-- Quantity selector (starts at 0) --}}
                    <div class="flex items-center justify-between bg-cream rounded-lg px-2 py-1">
                        <button type="button" class="text-maroon font-semibold text-lg leading-none qty-minus" disabled>−</button>
                        <input type="number" class="text-xs qty-value digits-en w-12 text-center border-0 bg-transparent focus:outline-none focus:ring-0" data-qty="0" value="0" min="0" step="1">
                        <button type="button" class="text-maroon font-semibold text-lg leading-none qty-plus">+</button>
                    </div>
                </div>
                {{-- Card footer: Add button aligned at bottom --}}
                <div class="card-footer mt-2">
                    {{-- Add to cart button --}}
                    <button type="button"
                            class="add-btn w-full bg-maroon text-white text-xs rounded-lg py-1.5 opacity-50 cursor-not-allowed"
                            disabled
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-barcode="{{ $product->barcode }}"
                            data-price="{{ $product->price }}"
                            data-image="{{ $product->imageUrl() }}">
                        {{ __('add') }}
                    </button>
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
        <button id="review-order-btn"
                class="w-full bg-[#25D366] text-white rounded-full py-3 text-sm font-medium opacity-50 cursor-not-allowed"
                disabled>
            🧾 {{ __('review_order') }}
        </button>
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
            <h3 id="modal-name" class="text-lg font-semibold text-gray-800 mb-2"></h3>
            <p id="modal-price" class="text-maroon font-bold text-xl mb-4"></p>
        </div>
    </div>
</div>

 {{-- Order Review Modal --}}
<div id="order-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-maroon text-white rounded-t-2xl px-4 py-3 flex items-center justify-between">
            <h3 class="font-semibold">{{ __('order_review') }}</h3>
            <button id="order-modal-close" class="bg-white/20 rounded-full p-2 hover:bg-white/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-4 max-h-96 overflow-y-auto">
            {{-- Customer Info Section --}}
            <div class="mb-4 space-y-3">
                <h4 class="text-sm font-semibold text-gray-700">{{ __('customer_info') }}</h4>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">{{ __('customer_name') }}</label>
                    <input type="text" id="customer-name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-maroon" placeholder="{{ __('customer_name_placeholder') }}">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">{{ __('customer_phone') }}</label>
                    <input type="tel" id="customer-phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-maroon" placeholder="{{ __('customer_phone_placeholder') }}">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">{{ __('customer_address') }}</label>
                    <textarea id="customer-address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-maroon" placeholder="{{ __('customer_address_placeholder') }}"></textarea>
                </div>
            </div>

            {{-- Order Items --}}
            <div id="order-items" class="space-y-3">
                {{-- Items will be injected here --}}
            </div>
            
            {{-- Order Summary --}}
            <div class="border-t border-gray-200 mt-4 pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ __('subtotal') }}</span>
                    <span id="order-subtotal" class="font-medium digits-en">$0.00</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-maroon">
                    <span>{{ __('total') }}</span>
                    <span id="order-total" class="digits-en">$0.00</span>
                </div>
            </div>

            {{-- Auto-generated Order Number --}}
            <div class="mt-4 p-3 bg-cream rounded-lg">
                <div class="text-xs text-gray-600">{{ __('order_number') }}</div>
                <div id="order-number" class="text-lg font-bold text-maroon digits-en font-mono">—</div>
            </div>
        </div>
        <div class="bg-gray-50 rounded-b-2xl px-4 py-3">
            <a id="order-whatsapp-btn"
               href="#"
               target="_blank"
               class="block text-center bg-[#25D366] text-white rounded-full py-3 font-medium text-sm">
                💬 {{ __('confirm_order_whatsapp') }}
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pass translations to JavaScript
    <?php
    $translations = [
        'add' => __('add'),
        'added' => __('added'),
        'order_message' => __('order_message'),
        'review_order' => __('review_order'),
        'order_review' => __('order_review'),
        'subtotal' => __('subtotal'),
        'total' => __('total'),
        'confirm_order_whatsapp' => __('confirm_order_whatsapp'),
    ];
    ?>
    const translations = @json($translations);

document.addEventListener('DOMContentLoaded', function () {
    // Draw every barcode from its stored number (CODE128 keeps leading zeros intact)
    document.querySelectorAll('.barcode').forEach(function (el) {
        try {
            if (typeof JsBarcode !== 'undefined') {
                JsBarcode(el, el.dataset.barcode, {
                    format: 'CODE128',
                    width: 1.3,
                    height: 30,
                    fontSize: 11,
                    margin: 0,
                    background: 'transparent',
                });
            }
        } catch (e) {
            console.warn('JsBarcode error:', e);
        }
    });

    const whatsappNumber = @json($whatsappNumber);
    const cart = {}; // { productId: { name, barcode, price, qty, image } }

    // Product Modal elements (View Only)
    const productModal = document.getElementById('product-modal');
    const modalImage = document.getElementById('modal-image');
    const modalName = document.getElementById('modal-name');
    const modalPrice = document.getElementById('modal-price');
    const modalBarcode = document.getElementById('modal-barcode');
    const modalClose = document.getElementById('modal-close');

    // Order Modal elements
    const orderModal = document.getElementById('order-modal');
    const orderItems = document.getElementById('order-items');
    const orderSubtotal = document.getElementById('order-subtotal');
    const orderTotal = document.getElementById('order-total');
    const orderNumberEl = document.getElementById('order-number');
    const customerNameEl = document.getElementById('customer-name');
    const customerPhoneEl = document.getElementById('customer-phone');
    const customerAddressEl = document.getElementById('customer-address');
    const orderModalClose = document.getElementById('order-modal-close');
    const orderWhatsAppBtn = document.getElementById('order-whatsapp-btn');
    const reviewOrderBtn = document.getElementById('review-order-btn');

    function generateOrderNumber() {
        const now = new Date();
        const dateStr = now.toISOString().slice(0,10).replace(/-/g,'');
        const timeStr = now.toTimeString().slice(0,5).replace(':','');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        return `ORD-${dateStr}-${timeStr}-${random}`;
    }

    function updateTotal() {
        let total = 0;
        Object.values(cart).forEach(item => total += item.price * item.qty);
        const totalEl = document.getElementById('cart-total');
        const pricePrefix = '{{ __('price_prefix') }}';
        if (totalEl) totalEl.textContent = total.toFixed(3) + ' ' + pricePrefix;

        // Enable/disable review order button
        if (reviewOrderBtn) {
            if (Object.keys(cart).length > 0) {
                reviewOrderBtn.disabled = false;
                reviewOrderBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                reviewOrderBtn.disabled = true;
                reviewOrderBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    function updateCardUI(card, qty) {
        const qtyEl = card.querySelector('.qty-value');
        const minusBtn = card.querySelector('.qty-minus');
        const plusBtn = card.querySelector('.qty-plus');
        const addBtn = card.querySelector('.add-btn');
        const id = addBtn.dataset.id;

        if (qtyEl) {
            qtyEl.dataset.qty = qty;
            qtyEl.value = qty;
        }
        if (minusBtn) minusBtn.disabled = qty <= 0;
        if (addBtn) {
            if (qty > 0) {
                addBtn.disabled = false;
                addBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                addBtn.disabled = true;
                addBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    function openProductModal(product) {
        modalImage.src = product.image;
        modalName.textContent = product.name;
        modalPrice.textContent = parseFloat(product.price).toFixed(3) + ' ' + '{{ __('price_prefix') }}';
        modalBarcode.textContent = 'BC: #' + product.barcode;
        productModal.classList.remove('hidden');
        productModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeProductModal() {
        productModal.classList.add('hidden');
        productModal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function openOrderModal() {
        // Build order items HTML
        let itemsHtml = '';
        let subtotal = 0;
        const pricePrefix = '{{ __('price_prefix') }}';
        
        Object.values(cart).forEach((item, index) => {
            const itemTotal = item.price * item.qty;
            subtotal += itemTotal;
            itemsHtml += `
                <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0">
                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-800 truncate">${item.name}</h4>
                        <p class="text-[10px] text-gray-500 digits-en">BC: #${item.barcode}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500">${item.price.toFixed(3)} ${pricePrefix}</span>
                            <span class="text-xs text-gray-400">×</span>
                            <span class="text-xs font-medium digits-en">${item.qty}</span>
                            <span class="text-xs text-gray-400">=</span>
                            <span class="text-xs font-semibold text-maroon digits-en">${itemTotal.toFixed(3)} ${pricePrefix}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        orderItems.innerHTML = itemsHtml;
        orderSubtotal.textContent = subtotal.toFixed(3) + ' ' + pricePrefix;
        orderTotal.textContent = subtotal.toFixed(3) + ' ' + pricePrefix;
        
        // Generate and display order number
        const orderNumber = generateOrderNumber();
        orderNumberEl.textContent = orderNumber;
        
        // Function to build WhatsApp message with current customer data
        function buildWhatsAppMessage() {
            const lines = Object.values(cart).map((item, i) =>
                `${i + 1}) ${item.name}\n\u2022 Barcode: #${item.barcode}\n\u2022 Qty: ${item.qty}\n\u2022 Price: ${(item.price * item.qty).toFixed(3)} ${pricePrefix}\n\u2022 Image: ${item.image}`
            );
            
            const customerName = customerNameEl.value.trim();
            const customerPhone = customerPhoneEl.value.trim();
            const customerAddress = customerAddressEl.value.trim();
            
            let message = '';
            if (lines.length) {
                message = `${translations.order_message}\n\n`;
                message += `Order #: ${orderNumber}\n`;
                if (customerName) message += `Customer: ${customerName}\n`;
                if (customerPhone) message += `Phone: ${customerPhone}\n`;
                if (customerAddress) message += `Address: ${customerAddress}\n`;
                message += `---------------------------------\n`;
                message += `${lines.join('\n\n')}\n\n`;
                message += `---------------------------------\n`;
                message += `Total: ${subtotal.toFixed(3)} ${pricePrefix}`;
            } else {
                message = translations.order_message;
            }
            return message;
        }
        
        // Set initial WhatsApp link
        orderWhatsAppBtn.href = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(buildWhatsAppMessage())}`;
        
        // Update WhatsApp link whenever customer inputs change
        [customerNameEl, customerPhoneEl, customerAddressEl].forEach(el => {
            el.addEventListener('input', function() {
                orderWhatsAppBtn.href = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(buildWhatsAppMessage())}`;
            });
        });
        
        orderModal.classList.remove('hidden');
        orderModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeOrderModal() {
        orderModal.classList.add('hidden');
        orderModal.classList.remove('flex');
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

    // Open order modal
    if (reviewOrderBtn) {
        reviewOrderBtn.addEventListener('click', function () {
            if (Object.keys(cart).length > 0) {
                openOrderModal();
            }
        });
    }

    // Close order modal
    if (orderModalClose) orderModalClose.addEventListener('click', closeOrderModal);
    orderModal.addEventListener('click', function (e) {
        if (e.target === orderModal) closeOrderModal();
    });

    // Card quantity controls
    document.querySelectorAll('[data-product-card]').forEach(function (card) {
        const minus = card.querySelector('.qty-minus');
        const plus = card.querySelector('.qty-plus');
        const qtyEl = card.querySelector('.qty-value');
        const addBtn = card.querySelector('.add-btn');

        function currentQty() {
            return parseInt(qtyEl.dataset.qty, 10);
        }

        function setQty(q) {
            q = Math.max(0, q);
            qtyEl.dataset.qty = q;
            qtyEl.value = q;
            updateCardUI(card, q);
            // If this product is already in the cart, keep the cart quantity in sync live.
            if (cart[addBtn.dataset.id]) {
                cart[addBtn.dataset.id].qty = q;
                updateTotal();
            }
        }

        if (minus) minus.addEventListener('click', () => setQty(currentQty() - 1));
        if (plus) plus.addEventListener('click', () => setQty(currentQty() + 1));
        // Manual input in card
        if (qtyEl) {
            qtyEl.addEventListener('change', function () {
                let val = parseInt(this.value, 10);
                if (isNaN(val) || val < 0) val = 0;
                this.value = val;
                this.dataset.qty = val;
                setQty(val);
            });
        }

        if (addBtn) {
            addBtn.addEventListener('click', function () {
                const id = addBtn.dataset.id;
                const qty = currentQty();
                if (qty <= 0) return;
                
                if (cart[id]) {
                    delete cart[id];
                    addBtn.textContent = translations.add;
                    addBtn.classList.remove('bg-rose');
                    addBtn.classList.add('bg-maroon');
                } else {
                    cart[id] = {
                        name: addBtn.dataset.name,
                        barcode: addBtn.dataset.barcode,
                        price: parseFloat(addBtn.dataset.price),
                        image: addBtn.dataset.image,
                        qty: qty,
                    };
                    addBtn.textContent = translations.added;
                    addBtn.classList.remove('bg-maroon');
                    addBtn.classList.add('bg-rose');
                }
                updateTotal();
            });
        }
    });

    updateTotal();
});
</script>
@endpush