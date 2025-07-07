<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    protected $guarded = ['id'];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
