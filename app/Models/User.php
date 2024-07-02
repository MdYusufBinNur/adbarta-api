<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function products()
    {
        return $this->hasMany(Product::class,'user_id');
    }

    public function wallet()
    {
        return $this->hasOne(UserWallet::class,'user_id');
    }

    public function wallet_history()
    {
        return $this->hasMany(WalletHistory::class,'user_id');
    }

    public function sub_district()
    {
        return $this->belongsTo(SubDistrict::class);
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uid = 'B' . date("dHis") . rand(1111,9999);
        });

    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}
