<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    protected $guarded = ['id'];

    public function return()
    {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
