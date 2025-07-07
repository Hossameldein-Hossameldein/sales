<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المنتج
            $table->string('barcode')->nullable()->unique(); // الباركود
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete(); // الصنف
            $table->string('unit')->default('قطعة'); // وحدة القياس
            $table->decimal('purchase_price', 12, 2)->default(0);        // سعر الشراء
            $table->decimal('retail_price', 12, 2)->default(0);          // سعر البيع القطاعي
            $table->decimal('wholesale_price', 12, 2)->default(0);
            $table->decimal('stock', 12, 2)->default(0); // المخزون الحالي
            $table->boolean('has_expiry')->default(false); // هل له تاريخ صلاحية؟
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
