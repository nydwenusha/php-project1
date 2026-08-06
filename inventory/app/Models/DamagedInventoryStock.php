<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedInventoryStock extends Model
{
    protected $fillable = ['final_item_id', 'quantity'];
}