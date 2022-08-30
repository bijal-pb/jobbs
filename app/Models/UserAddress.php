<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['first_name','last_name','mobile','address','lat','lang','city','zip','address_type','default'];
}
