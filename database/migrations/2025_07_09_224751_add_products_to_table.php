<?php

use App\Imports\ExcelReaderImport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = [
            'مستحضرات تجميل',
            'مكياج',
            'البرفيوم'
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create([
                'name' => $category
            ]);
        }
        $path = public_path('excel/first.xlsx');

        Excel::import(new ExcelReaderImport, $path);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
