<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'genre', 'country' , 'user_id'];
    public function albums() {
        return $this->hasMany(Album::class);
    }

    public function user() {
    return $this->belongsTo(User::class);
    }
}
