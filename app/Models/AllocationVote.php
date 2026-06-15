<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'allocation_id',
        'user_id',
        'comment',
        'vote',
    ];

    // Relation ها (اختیاری اما مفید)
    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
