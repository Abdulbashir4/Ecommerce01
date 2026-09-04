<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Invoice #{{ $order->order_id }} — Optimum Biomedical</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        .invoice {
            box-sizing: border-box;
            width: 210mm;
            height: 297mm;
            position: relative;
            overflow: hidden;
        }

        .invoice-content {
            padding: 10mm 12mm 28mm;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 1.8mm 2mm;
        }

        /*
         * নিচের ৩টি signature সবসময় A4-এর নিচে থাকবে।
         */
        .invoice-signatures {
            position: absolute;
            left: 12mm;
            right: 12mm;
            bottom: 9mm;
        }

        .invoice-words {
            margin-top: 5mm;
        }

        @media print {
            html,
            body {
                width: 210mm;
                height: 297mm;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                overflow: hidden !important;
            }

            .no-print {
                display: none !important;
            }

            .invoice {
                margin: 0 !important;
                box-shadow: none !important;
            }
        }

        @media screen {
            body {
                background: #f1f5f9;
                padding: 20px;
            }

            .invoice {
                margin: 0 auto;
                background: #fff;
                box-shadow: 0 20px 45px rgba(15, 23, 42, .15);
            }
        }
    </style>
</head>

<body class="text-slate-800">

<div class="no-print mx-auto mb-4 flex max-w-4xl justify-between">
    <a href="{{ url('/admin/sales') }}"
       class="rounded-xl border bg-white px-4 py-2 text-sm font-bold">
        ← New Sale
    </a>

    <button onclick="window.print()"
            class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white">
        Print Invoice
    </button>
</div>

