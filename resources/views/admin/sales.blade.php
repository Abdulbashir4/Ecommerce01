@php
    $invoiceCompany = $company ?? \App\Models\CompanyInfo::query()->first();
@endphp
@extends('layouts.admin')

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Point of Sale</p>
            <h1 class="text-3xl font-black tracking-tight text-slate-950">Manual Invoice</h1>
            <p class="mt-1 text-sm text-slate-500">Enter the information manually. Nothing from this sale is saved to the database.</p>
        </div>
    </div>

    {{-- ALL INFORMATION ABOVE --}}
    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-5 sm:px-7">
            <h2 class="text-lg font-black text-slate-950">Invoice Information</h2>
            <p class="mt-1 text-xs text-slate-500">Customer and product information</p>
        </div>

        <form id="manualInvoiceForm" method="POST" action="{{ route('admin.sales.store') }}" class="space-y-6 p-5 sm:p-7">
            @csrf

            <input type="hidden" name="invoice_number" id="invoiceNumber">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <label class="block">
                    <span class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500">Customer Name</span>
                    <input name="customer_name" id="customerName" value="{{ old('customer_name') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50"
                           placeholder="Customer name">
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500">Phone</span>
                    <input name="phone" id="customerPhone" value="{{ old('phone') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50"
                           placeholder="01XXXXXXXXX">
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500">Email</span>
                    <input type="email" name="email" id="customerEmail" value="{{ old('email') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50"
                           placeholder="customer@example.com">
                </label>
            </div>

            <label class="block">
                <span class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500">Address</span>
                <textarea name="address" id="customerAddress" rows="2"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50"
                          placeholder="Customer address">{{ old('address') }}</textarea>
            </label>

            <div class="overflow-hidden rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between bg-slate-950 px-4 py-3 text-white">
                    <div>
                        <span class="text-sm font-black">Products</span>
                        <span id="itemCount" class="ml-2 rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-bold">0</span>
                    </div>
                    <button type="button" id="addProduct"
                            class="rounded-lg bg-white px-3 py-2 text-xs font-black text-slate-900 hover:bg-indigo-50">
                        <i class="fa-solid fa-plus mr-1"></i>Add Product
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-[900px] w-full text-sm">
                        <thead class="bg-slate-50 text-[10px] font-black uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="p-3 text-left">Product Name</th>
                            <th class="w-32 p-3 text-left">Price</th>
                            <th class="w-24 p-3 text-left">Qty</th>
                            <th class="w-32 p-3 text-left">Discount</th>
                            <th class="w-36 p-3 text-right">Total</th>
                            <th class="w-12 p-3"></th>
                        </tr>
                        </thead>
                        <tbody id="saleItems" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>

                <div id="emptyItems" class="px-5 py-10 text-center text-sm text-slate-400">
                    <i class="fa-solid fa-box-open mb-2 text-2xl"></i>
                    <p>Add a product.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <label class="block">
                    <span class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500">Invoice Discount</span>
                    <input type="number" min="0" step="0.01" name="discount" id="invoiceDiscount" value="0"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-right font-bold outline-none focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50">
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500">Paid Amount</span>
                    <input type="number" min="0" step="0.01" name="paid" id="paid" value="0"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-right font-bold outline-none focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50">
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500">Payment Method</span>
                    <select name="payment_method" id="paymentMethod"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50">
                        <option value="Cash">Cash</option>
                        <option value="COD">COD</option>
                        <option value="Bkash">Bkash</option>
                        <option value="Nagad">Nagad</option>
                        <option value="Card">Card</option>
                        <option value="Bank">Bank</option>
                    </select>
                </label>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">

                <button type="button" id="resetSale"
                        class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-6 py-3 text-sm font-black text-white shadow-lg shadow-indigo-200 hover:-translate-y-0.5">
                   <i class="fa-solid fa-rotate-left mr-2"></i>Clear
                </button>
            </div>
        </form>
    </section>

    {{-- PREVIEW BELOW ALL INPUTS --}}
    <section class="rounded-3xl border border-slate-200 bg-slate-100/70 p-4 shadow-inner sm:p-6">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600">Live Preview</p>
                <h2 class="text-xl font-black text-slate-950">Invoice Preview</h2>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button" id="previewBtn"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-black text-slate-700 shadow-sm hover:bg-slate-50">
                    <i class="fa-solid fa-eye mr-1.5"></i>Preview
                </button>
                <button type="button" id="printBtn"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-black text-white hover:bg-slate-800">
                    <i class="fa-solid fa-print mr-1.5"></i>Print
                </button>
                <button type="button" id="downloadBtn"
                        class="rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-black text-white hover:bg-indigo-700">
                    <i class="fa-solid fa-download mr-1.5"></i>Download PDF
                </button>
            </div>
        </div>

        <div id="previewArea" class="overflow-x-auto rounded-2xl bg-slate-200 p-3 sm:p-6">
            <div id="invoicePreview" class="mx-auto w-[794px] min-h-[1123px] bg-white px-[45px] py-[50px] text-[13px] text-slate-950 shadow-2xl">

                <div class="grid grid-cols-3 items-start">
    {{-- LEFT: COMPANY INFORMATION --}}
    <div>
        <div class="text-[17px] font-black">
            {{ $invoiceCompany?->company_name ?? '' }}
        </div>
        <div class="mt-1 max-w-[240px] text-[12px] leading-5">
            {{ $invoiceCompany?->address ?? '' }}
        </div>
        <div class="text-[12px] leading-5">
            {{ $invoiceCompany?->phone ?? '' }}
        </div>
        @if(!empty($invoiceCompany?->email))
            <div class="text-[12px] leading-5">
                {{ $invoiceCompany->email }}
            </div>
        @endif
    </div>

    {{-- CENTER: COMPANY LOGO --}}
    <div class="flex items-center justify-center pt-2">
        @if(!empty($invoiceCompany?->logo))
            <img
                src="{{ filter_var($invoiceCompany->logo, FILTER_VALIDATE_URL) ? $invoiceCompany->logo : (str_starts_with((string) $invoiceCompany->logo, 'uploads/') ? asset($invoiceCompany->logo) : asset('uploads/side_image/' . basename((string) $invoiceCompany->logo))) }}"
                alt="{{ $invoiceCompany->company_name ?? 'Company Logo' }}"
                class="h-24 w-48 object-contain"
            >
        @endif
    </div>

    {{-- RIGHT: INVOICE INFORMATION --}}
    <div class="text-left text-[12px] leading-5">
        <div class="font-bold">Invoice No:</div>
        <div id="previewInvoiceNumber" class="text-[15px] font-black">
            INV-{{ now()->format('Ymd-His') }}
        </div>
        <div class="mt-1">
            Date:
            <span id="previewDate">
                {{ now()->format('d M Y, h:i A') }}
            </span>
        </div>
    </div>
