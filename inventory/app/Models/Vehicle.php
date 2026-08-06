<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = ['vehicle_no', 'route_area', 'is_active'];

    public function liveStocks(): HasMany
    {
        return $this->hasMany(VehicleStock::class, 'vehicle_id');
    }

    public function dispatches(): HasMany
    {
        return $this->hasMany(DailyDispatch::class, 'vehicle_id');
    }
}