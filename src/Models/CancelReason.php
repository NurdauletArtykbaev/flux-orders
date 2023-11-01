<?php

namespace Nurdaulet\FluxOrders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CancelReason extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cancel_reasons';
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

}
