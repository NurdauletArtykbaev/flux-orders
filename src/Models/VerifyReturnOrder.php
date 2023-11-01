<?php

namespace Nurdaulet\FluxOrders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifyReturnOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

}
