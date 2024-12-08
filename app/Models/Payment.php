<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_name', 'amount', 'status'];

    public function order(): belongsTo
    {
        return $this->belongsTo(Order::class);
    }


}
