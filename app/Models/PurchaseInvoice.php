<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function ($invoice) {
            // لو بالفعل عنده رقم فاتورة، مانعملش حاجة
            if ($invoice->invoice_number) return;

            // نولد رقم جديد
            $lastNumber = self::max('id') + 1;
            $invoice->invoice_number = 'PUR-' . str_pad($lastNumber, 6, '0', STR_PAD_LEFT);
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
