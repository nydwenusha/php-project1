<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenIssue extends Model
{
    use HasFactory;

    /**
     * Guarded attributes for mass assignment.
     */
    protected $fillable = [
        'issue_date',
        'raw_material_id',
        'batch_number',
        'quantity',
        'source_level'
    ];

    /**
     * Type casting for accurate decimal handling in calculations.
     */
    protected $casts = [
        'quantity' => 'decimal:3',
        'issue_date' => 'date:Y-m-d'
    ];

    /**
     * Inverse relationship to RawMaterial.
     * Links issuance record back to material master details.
     */
    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}