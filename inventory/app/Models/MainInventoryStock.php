<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MainInventoryStock extends Model
{
    protected $fillable = ['final_item_id', 'item_name', 'available_qty'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(FinalItem::class, 'final_item_id'); 
    }
}
