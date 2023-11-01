<?php

namespace Nurdaulet\FluxOrders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class RentType extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $guarded = ['id'];
    public array $translatable = ['name'];


}
