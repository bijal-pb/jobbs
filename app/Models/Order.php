<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserRequests;
use App\Models\UserOrderRate;
use App\Models\UserServices;
use App\Models\User;
use App\Models\OrderStatus;
use App\Models\ServiceCategories;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['reach_time','start_time','complete_time','price','service_fee','discount','total_amount','payment_status'];

    public function userRequest()
    {

        return $this->hasOne(UserRequests::class,'id','user_request_id')->with(['from_user','to_user','user_service']);
    }
  
    // public function userservice()
    // {
    //     return $this->hasOne(UserServices::class,'id','service_id');
    // }
    
    public function order_review()
    {
        return $this->hasMany(UserOrderRate::class,'order_id');
    }

    public function fromUser()
    {
        return $this->hasOne(User::class,'id','from');
    }
    public function toUser()
    {
        return $this->hasOne(User::class,'id','to');

    }
    public function order_status()
    {
        return $this->hasOne(OrderStatus::class,'id','status');

    }
    public function getCompleteTimeAttribute($value)
    {
        return (new Carbon($value))->format('Y-m-d H:i:s');
    }
    public function getStartTimeAttribute($value)
    {
        return (new Carbon($value))->format('Y-m-d H:i:s');
    }
    public function getReachTimeAttribute($value)
    {
        return (new Carbon($value))->format('Y-m-d H:i:s');
    }


    public function scopeSearch($query,$val)
    {
        if($val != null){
            return $query
            ->where('orders.id',$val)
            ->orWhere('from.first_name','like','%'.$val.'%')
            ->orWhere('to.first_name','like','%'.$val.'%');
          
            
        }else{
            return $query;
        }
        
    }

}

