<?php

namespace Nurdaulet\FluxOrders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'phone',
        'payment_method_id',
        'full_name',
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class,CartItem::class)->using(CartItem::class)
            ->withPivot([
                'quantity',
                'cart_id',
                'user_address_id',
                'receive_method_id',
                'fields',
                'rent_value',
                'rent_type_id'])
            ->wherePivotNull('deleted_at');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

}
