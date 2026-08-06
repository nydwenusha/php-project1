<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'address',
        'contact_information',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope a query to only include active suppliers.
     * Useful for BRN/GRN dropdowns to maintain data integrity.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function brnHeaders()
    {
        return $this->hasMany(BrnHeader::class);
    }
}