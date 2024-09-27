<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = ['user_id', 'name', 'relationship'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
