<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestMeaning extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'meaning',
        'explanation_of_meaning',
        'source',
        'context',
        'topic',
        'example',
        'example_meaning',
        'image'
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
