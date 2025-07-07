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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained()->cascadeOnDelete(); // الفاتورة المرتجع منها
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // الموظف اللي عمل المرتجع
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable(); // سبب المرتجع
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
