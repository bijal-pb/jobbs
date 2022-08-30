<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ServiceCategories;
use App\Models\UserRequests;


class UserServices extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['price','status'];

    public function servicecategory()
    {
        return $this->hasMany(ServiceCategories::class,'id','service_category_id')->with('service');

    }
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');

    }
    public function service_category()
    {
        return $this->hasOne(ServiceCategories::class,'id','service_category_id')->with('service');

    }
    public function getIconAttribute($value)
    {
        if ($value) {
            return asset('/serviceimages/' . $value);
        } else {
            return null;
        }
    }
}
