<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function sub_category(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

    public function image(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::creating(function ($model) {
            $originalSlug = Str::slug($model->name);
            $slug = $originalSlug;
            $counter = 1;
            while (static::query()->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $model->slug = $slug;
        });
    }

}