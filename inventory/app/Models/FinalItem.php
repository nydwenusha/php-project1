<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalItem extends Model
{
    protected $fillable = [
    'item_name', 
    'item_code', 
    'uom',
    'cost_price', 
    'selling_price', 
    'shelf_life_days'
];

protected static function booted()
{
    static::created(function ($item) {
        \App\Models\MainInventoryStock::create([
            'final_item_id' => $item->id,
            'item_name' => $item->item_name,
            'available_qty' => 0
        ]);
    });

    static::updated(function ($item) {
        \App\Models\MainInventoryStock::where('final_item_id', $item->id)->update([
            'item_name' => $item->item_name
        ]);
    });

    static::deleted(function ($item) {
        \App\Models\MainInventoryStock::where('final_item_id', $item->id)->delete();
    });
}

}
