<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackingRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Added 'customer_id' to link packing records with customers.
     */
    protected $fillable = [
        'customer_id',
        'product_name',
        'batch_reference',
        'pack_size',
        'quantity_packed',
        'packing_date'
    ];

    /**
     * Cast attributes to specific types.
     */
    protected $casts = [
        'quantity_packed' => 'integer',
        'packing_date' => 'date',
    ];

    /**
     * Get the customer that owns the packing record.
     * Professional Relationship: Each packing record may belong to one customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}