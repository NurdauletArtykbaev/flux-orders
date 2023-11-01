<?php

namespace Nurdaulet\FluxOrders\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Item extends Model
{
    use HasFactory,  Searchable, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_busy' => 'boolean',
        'is_required_deposit' => 'boolean'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
    public function images()
    {
        return $this->hasMany(ImageItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'ad_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeIsNotActive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeHit($query)
    {
        return $query->where('is_hit', 1);
    }
}
