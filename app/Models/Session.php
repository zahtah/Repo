<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = ['session_number', 'title', 'description', 'date', 'time'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'session_user', 'session_id', 'user_id');
    }

    public function allocations()
    {
        return $this->hasMany(Allocation::class, 'session', 'session_number');
    }
}

