<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function district(): HasMany
    {
        return $this->hasMany(District::class,'division_id');
    }
    public function product(): HasMany
    {
        return $this->hasMany(Product::class,'division_id');
    }
}
