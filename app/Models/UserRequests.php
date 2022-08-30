<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ServiceCategories;
use App\Models\UserServices;
use App\Models\User;
use App\Models\Order;
use App\Models\Services;


class UserRequests extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['start_date','end_date','start_time','end_time','lat','lang','address','status'];

    public function userservice()
    {

        return $this->hasmany(UserServices::class,'id','user_service_id')->with('service_category');
    }
    public function user_service()
    {
        return $this->hasOne(UserServices::class,'id','user_service_id')->with('service_category');

    }
    // public function order()
    // {
    //     return $this->hasOne(order::class,'id','user_request_id');
    // }

    public function from_user()
    {
        return $this->hasOne(User::class,'id','from');
    }
    public function to_user()
    {
        return $this->hasOne(User::class,'id','to');
    }
    public function order_review()
    {
        return $this->hasMany(UserOrderRate::class,'order_id');
    }
    public function order_status()
    {
        return $this->hasMany(Order::class,'id','status');

    }
    public function scopeSearch($query,$val)
    {
        return $query
        ->where('id',$val)
        ->Orwhere('from','like','%'.$val.'%');

    }
}
