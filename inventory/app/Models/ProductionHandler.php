<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionHandler extends Model
{
    protected $fillable = ['handler_name', 'handler_code' , 'phone', 'is_active'];
}
