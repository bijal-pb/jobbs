<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Models\UserSetting;
use App\Models\UserServices;
use App\Models\UserOrderRate;


class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // protected $appends = [
    //     'profile_photo_url',
    // ];

    protected $appends = [ 'rating'];

    public function scopeSearch($query,$val)
    {
        return $query
        ->where('id',$val)
        ->Orwhere('first_name','like','%'.$val.'%');
    }

    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function getCreatedAtAttribute($value)
    {
        return (new Carbon($value))->format('Y-m-d H:i:s');
    }
    public function getUpdatedAtAttribute($value)
    {
        return (new Carbon($value))->format('Y-m-d H:i:s');
    }
    public function user_setting()
    {
        return $this->hasOne(UserSetting::class,'user_id','id');
    }
    public function user_service()
    {
        return $this->hasMany(UserServices::class,'user_id')->with('service_category');
    }

    public function getPhotoAttribute($value)
    {
        if ($value) {
            return asset('/uploads/' . $value);
        } else {
            return null;
        }
    }

    public function getRatingAttribute()
    {
        return $this->hasMany(UserOrderRate::class,'rate_to','id')->avg('rate') == null ? 0 : ceil($this->hasMany(UserOrderRate::class,'rate_to','id')->avg('rate'));
    }
    
}
