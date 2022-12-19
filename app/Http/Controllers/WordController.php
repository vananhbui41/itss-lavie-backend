<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Word;
use App\Models\Meaning;
use App\Traits\HttpResponses;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class WordController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $words = Word::all();
        foreach ($words as $word) {
            $categories = $word->tags()->distinct()->get()->groupBy('category_id');
            foreach ($categories as $key => $category) {
                $category_name = Category::find($key)->name;
                $categories[$category_name] = $categories[$key];
                unset($categories[$key]);
            }
            $word->toArray();
            $word['categories'] = $categories;
        }
        return \response()->json($words);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'word' => 'required|unique:words,word|max:255',
            'meanings.*.tags_id' => 'required'
        ]);
        $data = $request->all();

        $arr_meanings = $request->meanings;
        $synonym = $request->synonym;
        $antonym = $request->antonym;
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        DB::beginTransaction();
        try {
            $word = Word::create($data);

            if (isset($arr_meanings)) {
                foreach($arr_meanings as $x){
                    $x['word_id'] = $word->id;
                    $meaning = Meaning::create($x);
                    foreach($x["tags_id"] as $tag_id){
                        $meaning->tags()->attach($tag_id);
                    }
                }
            }
            if (isset($synonym)) {
                foreach ($synonym as $value) {
                    $word2_id = Word::where('word', $value)->first();
                    if ($word2_id == null) {
                        return $this->error(null, 'Related word: '. $value . ' Not found', 200);
                    }
                    DB::table('word_relations')
                        ->updateOrInsert([
                            'word1_id' => $word->id,
                            'word2_id' => $word2_id->id,
                            'relation_type' => 1
                        ]);
                }
            }
            if (isset($antonym)) {
                foreach ($antonym as $value) {
                    $word2_id = Word::where('word', $value)->first();
                    if ($word2_id == null) {
                        return $this->error(null, 'Related word: '. $value . ' Not found', 200);
                    }
                    DB::table('word_relations')
                        ->updateOrInsert([
                            'word1_id' => $word->id,
                            'word2_id' => $word2_id->id,
                            'relation_type' => 0
                        ]);
                }
            }
            DB::commit();
            return $this->success($word, 'Word has been created successfully');
        } catch (QueryException $th) {
            DB::rollBack();
            return \response()->json($th->errorInfo);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $word = Word::findOrFail($id);
        $meanings = $word->meanings;
        foreach ($meanings as $meaning) {
            $tags = $meaning->tags;
            foreach ($tags as $tag) {
                $tag->category;
            }
        }
        return \response()->json($word);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), ['word|max:255']);
        $data = $request->all();

        $arr_meanings = $request->meanings;
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        DB::beginTransaction();
        try {
            $wordUpdate = Word::find($id);
            $wordUpdate->update($data);

            if (isset($arr_meanings)) {
                // Delete old data related
                $meaning = Meaning::where('word_id',$id);
                foreach($meaning as $m){
                    $m->tags()->detach();
                }

                // Create new data related
                foreach($arr_meanings as $x){
                    $x['word_id'] = $wordUpdate->id;
                    if (isset($x['id'])) {
                        $meaning = Meaning::findOrFail($id)->updated($x);
                    } else {
                        $meaning = Meaning::create($x);
                    }
                    if (isset($x['tags_id'])) {
                        foreach($x["tags_id"] as $tag_id){
                            $meaning->tags()->attach($tag_id);
                        }
                    }
                }
            }

            if (isset($synonym)) {
                foreach ($synonym as $value) {
                    $word2_id = Word::where('word', $value)->first();
                    if ($word2_id == null) {
                        return $this->error(null, 'Related word: '.  $value . ' Not found', 200);
                    }
                    DB::table('word_relations')
                        ->updateOrInsert([
                            'word1_id' => $wordUpdate->id,
                            'word2_id' => $word2_id,
                            'relation_type' => 1
                        ]);
                }
            }
            if (isset($antonym)) {
                foreach ($antonym as $value) {
                    $word2_id = Word::where('word', $value)->first();
                    if ($word2_id == null) {
                        return $this->error(null, 'Related word: '. $value . ' Not found', 200);
                    }
                    DB::table('word_relations')
                        ->updateOrInsert([
                            'word1_id' => $wordUpdate->id,
                            'word2_id' => $word2_id,
                            'relation_type' => 0
                        ]);
                }
            }
            DB::commit();
            return $this->success($wordUpdate->with('meanings')->get(), 'Word has been updated successfully');
        } catch (QueryException $th) {
            DB::rollBack();
            return \response()->json($th->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Word::destroy($id);
            $meaning = Meaning::where('word_id',$id)->get();
            foreach($meaning as $m){
                $m->tags()->detach();
            }
            Meaning::where('word_id',$id)->delete();
            return $this->success(\null,'Word delete successful');
        } catch (QueryException $th) {
            return \response()->json($th->errorInfo);
        }
    }

    public function search(Request $request)
    {
        $query = Word::with('meanings','tags');
        $keyword = $request->keyword;
        $tags = $request->tags;


        if (isset($keyword)) {
            try {
                $query->where(function($q) use ($keyword) {
                    $q->whereRelation('meanings','meaning','LIKE','%'.$keyword.'%')
                    ->orWhereRelation('meanings','example','LIKE','%'.$keyword.'%')
                    ->orWhereRelation('meanings','example_meaning','LIKE','%'.$keyword.'%')
                    ->orWhere('word','LIKE','%'.$keyword.'%')
                    ->orWhere('furigana','LIKE','%'.$keyword.'%');
                });
                if ($query->count() == 0) {
                    return $this->success(null,'Word not found.');
                }
            } catch (QueryException $th) {
                return $this->error(null,$th->getMessage(),400);
            }
        }

        if (isset($tags)) {
            try {
                foreach ($tags as $tag) {
                    $query->whereRelation('tags','name',$tag);
                }
                if ($query->count() == 0) {
                    return $this->success(null,'There no word with those tags.');
                }
            } catch (QueryException $th) {
                return $this->error(null,$th->getMessage(),400);
            }
        }
 
        if ($request->header('Authorization')) {
            $user = auth('sanctum')->user();
            $word_ids = $query->pluck('id')->toArray();
            $user->words()->syncWithoutDetaching($word_ids);
        }
        
        $results = $query->get()->toArray();
        foreach ($results as $key => $result) {
            $relations = DB::table('word_relations')
                ->where('word1_id', $result['id'])
                ->get()
                ->toArray();
            $synonym = [];
            $antonym = [];
            foreach ($relations as $relation) {
                if ($relation->relation_type == 1) {
                    $synonym_word = Word::find($relation->word2_id)->toArray();
                    \array_push($synonym, $synonym_word);
                }
                if ($relation->relation_type == 0) {
                    $antonym_word = Word::find($relation->word2_id)->toArray();
                    \array_push($antonym, $antonym_word);
                }
            }
            $results[$key]['synonym'] = $synonym;
            $results[$key]['antonym'] = $antonym;
        } 
        return \response()->json($results);
    }
}
