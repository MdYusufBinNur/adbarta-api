<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id')->select('full_name','email','uid','id','photo');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class,'receiver_id')->select('full_name','email','uid','id','photo');
    }

    public function getUserInfo($userId)
    {
        return User::query()->where('id','=', $userId)->select('full_name','email','uid','id','photo');

    }
}
