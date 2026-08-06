<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'name',
        'email',
        'phone',
        'address',
        'status',
        'credit_limit'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $latest = self::latest()->first();
            $number = $latest ? (int) substr($latest->customer_code, 4) + 1 : 1;
            $model->customer_code = 'CUS-' . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }
}