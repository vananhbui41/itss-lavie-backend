<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'word',
        'furigana'
    ];

    public function meanings()
    {
        return $this->hasMany(Meaning::class);
    }

    public function tags()
    {
        return $this->hasManyDeep(Tag::class, [Meaning::class, 'meaning_tag']);
    }
}
