<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesRep extends Model
{
    protected $fillable = ['rep_code', 'name', 'phone', 'is_active'];
}