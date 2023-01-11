<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'word_id'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function word() {
        return $this->belongsTo(Word::class);
    }
}
