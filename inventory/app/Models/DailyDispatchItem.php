<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyDispatchItem extends Model
{
    protected $fillable = [
        'dispatch_id', 'final_item_id', 'carried_forward_qty', 
        'new_loaded_qty', 'total_qty', 'actual_sales', 'damaged_qty', 'remaining_qty'
    ];

    public function dispatch(): BelongsTo 
    { 
        return $this->belongsTo(DailyDispatch::class); 
    }

    
    public function item(): BelongsTo
    {
        return $this->belongsTo(MainInventoryStock::class, 'final_item_id');
    }
}