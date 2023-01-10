<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'word',
        'type',
        'synonym',
        'anonym',
    ];

    public function requestMeanings()
    {
        return $this->hasMany(RequestMeaning::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getListTagsId()
    {
        $tagsId = array();
        $type[] = $this->type;
        $meanings = $this->requestMeanings()->get();
        $contexts = array();
        $topics = array();
        foreach ($meanings as $meaning) {
            $contexts[] = $meaning->context;
            $topics = array_merge($topics, explode(',', $meaning->topic));
        }

        $tagsId = array_merge($tagsId, $type, $contexts, $topics);

        return $tagsId;
    }

    public function getListTagsNames() {
        $tagsId = $this->getListTagsId();
        $tagsNames = array();
        foreach ($tagsId as $tagId) {
            $tagsNames[] = Tag::find($tagId)->name;
        }
        return $tagsNames;
    }

    public function getType() {
        return Tag::find($this->type)->name;
    }

    public function getContext() {
        $meanings = $this->requestMeanings()->get();
        $contexts = array();
        foreach ($meanings as $meaning) {
            $contexts[] = Tag::find($meaning->context)->name;
        }

        return $contexts;
    }

    public function getTopic() {
        $meanings = $this->requestMeanings()->get();
        $topics = array();
        foreach ($meanings as $meaning) {
            $topic = Tag::whereIn('id',explode(',', $meaning->topic))->pluck('name')->toArray();
            $topics = array_merge($topics, $topic);
        }
        return $topics;
    }
}
