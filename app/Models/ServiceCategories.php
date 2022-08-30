<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;
use App\Models\UserServices;

class ServiceCategories extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public $fillable = ['name','icon','detail'];

    public function service()
    {
        return $this->hasOne(Service::class,'id','service_id');
    }
    public function userservice()
    {
        return $this->hasOne(UserServices::class,'id','service_category_id');
    }
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
    public function scopeSearch($query,$val)
    {
        if($val != null){
            return $query
            ->where('service_categories.id',$val)
            ->orWhere('services.name','like','%'.$val.'%');
        }else{
            return $query;
        }
    }
    public function getIconAttribute($value)
    {
        if ($value) {
            return asset('/img/' . $value);
        } else {
            return null;
        }
    }
}
