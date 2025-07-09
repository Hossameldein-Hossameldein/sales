<?php

use App\Imports\ExcelReaderImport;
use Illuminate\Support\Facades\Route;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    
    return redirect()->to('/admin');
});


Route::get('/sales-invoices/{record}/print', function (SalesInvoice $record) {
    $record->load(['items', 'customer', 'user']);
    return view('prints.sales-invoice', ['record' => $record]);
})->name('sales.print');
