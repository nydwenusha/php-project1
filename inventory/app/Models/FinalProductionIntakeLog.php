<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalProductionIntakeLog extends Model
{
    protected $fillable = ['final_item_id', 'handler_id', 'quantity', 'system_user_id'];

    public function item()
    {
        return $this->belongsTo(FinalItem::class, 'final_item_id');
    }

    public function handler()
    {
        return $this->belongsTo(ProductionHandler::class, 'handler_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'system_user_id');
    }

    protected static function booted()
    {
        static::created(function ($log) {
            $stock = \App\Models\MainInventoryStock::where('final_item_id', $log->final_item_id)->first();
            if ($stock) {
                $stock->increment('available_qty', $log->quantity);
            }
        });

        static::updated(function ($log) {
            $stock = \App\Models\MainInventoryStock::where('final_item_id', $log->final_item_id)->first();
            
            if ($stock) {
                $oldQty = $log->getOriginal('quantity'); 
                $newQty = $log->quantity;               
                
                $difference = $newQty - $oldQty; 
                
                $stock->increment('available_qty', $difference);
            }
        });

        static::deleted(function ($log) {
            $stock = \App\Models\MainInventoryStock::where('final_item_id', $log->final_item_id)->first();
            if ($stock) {
                $stock->decrement('available_qty', $log->quantity);
            }
        });
    }
}