@extends('layouts.admin')
@section('title', $order->order_number)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-2 mb-4 print:hidden">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-maroon underline">← {{ __('orders') }}</a>
    <div class="flex flex-wrap gap-2">
        <button id="pdf-btn" type="button" class="inline-flex items-center gap-1 bg-maroon text-white text-sm rounded-lg px-4 py-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"/></svg>
            <span id="pdf-label">{{ __('download_pdf') }}</span>
        </button>
        <button type="button" onclick="window.print()" class="border border-maroon text-maroon text-sm rounded-lg px-4 py-2">{{ __('print') }}</button>
        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
              onsubmit="return confirm(@js(__('confirm_delete_order')));">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-red-600 border border-red-200 rounded-lg px-4 py-2">{{ __('delete') }}</button>
        </form>
    </div>
</div>

{{-- A4 order sheet: shown on screen, printed, and turned into the PDF --}}
<div class="overflow-x-auto">
<div id="order-sheet" class="bg-white text-black text-sm w-[794px] max-w-none p-9 mx-auto shadow-sm print:shadow-none print:p-0 print:w-auto">
    <div class="flex items-center justify-between border-b-2 border-maroon pb-3 mb-4">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}?v=2" alt="" class="w-16 h-16 rounded-full object-cover">
            <div class="text-lg font-extrabold text-maroon leading-tight">AL QAMAR<br><span class="text-xs font-semibold tracking-widest">GENERAL TRADING</span></div>
        </div>
        <div class="text-end">
            <div class="text-xs text-gray-600">{{ __('order_number') }}</div>
            <div class="font-bold font-mono" dir="ltr">{{ $order->order_number }}</div>
            <div class="text-xs text-gray-600 mt-1">{{ __('date') }}: <span dir="ltr">{{ $order->created_at->format('Y-m-d H:i') }}</span></div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-3 mb-4 text-xs">
        <div><div class="text-gray-500">{{ __('customer_name') }}</div><div class="font-semibold">{{ $order->customer_name ?: '—' }}</div></div>
        <div><div class="text-gray-500">{{ __('customer_phone') }}</div><div class="font-semibold" dir="ltr">{{ $order->customer_phone ?: '—' }}</div></div>
        <div><div class="text-gray-500">{{ __('customer_address') }}</div><div class="font-semibold whitespace-pre-line">{{ $order->customer_address ?: '—' }}</div></div>
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
        <tbody>
            @foreach ($order->items as $i => $item)
                <tr style="break-inside: avoid">
                    <td class="border border-gray-300 p-2 text-center">{{ $i + 1 }}</td>
                    <td class="border border-gray-300 p-1"><img src="{{ $item['image'] }}" alt="" class="w-12 h-14 object-cover rounded mx-auto"></td>
                    <td class="border border-gray-300 p-2 font-semibold">{{ $item['name'] }}</td>
                    <td class="border border-gray-300 p-2" dir="ltr">{{ $item['barcode'] }}</td>
                    <td class="border border-gray-300 p-2 text-center font-bold">{{ $item['qty'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-bold text-maroon">
                <td colspan="4" class="border border-gray-300 p-2 text-end">{{ __('total') }}</td>
                <td class="border border-gray-300 p-2 text-center">{{ $order->total_pcs }} {{ __('pcs') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
</div>

<style>
    @page { size: A4; margin: 12mm; }
    @media print {
        aside, main > div:not(.overflow-x-auto) { display: none !important; }
        body, main { background: #fff !important; padding: 0 !important; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
(function () {
    const fileName = @json($order->order_number . '.pdf');
    const btn = document.getElementById('pdf-btn');
    const label = document.getElementById('pdf-label');
    const idle = label.textContent;

    // Renders the sheet in the browser (keeps Arabic/Persian text shaped correctly)
    async function downloadPdf() {
        btn.disabled = true;
        label.textContent = @json(__('preparing_pdf'));
        try {
            const sheet = document.getElementById('order-sheet');
            await Promise.all([...sheet.querySelectorAll('img')].map(img =>
                img.complete ? null : new Promise(r => { img.onload = img.onerror = r; })));
            const canvas = await html2canvas(sheet, { scale: 2, useCORS: true, backgroundColor: '#ffffff' });
            const pdf = new window.jspdf.jsPDF({ unit: 'pt', format: 'a4' });
            const pageW = pdf.internal.pageSize.getWidth();
            const pageH = pdf.internal.pageSize.getHeight();
            const imgH = canvas.height * pageW / canvas.width;
            const img = canvas.toDataURL('image/jpeg', 0.9);
            for (let y = 0; y < imgH; y += pageH) {
                if (y > 0) pdf.addPage();
                pdf.addImage(img, 'JPEG', 0, -y, pageW, imgH);
            }
            pdf.save(fileName);
        } catch (err) {
            console.error(err);
            window.print(); // fallback: browser "Save as PDF"
        } finally {
            btn.disabled = false;
            label.textContent = idle;
        }
    }

    btn.addEventListener('click', downloadPdf);
    if (new URLSearchParams(location.search).has('pdf')) {
        window.addEventListener('load', downloadPdf);
    }
})();
</script>
@endsection
