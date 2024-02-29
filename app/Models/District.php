<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'district_id');
    }

    public function sub_districts(): HasMany
    {
        return $this->hasMany(SubDistrict::class);
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'district_id');
    }
}
