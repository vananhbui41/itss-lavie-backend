<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'meaning'
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
