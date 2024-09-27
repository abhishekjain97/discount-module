<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = ['user_id', 'booking_date', 'total_amount', 'for_member', 'discount'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
