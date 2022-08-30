<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ServiceCategories;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;
    
  
    public $fillable = ['name','icon','detail'];

    public function ServiceCategories()
    {
        return $this->hasOne(ServiceCategories::class,'service_category_id');
    }
    public function scopeSearch($query,$val)
    {
        return $query
        ->where('id',$val)
        ->Orwhere('name','like','%'.$val.'%');
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