</div>

{{-- TWO HORIZONTAL LINES BELOW EMAIL --}}
<div class="mt-3 w-full space-y-1">
    <div class="h-px w-full bg-slate-700"></div>
    <div class="h-px w-full bg-slate-700"></div>
</div>

                {{-- CUSTOMER AT THE BOTTOM OF THE INVOICE, ABOVE SIGNATURES --}}
                <div class="mt-4">
                    <div class="grid grid-cols-2 border border-slate-700 text-[12px]">
                        <div class="border-r border-slate-700 p-3">
                            <div class="mb-2 font-black uppercase tracking-wide">Customer</div>
                            <div>Name: <span id="pName">---</span></div>
                            <div>Phone: <span id="pPhone">---</span></div>
                            <div>Email: <span id="pEmail">---</span></div>
                            <div>Address: <span id="pAddress">---</span></div>
                        </div>
                        <div class="p-3">
                            <div class="mb-2 font-black uppercase tracking-wide">Payment</div>
                            <div>Method: <span id="pPayment">Cash</span></div>
                            <div>Status: <span id="pStatus">Unpaid</span></div>
                        </div>
                    </div>
                </div>

                <div class="mt-7">
                    <table class="w-full border-collapse border border-slate-700 text-[12px]">
                        <thead>
                        <tr class="bg-slate-100">
                            <th class="border border-slate-700 p-2 text-center">#</th>
                            <th class="border border-slate-700 p-2 text-left">Description</th>
                            <th class="border border-slate-700 p-2 text-center">Qty</th>
                            <th class="border border-slate-700 p-2 text-right">Unit Price</th>
                            <th class="border border-slate-700 p-2 text-right">Discount</th>
                            <th class="border border-slate-700 p-2 text-right">Total</th>
                        </tr>
                        </thead>
                        <tbody id="previewItems"></tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="border border-slate-700"></td>
                            <td class="border border-slate-700 p-2 font-bold">Sub Total</td>
                            <td id="pSubtotal" class="border border-slate-700 p-2 text-right font-bold">৳ 0.00</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="border border-slate-700"></td>
                            <td class="border border-slate-700 p-2 font-bold">Total Discount</td>
                            <td id="pDiscount" class="border border-slate-700 p-2 text-right font-bold">৳ 0.00</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="border border-slate-700"></td>
                            <td class="border border-slate-700 p-2 text-base font-black">Grand Total</td>
                            <td id="pGrand" class="border border-slate-700 p-2 text-right text-base font-black">৳ 0.00</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="border border-slate-700"></td>
                            <td class="border border-slate-700 p-2 font-bold">Paid</td>
                            <td id="pPaid" class="border border-slate-700 p-2 text-right font-bold">৳ 0.00</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="border border-slate-700"></td>
                            <td class="border border-slate-700 p-2 font-bold">Due</td>
                            <td id="pDue" class="border border-slate-700 p-2 text-right font-bold">৳ 0.00</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-6 text-[12px] font-bold">
                    In Words: <span id="pWords">Zero</span> Taka Only
                </div>

                {{-- SIGNATURES AT THE VERY BOTTOM --}}
                <div class="mt-[360px] grid grid-cols-3 gap-10 text-center text-[12px] font-bold">
                    <div>
                        <div class="mx-auto mb-2 w-32 border-t border-slate-700"></div>
                        Customer Signature
                    </div>
                    <div>
                        <div class="mx-auto mb-2 w-32 border-t border-slate-700"></div>
                        Prepared By
                    </div>
                    <div>
                        <div class="mx-auto mb-2 w-32 border-t border-slate-700"></div>
                        Authorized Signature
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const rows = document.getElementById('saleItems');
    const form = document.getElementById('manualInvoiceForm');
    const empty = document.getElementById('emptyItems');
    const invoiceNumberInput = document.getElementById('invoiceNumber');

    let rowIndex = 0;

    function makeInvoiceNumber() {
        const d = new Date();
        const pad = n => String(n).padStart(2, '0');
        return `INV-${d.getFullYear()}${pad(d.getMonth()+1)}${pad(d.getDate())}-${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
    }

    const invoiceNumber = makeInvoiceNumber();
    invoiceNumberInput.value = invoiceNumber;
    document.getElementById('previewInvoiceNumber').textContent = invoiceNumber;

    function money(v) {
        return '৳ ' + Number(v || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function esc(v) {
        return String(v ?? '').replace(/[&<>'"]/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'
        }[c]));
    }

    function words(n) {
        n = Math.floor(Number(n) || 0);
        if (!n) return 'Zero';

        const one = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
        const ten = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];

        function h(x) {
            let s = '';
            if (x >= 100) { s += one[Math.floor(x/100)] + ' Hundred '; x %= 100; }
            if (x >= 20) { s += ten[Math.floor(x/10)] + ' '; x %= 10; }
            if (x) s += one[x] + ' ';
            return s.trim();
        }

        let s = '';
        if (n >= 10000000) { s += h(Math.floor(n/10000000)) + ' Crore '; n %= 10000000; }
        if (n >= 100000) { s += h(Math.floor(n/100000)) + ' Lakh '; n %= 100000; }
        if (n >= 1000) { s += h(Math.floor(n/1000)) + ' Thousand '; n %= 1000; }
        if (n) s += h(n);
        return s.trim();
    }

    function sync() {
        empty.classList.toggle('hidden', rows.children.length > 0);
        document.getElementById('itemCount').textContent = rows.children.length;
    }

    function addRow() {
        const i = rowIndex++;
        const tr = document.createElement('tr');
        tr.className = 'sale-row';

        tr.innerHTML = `
            <td class="p-3">
                <input type="text" name="items[${i}][product_name]" class="product-name w-full min-w-[300px] rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50" placeholder="Product name">
            </td>
            <td class="p-3">
                <input type="number" min="0" step="0.01" name="items[${i}][price]" value="0" class="price w-full rounded-lg border border-slate-200 px-2 py-2 text-right text-xs font-bold outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
            </td>
            <td class="p-3">
                <input type="number" min="1" step="1" name="items[${i}][qty]" value="1" class="qty w-full rounded-lg border border-slate-200 px-2 py-2 text-right text-xs font-bold outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
            </td>
            <td class="p-3">
                <input type="number" min="0" step="0.01" name="items[${i}][discount]" value="0" class="item-discount w-full rounded-lg border border-slate-200 px-2 py-2 text-right text-xs font-bold outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
            </td>
            <td class="row-total p-3 text-right font-black">৳ 0.00</td>
            <td class="p-3 text-center">
                <button type="button" class="remove-row grid h-8 w-8 place-items-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        `;

        rows.appendChild(tr);

        tr.querySelectorAll('input').forEach(x => x.addEventListener('input', calculate));
        tr.querySelector('.remove-row').addEventListener('click', () => {
            tr.remove();
            sync();
            calculate();
        });

        sync();
        calculate();
    }

    function calculate() {
        let subtotal = 0;
        let itemDiscount = 0;
        let number = 1;

        const preview = document.getElementById('previewItems');
        preview.innerHTML = '';

        rows.querySelectorAll('.sale-row').forEach(row => {
            const name = row.querySelector('.product-name').value.trim();
            const price = Math.max(0, Number(row.querySelector('.price').value) || 0);
            const qty = Math.max(0, Number(row.querySelector('.qty').value) || 0);
            const discount = Math.max(0, Number(row.querySelector('.item-discount').value) || 0);

            const gross = price * qty;
            const lineDiscount = Math.min(discount, gross);
            const total = Math.max(0, gross - lineDiscount);

            subtotal += gross;
            itemDiscount += lineDiscount;

            row.querySelector('.row-total').textContent = money(total);

            if (name) {
                preview.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td class="border border-slate-700 p-2 text-center">${number++}</td>
                        <td class="border border-slate-700 p-2">${esc(name)}</td>
                        <td class="border border-slate-700 p-2 text-center">${qty}</td>
                        <td class="border border-slate-700 p-2 text-right">${money(price)}</td>
                        <td class="border border-slate-700 p-2 text-right">${money(lineDiscount)}</td>
                        <td class="border border-slate-700 p-2 text-right font-bold">${money(total)}</td>
                    </tr>
                `);
            }
        });

        const invoiceDiscount = Math.max(0, Number(document.getElementById('invoiceDiscount').value) || 0);
        const appliedInvoiceDiscount = Math.min(invoiceDiscount, Math.max(0, subtotal - itemDiscount));
        const grand = Math.max(0, subtotal - itemDiscount - appliedInvoiceDiscount);
        const paid = Math.min(Math.max(0, Number(document.getElementById('paid').value) || 0), grand);
        const due = Math.max(0, grand - paid);

        document.getElementById('pSubtotal').textContent = money(subtotal);
        document.getElementById('pDiscount').textContent = money(itemDiscount + appliedInvoiceDiscount);
        document.getElementById('pGrand').textContent = money(grand);
        document.getElementById('pPaid').textContent = money(paid);
        document.getElementById('pDue').textContent = money(due);
        document.getElementById('pWords').textContent = words(grand);

        document.getElementById('pName').textContent = document.getElementById('customerName').value.trim() || '---';
        document.getElementById('pPhone').textContent = document.getElementById('customerPhone').value.trim() || '---';
        document.getElementById('pEmail').textContent = document.getElementById('customerEmail').value.trim() || '---';
        document.getElementById('pAddress').textContent = document.getElementById('customerAddress').value.trim() || '---';
        document.getElementById('pPayment').textContent = document.getElementById('paymentMethod').value;
        document.getElementById('pStatus').textContent = paid >= grand && grand > 0 ? 'Paid' : 'Unpaid';
    }

    document.getElementById('addProduct').addEventListener('click', addRow);

    ['customerName','customerPhone','customerEmail','customerAddress','paymentMethod','invoiceDiscount','paid']
        .forEach(id => {
            document.getElementById(id).addEventListener('input', calculate);
            document.getElementById(id).addEventListener('change', calculate);
        });

    document.getElementById('resetSale').addEventListener('click', () => {
        form.reset();
        rows.innerHTML = '';
        rowIndex = 0;
        const next = makeInvoiceNumber();
        invoiceNumberInput.value = next;
        document.getElementById('previewInvoiceNumber').textContent = next;
        addRow();
        calculate();
    });

    form.addEventListener('submit', e => {
        const saleRows = [...rows.querySelectorAll('.sale-row')];

        if (!saleRows.length) {
            e.preventDefault();
            alert('Please add at least one product.');
            return;
        }

        const invalid = saleRows.some(row => {
            return !row.querySelector('.product-name').value.trim()
                || Number(row.querySelector('.price').value) <= 0
                || Number(row.querySelector('.qty').value) <= 0;
        });

        if (invalid) {
            e.preventDefault();
            alert('Please enter Product Name, Price and Qty for every item.');
        }
    });

    document.getElementById('previewBtn').addEventListener('click', () => {
        calculate();
        document.getElementById('previewArea').scrollIntoView({behavior:'smooth', block:'start'});
    });

    function printInvoice() {
        calculate();

        const invoice = document.getElementById('invoicePreview').outerHTML;
        const w = window.open('', '_blank', 'width=900,height=1100');

        if (!w) {
            alert('Please allow pop-ups to print the invoice.');
            return;
        }

        w.document.write(`
            <!doctype html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>${invoiceNumber}</title>
                <script src="https://cdn.tailwindcss.com"><\/script>
            </head>
            <body class="m-0 bg-white">
                ${invoice}
                <script>
                    window.addEventListener('load', function(){
                        setTimeout(function(){ window.print(); }, 700);
                    });
                <\/script>
            </body>
            </html>
        `);

        w.document.close();
    }

    document.getElementById('printBtn').addEventListener('click', printInvoice);
    document.getElementById('downloadBtn').addEventListener('click', printInvoice);

    addRow();
    calculate();
})();
</script>
@endpush
