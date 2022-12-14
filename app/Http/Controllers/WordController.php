<?php

namespace App\Http\Controllers;

use App\Models\Word;
use App\Models\Meaning;
use App\Models\MeaningTag;
use App\Traits\HttpResponses;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        return \response()->json($words);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['word' => 'required|unique:words,word|max:255']);
        $data = array(
            "word" => $request->word,
            "furigana" => $request->furigana
        );

        $arr_meanings = $request->meanings;
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $word = Word::create($data);

            foreach($arr_meanings as $x){
            
                $data_meaning = array(
                    "word_id"=> $word->id,
                    "meaning"=> $x["meaning"],
                    "explanation_of_meaning"=> $x["explanation_of_meaning"],
                    "example"=> $x["example"],
                    "example_meaning"=> $x["example_meaning"],
                    "image"=> $x["image"]
                );
                $meaning = Meaning::create($data_meaning);

                foreach($x["tags_id"] as $tag_id){
                    $tm = array(
                        "tag_id"=> $tag_id,
                        "meaning_id"=> $meaning->id
                    );
                    $meaningtag = MeaningTag::create($tm);
                }
            }
            return $this->success($word, 'Word has been created successfully');
        } catch (QueryException $th) {
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
        $words = Word::find($id);
        return \response()->json($words);
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
        $data = array(
            "word" => $request->word,
            "furigana" => $request->furigana
        );

        $arr_meanings = $request->meanings;
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $word = Word::find($id);
            $word->update($data);

            // Delete old data related
            $meaning = Meaning::where('word_id',$id)->get();
            foreach($meaning as $m){
                $meaningtag = MeaningTag::where('meaning_id', $m["id"])->delete();
            }
            Meaning::where('word_id',$id)->delete();

            // Create new data related
            foreach($arr_meanings as $x){
            
                $data_meaning = array(
                    "word_id"=> $id,
                    "meaning"=> $x["meaning"],
                    "explanation_of_meaning"=> $x["explanation_of_meaning"],
                    "example"=> $x["example"],
                    "example_meaning"=> $x["example_meaning"],
                    "image"=> $x["image"]
                );
                $meaning = Meaning::create($data_meaning);

                foreach($x["tags_id"] as $tag_id){
                    $tm = array(
                        "tag_id"=> $tag_id,
                        "meaning_id"=> $meaning->id
                    );
                    $meaningtag = MeaningTag::create($tm);
                }
            }
            return $this->success($word, 'Word has been created successfully');
        } catch (QueryException $th) {
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
                $meaningtag = MeaningTag::where('meaning_id', $m["id"])->delete();
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
        return $query->get();      
    }
}
