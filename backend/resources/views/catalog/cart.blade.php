@extends('layouts.app')

@section('title', __('cart') . ' - ' . __('store_name'))

@push('styles')
<style>
    @page { size: A4; margin: 12mm; }
    @media print {
        html, body { background: #fff !important; }
        body > div { padding: 0 !important; max-width: none !important; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        #print-sheet tr { break-inside: avoid; }
    }
</style>
@endpush

@section('content')
@php $catalogUrl = route('catalog.index', ['locale' => app()->getLocale()]); @endphp

{{-- ===================== Screen view ===================== --}}
<div class="pt-4 print:hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <a href="{{ $catalogUrl }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}?v=2" alt="AL QAMAR / GENERAL TRADING" class="w-10 h-10 rounded-full object-cover">
            <span class="text-sm font-semibold text-maroon">{{ __('store_name') }}</span>
        </a>
        <a href="{{ $catalogUrl }}" class="flex items-center gap-1 text-sm font-semibold text-maroon">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M11 18l-6-6 6-6"/></svg>
            {{ __('continue_shopping') }}
        </a>
    </div>

    <h1 class="text-xl md:text-2xl font-extrabold text-black mb-4">
        {{ __('cart') }}
        <span class="text-base font-semibold text-gray-500">(<span id="head-pcs" class="digits-en">0</span> {{ __('pcs') }})</span>
    </h1>

    {{-- Empty state --}}
    <div id="cart-empty" class="hidden bg-white rounded-2xl p-10 text-center">
        <p class="text-gray-500 mb-4">{{ __('cart_empty') }}</p>
        <a href="{{ $catalogUrl }}" class="inline-block bg-maroon text-white rounded-full px-6 py-2.5 text-sm font-semibold">{{ __('continue_shopping') }}</a>
    </div>

    {{-- Sent notice --}}
    <div id="sent-notice" class="hidden bg-green-50 text-green-800 rounded-xl p-3 mb-4 text-sm font-medium">{{ __('order_sent') }}</div>

    <div id="cart-content" class="hidden grid lg:grid-cols-[1fr_380px] gap-4 md:gap-6 items-start">

        {{-- Items --}}
        <div id="cart-items" class="bg-white rounded-2xl divide-y divide-gray-100"></div>

        {{-- Sidebar: customer + summary + actions --}}
        <div class="space-y-4 lg:sticky lg:top-4">
            <div class="bg-white rounded-2xl p-4 space-y-3">
                <h2 class="text-sm font-bold text-gray-800">{{ __('customer_info') }}</h2>
                <div>
                    <label class="block text-xs text-gray-600 mb-1" for="customer-name">{{ __('customer_name') }}</label>
                    <input type="text" id="customer-name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-maroon" placeholder="{{ __('customer_name_placeholder') }}">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1" for="customer-phone">{{ __('customer_phone') }}</label>
                    <input type="tel" id="customer-phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-maroon" placeholder="{{ __('customer_phone_placeholder') }}">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1" for="customer-address">{{ __('customer_address') }}</label>
                    <textarea id="customer-address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-maroon" placeholder="{{ __('customer_address_placeholder') }}"></textarea>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">{{ __('order_number') }}</span>
                    <span class="order-number text-sm font-bold text-maroon digits-en font-mono"></span>
                </div>
                <div class="flex justify-between items-center border-t border-gray-100 pt-3 text-lg font-bold text-maroon">
                    <span>{{ __('total') }}</span>
                    <span><span class="total-pcs digits-en">0</span> {{ __('pcs') }}</span>
                </div>

                <a id="whatsapp-btn" href="#" target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-2 bg-[#25D366] text-white rounded-full py-3 font-semibold text-sm">
                    💬 {{ __('confirm_order_whatsapp') }}
                </a>
                <button id="print-btn" type="button"
                        class="w-full flex items-center justify-center gap-2 border-2 border-maroon text-maroon rounded-full py-2.5 font-semibold text-sm hover:bg-maroon hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/></svg>
                    {{ __('print_order') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===================== Print / PDF view ===================== --}}
<div id="print-sheet" class="hidden print:block text-black text-sm">
    <div class="flex items-center justify-between border-b-2 border-maroon pb-3 mb-4">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}?v=2" alt="" class="w-16 h-16 rounded-full object-cover">
            <div class="text-lg font-extrabold text-maroon">AL QAMAR<br><span class="text-xs font-semibold tracking-widest">GENERAL TRADING</span></div>
        </div>
        <div class="text-end">
            <div class="text-xs text-gray-600">{{ __('order_number') }}</div>
            <div class="order-number font-bold font-mono digits-en"></div>
            <div class="text-xs text-gray-600 mt-1">{{ __('date') }}: <span id="print-date" class="digits-en"></span></div>
        </div>
    </div>

    <div id="print-customer" class="grid grid-cols-3 gap-3 mb-4 text-xs">
        <div><div class="text-gray-500">{{ __('customer_name') }}</div><div id="p-name" class="font-semibold">—</div></div>
        <div><div class="text-gray-500">{{ __('customer_phone') }}</div><div id="p-phone" class="font-semibold digits-en">—</div></div>
        <div><div class="text-gray-500">{{ __('customer_address') }}</div><div id="p-address" class="font-semibold whitespace-pre-line">—</div></div>
    </div>

    <table class="w-full border-collapse text-xs">
        <thead>
            <tr class="bg-cream text-maroon">
                <th class="border border-gray-300 p-2 w-8">#</th>
                <th class="border border-gray-300 p-2 w-16"></th>
                <th class="border border-gray-300 p-2 text-start">{{ __('product') }}</th>
                <th class="border border-gray-300 p-2 text-start">{{ __('barcode') }}</th>
                <th class="border border-gray-300 p-2 w-20">{{ __('quantity') }}</th>
            </tr>
        </thead>
        <tbody id="print-rows"></tbody>
        <tfoot>
            <tr class="font-bold text-maroon">
                <td colspan="4" class="border border-gray-300 p-2 text-end">{{ __('total') }}</td>
                <td class="border border-gray-300 p-2 text-center"><span class="total-pcs digits-en"></span> {{ __('pcs') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

@push('scripts')
{{-- Builds the order PDF in the browser (keeps Arabic/Persian text shaped correctly) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const whatsappNumber = @json($whatsappNumber);
    const orderMessage = @json(__('order_message'));
    const pdfEndpoint = @json(route('catalog.cart.pdf', ['locale' => app()->getLocale()]));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const t = {
        quantity: @json(__('quantity')),
        remove: @json(__('remove')),
        preparing: @json(__('preparing_order')),
        sendPdf: @json(__('send_order_pdf')),
    };

    // Same storage keys as the catalog page
    const CART_KEY = 'alqamar_cart';
    const ORDER_NO_KEY = 'alqamar_order_no';
    const CUSTOMER_KEY = 'alqamar_customer';

    const store = {
        get(key, fallback) { try { return JSON.parse(sessionStorage.getItem(key)) ?? fallback; } catch (e) { return fallback; } },
        set(key, value) { try { sessionStorage.setItem(key, JSON.stringify(value)); } catch (e) {} },
        remove(key) { try { sessionStorage.removeItem(key); } catch (e) {} },
    };

    let cart = store.get(CART_KEY, {});
    let sent = false;

    const el = (id) => document.getElementById(id);
    const itemsEl = el('cart-items');
    const nameEl = el('customer-name');
    const phoneEl = el('customer-phone');
    const addressEl = el('customer-address');
    const waBtn = el('whatsapp-btn');

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const totalPcs = () => Object.values(cart).reduce((n, item) => n + item.qty, 0);

    function generateOrderNumber() {
        const now = new Date();
        const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
        const timeStr = now.toTimeString().slice(0, 5).replace(':', '');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        return `ORD-${dateStr}-${timeStr}-${random}`;
    }

    let orderNumber = store.get(ORDER_NO_KEY, null);
    if (!orderNumber) { orderNumber = generateOrderNumber(); store.set(ORDER_NO_KEY, orderNumber); }
    document.querySelectorAll('.order-number').forEach(n => n.textContent = orderNumber);
    el('print-date').textContent = new Date().toLocaleString('en-GB', { dateStyle: 'medium', timeStyle: 'short' });

    // Customer details survive moving back to the catalog and returning
    const customer = store.get(CUSTOMER_KEY, {});
    nameEl.value = customer.name || '';
    phoneEl.value = customer.phone || '';
    addressEl.value = customer.address || '';

    function buildWhatsAppMessage(pdfUrl) {
        const lines = Object.values(cart).map((item, i) =>
            `${i + 1}) ${item.name}\n• Barcode: #${item.barcode}\n• Qty: ${item.qty}\n• Image: ${item.image}`
        );
        let message = `${orderMessage}\n\n`;
        message += `Order #: ${orderNumber}\n`;
        if (pdfUrl) message += `PDF: ${pdfUrl}\n`;
        if (nameEl.value.trim()) message += `Customer: ${nameEl.value.trim()}\n`;
        if (phoneEl.value.trim()) message += `Phone: ${phoneEl.value.trim()}\n`;
        if (addressEl.value.trim()) message += `Address: ${addressEl.value.trim()}\n`;
        message += `---------------------------------\n`;
        message += `${lines.join('\n\n')}\n\n`;
        message += `---------------------------------\n`;
        message += `Total: ${totalPcs()} pcs`;
        return message;
    }

    const whatsappLink = (pdfUrl) =>
        `https://api.whatsapp.com/send?phone=${whatsappNumber}&text=${encodeURIComponent(buildWhatsAppMessage(pdfUrl))}`;

    function updateLinkAndPrint() {
        waBtn.href = whatsappLink(null);
        el('p-name').textContent = nameEl.value.trim() || '—';
        el('p-phone').textContent = phoneEl.value.trim() || '—';
        el('p-address').textContent = addressEl.value.trim() || '—';
        schedulePdf(); // keep the phone-share PDF in step with what's on screen
    }

    function render() {
        const items = Object.entries(cart);
        const pcs = totalPcs();
        el('head-pcs').textContent = pcs;
        document.querySelectorAll('.total-pcs').forEach(n => n.textContent = pcs);

        const empty = items.length === 0 && !sent;
        el('cart-empty').classList.toggle('hidden', !empty);
        el('cart-content').classList.toggle('hidden', empty);

        itemsEl.innerHTML = items.map(([id, item]) => `
            <div class="flex items-center gap-3 md:gap-4 p-3 md:p-4" data-id="${esc(id)}">
                <img src="${esc(item.thumb || item.image)}" alt="" class="w-20 h-24 md:w-24 md:h-28 rounded-xl object-cover bg-[#F1E7E0] flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="text-base md:text-lg font-extrabold text-black truncate">${esc(item.name)}</div>
                    <div class="text-xs text-gray-500 digits-en mt-0.5">BC: ${esc(item.barcode)}</div>
                    <div class="flex items-center gap-3 mt-2">
                        <div class="flex items-center gap-1.5 h-9 px-2.5 rounded-full bg-maroon text-white ${sent ? 'opacity-60 pointer-events-none' : ''}">
                            <button type="button" class="qty-minus text-lg leading-none font-semibold px-1" aria-label="-">−</button>
                            <input type="text" inputmode="numeric" maxlength="4" value="${item.qty}" aria-label="${esc(t.quantity)}"
                                   class="qty-value w-10 h-6 rounded-md bg-white/15 focus:bg-white focus:text-maroon text-center text-sm font-semibold digits-en border-0 outline-none p-0">
                            <button type="button" class="qty-plus text-lg leading-none font-semibold px-1" aria-label="+">+</button>
                        </div>
                        ${sent ? '' : `<button type="button" class="remove-btn text-xs text-red-600 underline">${esc(t.remove)}</button>`}
                    </div>
                </div>
            </div>`).join('');

        el('print-rows').innerHTML = items.map(([id, item], i) => `
            <tr>
                <td class="border border-gray-300 p-2 text-center digits-en">${i + 1}</td>
                <td class="border border-gray-300 p-1"><img src="${esc(item.thumb || item.image)}" alt="" class="w-12 h-14 object-cover rounded mx-auto"></td>
                <td class="border border-gray-300 p-2 font-semibold">${esc(item.name)}</td>
                <td class="border border-gray-300 p-2 digits-en">${esc(item.barcode)}</td>
                <td class="border border-gray-300 p-2 text-center font-bold digits-en">${item.qty}</td>
            </tr>`).join('');

        waBtn.classList.toggle('opacity-50', sent);
        waBtn.classList.toggle('pointer-events-none', sent);
        updateLinkAndPrint();
    }

    function setQty(id, q) {
        if (sent || !cart[id]) return;
        q = Math.max(0, q);
        if (q > 0) cart[id].qty = q; else delete cart[id];
        store.set(CART_KEY, cart);
        render();
    }

    // Quantity controls (event delegation, items are re-rendered)
    itemsEl.addEventListener('click', function (e) {
        const row = e.target.closest('[data-id]');
        if (!row) return;
        const id = row.dataset.id;
        if (e.target.closest('.qty-plus')) setQty(id, cart[id].qty + 1);
        else if (e.target.closest('.qty-minus')) setQty(id, cart[id].qty - 1);
        else if (e.target.closest('.remove-btn')) setQty(id, 0);
    });
    itemsEl.addEventListener('focusin', (e) => { if (e.target.matches('.qty-value')) e.target.select(); });
    itemsEl.addEventListener('keydown', (e) => { if (e.target.matches('.qty-value') && e.key === 'Enter') e.target.blur(); });
    itemsEl.addEventListener('change', function (e) {
        if (!e.target.matches('.qty-value')) return;
        const digits = e.target.value
            .replace(/[٠-٩]/g, d => d.charCodeAt(0) - 0x0660)
            .replace(/[۰-۹]/g, d => d.charCodeAt(0) - 0x06F0)
            .replace(/\D/g, '');
        setQty(e.target.closest('[data-id]').dataset.id, parseInt(digits, 10) || 0);
    });

    [nameEl, phoneEl, addressEl].forEach(input => input.addEventListener('input', function () {
        store.set(CUSTOMER_KEY, { name: nameEl.value, phone: phoneEl.value, address: addressEl.value });
        updateLinkAndPrint();
    }));

    // ---- Order PDF: render the print sheet to an A4 PDF in the browser ----
    async function makePdfBlob() {
        const wrap = document.createElement('div');
        wrap.dir = document.documentElement.dir;
        wrap.style.cssText = 'position:fixed;left:-10000px;top:0;width:794px;padding:36px;background:#fff;';
        const clone = el('print-sheet').cloneNode(true);
        clone.removeAttribute('id');
        clone.classList.remove('hidden');
        wrap.appendChild(clone);
        document.body.appendChild(wrap);
        try {
            await Promise.all([...wrap.querySelectorAll('img')].map(img =>
                img.complete ? null : new Promise(r => { img.onload = img.onerror = r; })));
            const canvas = await html2canvas(wrap, { scale: 2, useCORS: true, backgroundColor: '#ffffff' });
            const pdf = new window.jspdf.jsPDF({ unit: 'pt', format: 'a4' });
            const pageW = pdf.internal.pageSize.getWidth();
            const pageH = pdf.internal.pageSize.getHeight();
            const imgH = canvas.height * pageW / canvas.width;
            const img = canvas.toDataURL('image/jpeg', 0.85);
            for (let y = 0; y < imgH; y += pageH) {
                if (y > 0) pdf.addPage();
                pdf.addImage(img, 'JPEG', 0, -y, pageW, imgH);
            }
            return pdf.output('blob');
        } finally {
            wrap.remove();
        }
    }

    async function uploadPdf(blob) {
        const form = new FormData();
        form.append('pdf', blob, `${orderNumber}.pdf`);
        form.append('order_number', orderNumber);
        const res = await fetch(pdfEndpoint, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: form,
        });
        if (!res.ok) throw new Error('PDF upload failed: ' + res.status);
        return (await res.json()).url;
    }

    // WhatsApp: make + upload the PDF, then open WhatsApp with its link in the message.
    // If the PDF fails for any reason, the order is still sent without it.
    // After sending, the stored cart is cleared (no duplicates) but this page stays
    // as-is so the order can still be printed.
    let busy = false;
    const waLabel = waBtn.innerHTML;

    function setBusy(text) {
        busy = !!text;
        if (text) waBtn.textContent = text; else waBtn.innerHTML = waLabel;
        waBtn.classList.toggle('opacity-70', busy);
        waBtn.classList.toggle('pointer-events-none', busy);
    }

    function markSent() {
        sent = true;
        [CART_KEY, ORDER_NO_KEY].forEach(store.remove);
        el('sent-notice').classList.remove('hidden');
        render();
    }

    // Phones: share the PDF file itself through the system share sheet
    // (the customer picks WhatsApp and the store chat). WhatsApp links can't carry files.
    const canShareFiles = (() => {
        try {
            return window.matchMedia('(pointer: coarse)').matches && !!navigator.canShare
                && navigator.canShare({ files: [new File(['x'], 'x.pdf', { type: 'application/pdf' })] });
        } catch (e) { return false; }
    })();

    // The share sheet must open right after the tap, so the PDF is prepared in advance
    // and rebuilt (debounced) whenever the order or customer details change.
    let pdfVersion = 0, readyPdf = null, pdfTimer = null;
    const toFile = (blob) => new File([blob], `${orderNumber}.pdf`, { type: 'application/pdf' });

    function schedulePdf() {
        if (!canShareFiles || sent) return;
        const v = ++pdfVersion;
        readyPdf = null;
        clearTimeout(pdfTimer);
        pdfTimer = setTimeout(async function () {
            try {
                const blob = await makePdfBlob();
                if (v === pdfVersion) readyPdf = toFile(blob);
            } catch (err) { console.warn(err); }
        }, 800);
    }

    async function sharePdf() {
        if (!readyPdf) {
            // Not ready yet: build it now; the next tap opens the share sheet
            setBusy(t.preparing);
            const v = ++pdfVersion;
            clearTimeout(pdfTimer);
            try {
                const blob = await makePdfBlob();
                if (v === pdfVersion) readyPdf = toFile(blob);
            } catch (err) {
                console.warn(err);
                setBusy(null);
                return sendLink();
            }
            setBusy(null);
            waBtn.textContent = '📄 ' + t.sendPdf;
            return;
        }
        try {
            await navigator.share({ files: [readyPdf], text: `${orderMessage}\nOrder #: ${orderNumber}` });
            markSent();
        } catch (err) {
            if (err.name === 'AbortError') return; // share sheet closed without sending
            console.warn(err);
            sendLink();
        }
    }

    // Computers (and fallback): upload the PDF and open WhatsApp with its link.
    async function sendLink() {
        // Open the tab now, while the tap still counts as a user action (popup blockers)
        const win = window.open('', '_blank');
        setBusy(t.preparing);
        updateLinkAndPrint();
        let pdfUrl = null;
        try {
            pdfUrl = await uploadPdf(await makePdfBlob());
        } catch (err) {
            console.warn(err);
        }
        const url = whatsappLink(pdfUrl);
        setBusy(null);
        markSent();
        if (win && !win.closed) {
            win.location.href = url;
        } else {
            window.location.href = url; // in-app browsers that block new tabs
        }
    }

    waBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (sent || busy || !Object.keys(cart).length) return;
        if (canShareFiles) sharePdf(); else sendLink();
    });

    // Print / Save as PDF: the browser's print dialog offers "Save as PDF".
    el('print-btn').addEventListener('click', function () {
        updateLinkAndPrint();
        el('print-date').textContent = new Date().toLocaleString('en-GB', { dateStyle: 'medium', timeStyle: 'short' });
        const title = document.title;
        document.title = orderNumber; // suggested PDF file name
        window.print();
        setTimeout(() => { document.title = title; }, 1000);
    });

    // Returning here with the browser Back button: re-read the cart
    window.addEventListener('pageshow', function (e) {
        if (e.persisted && !sent) { cart = store.get(CART_KEY, {}); render(); }
    });

    render();
});
</script>
@endpush
