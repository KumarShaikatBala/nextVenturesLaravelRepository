<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'product_id', 'quantity', 'total_price', 'status'];



    public function user():belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product():belongsTo
    {
        return $this->belongsTo(Product::class);
    }


//date format mutator
    public function getCreatedAtAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }

}
