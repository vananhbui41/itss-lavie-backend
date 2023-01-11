<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public function checkBookmark($user) {
        $bookmarks = $user->bookmarks;

        if ($bookmarks->count() <= 0) {
            return 0;
        }

        foreach ($bookmarks as $bookmark) {
            if ($bookmark->word_id == $this->id) {
                return 1;
            }
        }

        return 0; 
    }
}
