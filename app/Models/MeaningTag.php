<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeaningTag extends Model
{
    use HasFactory;

    protected $table = "meaning_tag"; // Note: vội quá nên set tạm

    protected $fillable = [
        'tag_id',
        'meaning_id'
    ];

    public function tags()
    {
        return $this->belongsTo(Tag::class);
    }

    public function meanings()
    {
        return $this->belongsTo(Meaning::class);
    }
}