<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class UserOrderRate extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['review','rate'];

    public function rate_by()
    {
        return $this->hasOne(User::class,'id','rate_by');
    }
}
