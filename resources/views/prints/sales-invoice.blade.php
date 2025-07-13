<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            width: 85mm;
            height: 180mm;
            margin: 0 auto;
            padding: 10px;
            background: #fff;
            font-size: 13px;
            direction: rtl;
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo img {
            height: 60px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }

        .info {
            margin-bottom: 10px;
        }

        .info div {
            margin-bottom: 4px;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* مهمة جداً لتقسيم الأعمدة بالتساوي */
        }

        .items th,
        .items td {
            border-bottom: 1px dashed #000;
            padding: 4px 2px;
            text-align: right;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            font-size: 13px;
        }

        /* عرض مخصص لكل عمود */
        .items th:nth-child(1),
        .items td:nth-child(1) {
            width: 25%;
            /* اسم المنتج */
        }

        .items th:nth-child(2),
        .items td:nth-child(2) {
            width: 23%;
            /* الكمية */
            text-align: center;
        }

        .items th:nth-child(3),
        .items td:nth-child(3) {
            width: 23%;
            /* السعر */
            text-align: center;
        }

        .items th:nth-child(4),
        .items td:nth-child(4) {
            width: 29%;
            /* الإجمالي */
            text-align: center;
        }

        .totals {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        .totals div {
            margin-bottom: 4px;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
        }

        .info {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        @media print {
            body {
                width: auto;
            }
        }
    </style>
</head>

<body>

    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="لوجو" width="60px" height="30px">
    </div>

    <div class="title">فاتورة بيع</div>

    <div class="info">
        <div><strong>رقم الفاتورة:</strong> {{ $record->id }}</div>
        <div><strong>التاريخ:</strong> {{ $record->date }}</div>
        <div><strong>نوع البيع:</strong> كاش</div>
        @if ($record->customer)
            <div><strong>العميل:</strong> {{ $record->customer->name }}</div>
        @endif
        <div><strong>الموظف:</strong> {{ $record->user->name ?? '---' }}</div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->items as $item)
                <tr>
                    <td colspan="1" style="word-break: break-word; white-space: normal; max-width: 40mm;">
                        {{ $item->product_name }}
                    </td>
                    <td colspan="1">{{ (int) $item->quantity }}</td>
                    <td colspan="1">{{ number_format($item->price, 0) }}</td>
                    <td colspan="1">{{ number_format($item->total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><strong>الخصم:</strong> {{ number_format($record->discount, 0) }} ج</div>
        <div><strong>الضريبة:</strong> {{ number_format($record->tax, 0) }} ج</div>
        <div><strong>الإجمالي:</strong> {{ number_format($record->total, 0) }} ج</div>
    </div>

    {{-- location and phone number section --}}

    <div class="info">
        <div><strong>العنوان:</strong> 121 ش صعب صالح بجوار ملك الفراولة وامام سيد حنفى - عين شمس القاهرة </div>
        <div><strong>رقم الموبايل:</strong> 01111024128</div>
    </div>

    <div class="footer">
        سعداء بزيارتكم لنا
    </div>


    <script>
        window.print();
    </script>
</body>

</html>