<div class="invoice">

    <div class="invoice-content">

        {{-- HEADER --}}
        <div class="flex items-start justify-between gap-5 border-b-2 border-slate-900 pb-4">

            <div class="flex gap-3">

                <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-lg bg-slate-100 text-xl font-black text-indigo-600">

                    @if(!empty($company?->logo))

                        <img
                            src="{{ filter_var($company->logo, FILTER_VALIDATE_URL)
                                ? $company->logo
                                : (str_starts_with((string) $company->logo, 'uploads/')
                                    ? asset($company->logo)
                                    : asset('uploads/side_image/' . basename((string) $company->logo))) }}"
                            alt="{{ $company->company_name ?? 'Company Logo' }}"
                            class="h-full w-full object-contain"
                        >

                    @else
                        OB
                    @endif

                </div>

                <div>

                    <h1 class="text-xl font-black">
                        {{ $company->company_name ?: 'Optimum Biomedical' }}
                    </h1>

                    <p class="mt-1 max-w-md text-[10px] leading-4 text-slate-500">
                        {{ $company->address }}
                    </p>

                    <p class="text-[10px] text-slate-500">
                        {{ $company->phone }}

                        @if($company->email)
                            · {{ $company->email }}
                        @endif
                    </p>

                </div>

            </div>

            <div class="text-right">

                <div class="text-[9px] font-black uppercase tracking-widest text-slate-400">
                    Invoice No
                </div>

                <div class="text-xl font-black">
                    #{{ $order->order_id }}
                </div>

                <div class="text-[10px] text-slate-500">
                    {{ $order->created_at?->format('d M Y, h:i A') }}
                </div>

            </div>

        </div>


        {{-- CUSTOMER / PAYMENT --}}
        <div class="grid grid-cols-2 gap-4 border-b border-slate-200 py-3">

            <div>

                <div class="text-[9px] font-black uppercase tracking-widest text-slate-400">
                    Bill To
                </div>

                <div class="mt-1 text-[11px] font-black">
                    {{ $order->customer_name ?: 'Walk-in Customer' }}
                </div>

                <div class="text-[10px] text-slate-500">
                    {{ $order->address }}
                </div>

                <div class="text-[10px] text-slate-500">
                    {{ $order->phone }}
                </div>

                <div class="text-[10px] text-slate-500">
                    {{ $order->email }}
                </div>

            </div>

            <div class="text-right">

                <div class="text-[9px] font-black uppercase tracking-widest text-slate-400">
                    Payment
                </div>

                <div class="mt-1 text-[11px] font-bold">
                    {{ $order->payment_method }}
                </div>

                <div class="text-[10px] font-bold {{ $order->payment_status === 'Paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $order->payment_status }}
                </div>

            </div>

        </div>


        {{-- ITEMS --}}
        @php($subtotal = 0)

        <table class="invoice-table mt-3 w-full border-collapse text-[10px]">

            <thead>

                <tr class="border-y border-slate-300 bg-slate-50 text-[8px] font-black uppercase tracking-wider">

                    <th class="text-left">#</th>

                    <th class="text-left">
                        Description
                    </th>

                    <th class="text-center">
                        Qty
                    </th>

                    <th class="text-right">
                        Unit Price
                    </th>

                    <th class="text-right">
                        Discount
                    </th>

                    <th class="text-right">
                        Total
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-slate-100">

                @foreach($order->items as $i => $item)

                    @php
                        $gross = (float) $item->price * (int) $item->qty;
                        $line = (float) $item->total;
                        $itemDiscount = max(0, $gross - $line);
                        $subtotal += $gross;
                    @endphp

                    <tr>

                        <td>
                            {{ $i + 1 }}
                        </td>

                        <td class="font-semibold">
                            {{ $item->product_name }}
                        </td>

                        <td class="text-center">
                            {{ $item->qty }}
                        </td>

                        <td class="text-right">
                            ৳ {{ number_format($item->price, 2) }}
                        </td>

                        <td class="text-right">
                            ৳ {{ number_format($itemDiscount, 2) }}
                        </td>

                        <td class="text-right font-bold">
                            ৳ {{ number_format($line, 2) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>


            {{-- TOTAL --}}
            <tfoot class="font-bold">

                <tr>

                    <td colspan="4"></td>

                    <td class="text-right text-slate-500">
                        Sub Total
                    </td>

                    <td class="text-right">
                        ৳ {{ number_format($subtotal, 2) }}
                    </td>

                </tr>


                <tr>

                    <td colspan="4"></td>

                    <td class="text-right text-slate-500">
                        Total Discount
                    </td>

                    <td class="text-right">
                        ৳ {{ number_format(
                            max(0, $subtotal - (float) $order->total_amount),
                            2
                        ) }}
                    </td>

                </tr>


                <tr class="border-t-2 border-slate-900 text-[11px]">

                    <td colspan="4"></td>

                    <td class="text-right">
                        Grand Total
                    </td>

                    <td class="text-right">
                        ৳ {{ number_format($order->total_amount, 2) }}
                    </td>

                </tr>


                <tr>

                    <td colspan="4"></td>

                    <td class="text-right text-slate-500">
                        Paid
                    </td>

                    <td class="text-right">
                        ৳ {{
                            $order->payment_status === 'Paid'
                            ? number_format($order->total_amount, 2)
                            : '0.00'
                        }}
                    </td>

                </tr>


                <tr>

                    <td colspan="4"></td>

                    <td class="text-right text-slate-500">
                        Due
                    </td>

                    <td class="text-right">
                        ৳ {{
                            $order->payment_status === 'Paid'
                            ? '0.00'
                            : number_format($order->total_amount, 2)
                        }}
                    </td>

                </tr>

            </tfoot>

        </table>


        {{-- WORDS --}}
        <div class="invoice-words text-[10px] font-bold">
            In Words:
            {{ number_format($order->total_amount, 2) }}
            Taka Only
        </div>

    </div>


    {{-- ALWAYS BOTTOM --}}
    <div class="invoice-signatures grid grid-cols-3 gap-8 text-center text-[9px] font-bold text-slate-500">

        <div>
            <div class="mx-auto mb-1 w-28 border-t border-slate-400"></div>
            For Customer
        </div>

        <div>
            <div class="mx-auto mb-1 w-28 border-t border-slate-400"></div>
            Prepared By
        </div>

        <div>
            <div class="mx-auto mb-1 w-28 border-t border-slate-400"></div>
            For Authority
        </div>

    </div>

</div>


<script>

function fitInvoiceToOnePage() {

    const sheet =
        document.getElementById('invoicePreview');

    const content =
        document.getElementById('invoiceMainContent');

    const footer =
        document.getElementById('invoiceSignatures');

    if (!sheet || !content || !footer) {
        return;
    }


    /*
     * প্রথমে scale reset।
     */
    content.style.transform =
        'scale(1)';


    /*
     * Footer-এর top position।
     */
    const footerTop =
        footer.offsetTop;


    /*
     * Content-এর আসল height।
     */
    const naturalHeight =
        content.scrollHeight;


    /*
     * Footer-এর জন্য জায়গা বাদ দিয়ে
     * available height।
     */
    const availableHeight =
        footerTop - 15;


    if (
        naturalHeight > availableHeight &&
        availableHeight > 0
    ) {

        let scale =
            availableHeight / naturalHeight;


        /*
         * খুব ছোট হলেও page-এর বাইরে
         * যেতে দেওয়া হবে না।
         */
        scale =
            Math.max(
                0.35,
                Math.min(1, scale)
            );


        content.style.transform =
            'scale(' +
            scale.toFixed(4) +
            ')';
    }
}


/*
 * Browser screen load হওয়ার পরে।
 */
window.addEventListener('load', function () {

    setTimeout(
        fitInvoiceToOnePage,
        300
    );

});


/*
 * Print করার ঠিক আগে আবার calculate করবে।
 */
window.addEventListener(
    'beforeprint',
    fitInvoiceToOnePage
);

</script>

</body>
</html>