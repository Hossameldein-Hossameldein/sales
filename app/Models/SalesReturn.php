<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $guarded = ['id'];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    } 
}
