<?php

namespace Nurdaulet\FluxOrders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function getFullNameWithPhoneAttribute()
    {
        return $this->name . ' ' . $this->surname . '| ' . $this->phone;
    }

    public function getCompanyNameWithPhoneAttribute()
    {
        return( $this->company_name ?? ($this->name . ' ' . $this->surname)) . '| ' . $this->phone;
    }

}
