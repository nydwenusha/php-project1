<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleStock extends Model
{
    protected $fillable = ['vehicle_id', 'final_item_id', 'quantity'];
    
    // Note: Link this dynamically to your existing Final Production Item model if needed
}