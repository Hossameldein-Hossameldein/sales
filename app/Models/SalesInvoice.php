<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
