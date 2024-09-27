<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = ['discount_type', 'discount_value', 'max_discount_amount', 'user_left', 'valid_until'];


    
}
