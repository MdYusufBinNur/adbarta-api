<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sub_category(): HasMany
    {
        return $this->hasMany(SubCategory::class,'category_id');
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class,'category_id');
    }
    public function getProductCountAttribute(): int
    {
        return $this->product()->count();
    }
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
