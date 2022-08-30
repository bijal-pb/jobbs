<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserDocument;

class DocumentType extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public $fillable = [ 'name','description' ];

    public function UserDocument()
    {
        return $this->hasOne(UserDocument::class,'id','doctype_id');
    }

    public function scopeSearch($query,$val)
    {
        return $query
        ->where('id',$val)
        ->Orwhere('name','like','%'.$val.'%');
    }
}
