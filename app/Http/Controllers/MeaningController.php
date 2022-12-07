<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Example;
use App\Models\Meaning;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Database\QueryException;

class MeaningController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function search(Request $request)
    {
        $meaning = Meaning::query();

        $word = $request->word;
        $tags = $request->tags;

        if (isset($tags)) {
            try {
                $tagResult = Tag::whereIn('name',$tags)->pluck('id')->toArray();

                $exResult = Example::whereHas('tags', function ($query) use ($tagResult){
                    $query->whereIn('tag_id', $tagResult);
                })->with('tags')->get();

                foreach ($exResult as $key => $value) {
                    if (count($value->tags) < count($tags)) {
                        unset($exResult[$key]);
                    }
                }

                $x = array_column($exResult->toArray(), 'meaning_id');

                $meaning->whereIn('id',$x);
                
                if ($meaning->count() == 0) {
                    return $this->success(null,'There no word with this tag.');
                }
            } catch (QueryException $th) {
                return $this->error(null,$th->getMessage(),400);
            }
        }

        if (isset($word)) {
            try {
                $meaning->where('word','LIKE','%'.$word.'%')->orWhere('meaning','LIKE','%'.$word.'%');
                if ($meaning->count() == 0) {
                    return $this->success(null,'Word not found.');
                }
            } catch (QueryException $th) {
                return $this->error(null,$th->getMessage(),400);
            }
        }

        return $meaning->with('examples','tags')->get();
    }
}
