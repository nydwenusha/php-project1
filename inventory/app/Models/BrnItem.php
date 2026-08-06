<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrnItem extends Model
{
    /**
     * Attributes that are mass assignable.
     */
    protected $fillable = [
        'brn_header_id',
        'raw_material_id',
        'purchase_price',
        'quantity',
        'batch_number',
        'expiry_date',
        'avg_cost'
    ];

    /**
     * Parent relationship back to the BRN Header.
     */
    public function header()
    {
        return $this->belongsTo(BrnHeader::class, 'brn_header_id');
    }

    /**
     * Detailed relationship to the Material Master.
     */
    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }
}