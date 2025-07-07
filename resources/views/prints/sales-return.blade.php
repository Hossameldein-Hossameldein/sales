<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مرتجع بيع</title>
    <style>
        body { font-family: 'Arial', sans-serif; direction: rtl; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        .header { text-align: center; font-size: 24px; font-weight: bold; }
        .meta { margin-top: 20px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">مرتجع بيع</div>

    <div class="meta">
        <p>رقم المرتجع: {{ $return->id }}</p>
        <p>رقم الفاتورة الأصلية: {{ $return->invoice->invoice_number }}</p>
        <p>التاريخ: {{ $return->created_at->format('Y-m-d H:i') }}</p>
        <p>تم بواسطة: {{ $return->user->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>المنتج</th>
                <th>الباركود</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($return->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->barcode }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="meta">
        <p><strong>إجمالي المرتجع: {{ number_format($return->total, 2) }} جنيه</strong></p>
        @if ($return->notes)
            <p>السبب: {{ $return->notes }}</p>
        @endif
    </div>
</body>
</html>
