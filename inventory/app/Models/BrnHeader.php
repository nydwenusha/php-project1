<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrnHeader extends Model
{
    /**
     * Attributes that are mass assignable.
     */
    protected $fillable = [
        'brn_code', 
        'supplier_id', 
        'purchase_date'
    ];

    /**
     * Get the line items associated with the BRN.
     */
    public function items()
    {
        return $this->hasMany(BrnItem::class, 'brn_header_id');
    }

    /**
     * Get the supplier that provided the receipt.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}