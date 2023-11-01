<?php

namespace Nurdaulet\FluxOrders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanceledOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'canceled_orders';
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function reason()
    {
        return $this->belongsTo(CancelReason::class, 'cancel_id');
    }
}
