<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use App\Models\SalesInvoice;
use App\Models\SalesItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Expense;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ توليد صلاحيات Filament Shield بدون ما يسأل عن اسم البانل
        Artisan::call('shield:generate', ['--panel' => 'admin']);

        // ✅ إنشاء الأدوار
        $adminRole = Role::firstOrCreate(['name' => 'مدير']);
        $cashierRole = Role::firstOrCreate(['name' => 'كاشير']);
        $employeeRole = Role::firstOrCreate(['name' => 'موظف']);

        // ✅ ربط الصلاحيات بالأدوار
        $adminRole->syncPermissions(Permission::all());

        $cashierRole->syncPermissions(Permission::whereIn('name', [
            'view_product', 'view_sales_invoice', 'create_sales_invoice',
        ])->get());

        $employeeRole->syncPermissions(Permission::whereIn('name', [
            'view_product', 'create_product',
        ])->get());

        // ✅ إنشاء مستخدم مدير
        $admin = User::firstOrCreate(
            ['email' => 'admin@app.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('admin123456789'),
            ]
        );
        $admin->assignRole('مدير');

        // ✅ إنشاء تصنيفات
        $categories = ['أجهزة', 'اكسسوارات', 'مواد تنظيف'];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        // ✅ مورد
        $supplier = Supplier::firstOrCreate([
            'name' => 'شركة التوريدات الحديثة',
            'phone' => '0100000000',
            'address' => 'القاهرة',
        ]);

        // ✅ منتجات
        $products = [
            [
                'name' => 'موبايل سامسونج',
                'barcode' => '111',
                'category_id' => 1,
                'unit' => 'قطعة',
                'purchase_price' => 5000,
                'retail_price' => 6000,
                'wholesale_price' => 5800,
                'stock' => 10,
            ],
            [
                'name' => 'سماعة بلوتوث',
                'barcode' => '112',
                'category_id' => 2,
                'unit' => 'قطعة',
                'purchase_price' => 150,
                'retail_price' => 250,
                'wholesale_price' => 200,
                'stock' => 30,
            ],
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate($prod);
        }

        // ✅ إنشاء فاتورة شراء
        $purchaseInvoice = PurchaseInvoice::create([
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-1001',
            'payment_type' => 'كاش',
            'discount' => 50,
            'tax' => 100,
            'total' => 10100,
            'notes' => 'فاتورة شراء تجريبية',
        ]);

        foreach (Product::all() as $product) {
            PurchaseItem::create([
                'purchase_invoice_id' => $purchaseInvoice->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_barcode' => $product->barcode,
                'purchase_price' => $product->purchase_price,
                'retail_price' => $product->retail_price,
                'wholesale_price' => $product->wholesale_price,
                'quantity' => 5,
                'total' => $product->purchase_price * 5,
            ]);
            $product->increment('stock', 5);
        }

        // ✅ إنشاء فاتورة بيع
        $salesInvoice = SalesInvoice::create([
            'invoice_number' => 'SAL-1001',
            'sale_type' => 'قطاعي',
            'discount' => 0,
            'tax' => 0,
            'total' => 12000,
            'notes' => 'فاتورة بيع تجريبية',
            'user_id' => $admin->id,
        ]);

        foreach (Product::all() as $product) {
            SalesItem::create([
                'sales_invoice_id' => $salesInvoice->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'barcode' => $product->barcode,
                'price' => $product->retail_price,
                'quantity' => 2,
                'total' => $product->retail_price * 2,
            ]);
            $product->decrement('stock', 2);
        }

        // ✅ إنشاء مرتجع
        $return = SalesReturn::create([
            'sales_invoice_id' => $salesInvoice->id,
            'user_id' => $admin->id,
            'total' => 1200,
            'notes' => 'مرتجع تجريبي',
        ]);

        SalesReturnItem::create([
            'sales_return_id' => $return->id,
            'product_id' => 1,
            'product_name' => 'موبايل سامسونج',
            'barcode' => '111',
            'price' => 6000,
            'quantity' => 0.2,
            'total' => 1200,
        ]);

        // ✅ مصروفات
        Expense::create([
            'type' => 'فاتورة كهرباء',
            'amount' => 300,
            'date' => now(),
            'notes' => 'عن شهر مايو',
        ]);

        Expense::create([
            'type' => 'مرتب موظف',
            'amount' => 5000,
            'date' => now(),
            'notes' => 'مرتب شهر كامل',
        ]);
    }
}
