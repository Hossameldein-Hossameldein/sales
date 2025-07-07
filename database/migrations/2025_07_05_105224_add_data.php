<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Artisan::call('shield:generate', ['--panel' => 'admin']);

        // ✅ إنشاء الأدوار
        $adminRole = Role::firstOrCreate(['name' => 'مدير']);
        $cashierRole = Role::firstOrCreate(['name' => 'كاشير']);
        $employeeRole = Role::firstOrCreate(['name' => 'موظف']);

        // ✅ ربط الصلاحيات بالأدوار
        $adminRole->syncPermissions(Permission::all());

        $cashierRole->syncPermissions(Permission::whereIn('name', [
            'view_product',
            'view_sales_invoice',
            'create_sales_invoice',
        ])->get());

        $employeeRole->syncPermissions(Permission::whereIn('name', [
            'view_product',
            'create_product',
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
