<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlendingRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * * @var array
     */
    protected $fillable = [
        'blending_date',
        'raw_material_id',
        'input_batch_number',
        'input_weight',
        'output_weight',
        'new_batch_number'
    ];

    /**
     * Cast attributes to native types.
     * * @var array
     */
    protected $casts = [
        'blending_date' => 'date',
        'input_weight' => 'decimal:2',
        'output_weight' => 'decimal:2',
    ];

    /**
     * Relationship: Each blending record belongs to a specific Raw Material.
     * Corresponds to Test Case BLD-005 for traceability.
     */
    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}