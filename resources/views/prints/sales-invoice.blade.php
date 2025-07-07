<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>فاتورة بيع</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            width: 58mm;
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
            margin-bottom: 10px;
        }

        .items th,
        .items td {
            border-bottom: 1px dashed #000;
            padding: 4px 0;
            text-align: right;
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
        <div><strong>رقم الفاتورة:</strong> {{ $record->invoice_number }}</div>
        <div><strong>التاريخ:</strong> {{ $record->date }}</div>
        <div><strong>نوع البيع:</strong> {{ $record->sale_type }}</div>
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
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><strong>الخصم:</strong> {{ number_format($record->discount, 2) }} ج</div>
        <div><strong>الضريبة:</strong> {{ number_format($record->tax, 2) }} ج</div>
        <div><strong>الإجمالي:</strong> {{ number_format($record->total, 2) }} ج</div>
    </div>

    <div class="footer">
        شكراً لتعاملكم معنا
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
