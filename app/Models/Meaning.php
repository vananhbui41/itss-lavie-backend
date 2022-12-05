<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meaning extends Model
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
        'meaning',
        'dongnghia',
        'trainghia'
    ];

    public function examples()
    {
        return $this->hasMany(Example::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'histories','user_id','meaning_id');
    }

    public function tags()
    {
        return $this->hasManyDeep(Tag::class, [Example::class, 'example_tag']);
    }
}
