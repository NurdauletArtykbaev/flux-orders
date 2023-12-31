<?php

namespace Nurdaulet\FluxOrders\Models;

use Nurdaulet\FluxOrders\Traits\HasFilters;
use Nurdaulet\FluxOrders\Helpers\OrderHelper;
use Nurdaulet\FluxBase\Traits\Reviewable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_item';
    protected $fillable = [
        'item_id', 'order_id',
        'price', 'rent_value',
        'quantity',
        'rent_type_id'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }


    public function rentType()
    {
        return $this->belongsTo(RentType::class, 'rent_type_id');
    }



}
