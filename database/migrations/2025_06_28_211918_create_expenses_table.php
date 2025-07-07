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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // نوع المصروف: كهرباء، إيجار، مرتبات...
            $table->decimal('amount', 12, 2); // المبلغ
            $table->date('date'); // التاريخ
            $table->text('notes')->nullable(); // ملاحظات اختيارية
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // الموظف اللي سجل المصروف
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
