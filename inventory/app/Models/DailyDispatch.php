<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyDispatch extends Model
{
    protected $fillable = ['dispatch_date', 'gate_pass_no', 'vehicle_id', 'sales_rep_id', 'route', 'status', 'user_id'];

    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function salesRep(): BelongsTo { return $this->belongsTo(SalesRep::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    
    public function items(): HasMany
    {
        return $this->hasMany(DailyDispatchItem::class, 'dispatch_id');
    }
}