<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory;

    /**
     * Attributes that are mass assignable.
     */
    protected $fillable = [
        'raw_material_id', 
        'batch_number', 
        'quantity', 
        'location', 
        'expiry_date', 
        'unit_cost'
    ];

    /**
     * Attribute casting for consistent data handling.
     */
    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    /**
     * Relationship mapping to RawMaterial model.
     */
    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    /**
     * Global scope to filter stock by inventory location.
     */
    public function scopeAtLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Global scope to filter records that have available stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }
}