<?php

namespace Nurdaulet\FluxOrders\Filters;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class OrderFilter extends ModelFilter
{
    public function created_at($value)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->whereDate('created_at', now());
    }
    
    public function this_month($value)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->whereDate('created_at', '>=', (new Carbon('first day of this month'))->format('d.m.Y'));
    }

    public function status($value)
    {
        if (empty($value)) {
            return $this;
        }
        if (is_array($value)) {
            return $this->builder->whereIn('status', $value);
        }
        return $this->builder->where('status', $value);
    }

    public function user_id($value)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->where('user_id', $value);
    }
    public function lord_id($value)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->where('lord_id', $value);
    }

    public function latest_by($value = null)
    {
        if (empty($value)) {
            return $this;
        }
        return $this->builder->latest($value ?? null);
    }


}
