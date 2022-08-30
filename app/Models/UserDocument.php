<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocumentType;
use App\Models\User;

class UserDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['document','status'];
    public function DocumentType()
    {
        return $this->hasMany(DocumentType::class,'document_type_id');
    }

    public function uploadby()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function documentname()
    {
        return $this->hasOne(DocumentType::class,'id','document_type_id');
    }

    public function scopeSearch($query,$val)
    {
        if($val != null){
            return $query
            ->where('user_documents.id',$val)
            ->orWhere('users.first_name','like','%'.$val.'%');     
        }else{
            return $query;
        }
        
    }
    public function getDocumentAttribute($value)
    {
        if ($value) {
            return asset('/documentimages/' . $value);
        } else {
            return null;
        }
    }
}
