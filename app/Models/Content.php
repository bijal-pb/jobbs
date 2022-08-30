<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContentSession;

class Content extends Model
{
    use HasFactory;

    protected $appends = ['total_sessions'];

    public function sessions()
    {
        return $this->hasMany(ContentSession::class,'content_id');
    }

    public function getTotalSessionsAttribute()
    {
        return $this->hasMany(ContentSession::class,'content_id')->count();
    }

    public function scopeSearch($query,$val)
    {
        return $query
        ->where('id',$val)
        ->Orwhere('name','like','%'.$val.'%');
    }
}
