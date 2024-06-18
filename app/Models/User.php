<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\UMKMGroups;
use App\Models\UMKMGroupList;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject; // <-- import JWTSubject

class User extends Authenticatable implements JWTSubject // <-- tambahkan ini
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public function city()
    {
        // return $this->belongsTo('App\Models\City');
        return $this->belongsTo(City::class, 'id_city');
    }
    public function province()
    {
        // return $this->belongsTo('App\Models\Province');
        return $this->belongsTo(Province::class, 'id_province');
    }

    public function incomes()
    {
        return $this->hasMany(Transaction::class, 'id_user')->where('status', 1);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'id_user');
    }

    public function group()
    {
        return $this->hasOne(UMKMGroupList::class, 'id_user');
    }

    protected $fillable = [
        'name', 'email', 'password', 'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * getJWTIdentifier
     *
     * @return void
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * getJWTCustomClaims
     *
     * @return void
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
