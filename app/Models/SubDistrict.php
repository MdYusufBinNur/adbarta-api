<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubDistrict extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): HasMany
    {
        return $this->hasMany(User::class,'sub_district_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class,'sub_district_id');
    }
}
