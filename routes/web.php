<?php

use Illuminate\Support\Facades\Route;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;

Route::get('/', function () {

    return view('welcome');
});


Route::get('/sales-invoices/{record}/print', function (SalesInvoice $record) {
    $record->load(['items', 'customer', 'user']);
    return view('prints.sales-invoice', ['record' => $record]);
})->name('sales.print');
