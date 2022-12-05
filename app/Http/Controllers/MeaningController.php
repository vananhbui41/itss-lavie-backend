<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Example;
use App\Models\Meaning;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;

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
        $tag = $request->tag;
        $category = $request->category;

        if (isset($tag)) {
            $exResult = Example::whereHas('tags', function ($query) use ($tag){
                $query->where('name', 'like', '%'.$tag.'%');
            })->pluck('meaning_id')->toArray();
        
            $meaning->whereIn('id',$exResult);
        }

        if (isset($category)) {
            $categoryResult = Category::where('name', 'like', '%'.$category.'%')->pluck('id')->toArray();
            $tagResult = Tag::whereIn('category_id', $categoryResult)->pluck('id')->toArray();
            
            $exampleResult = Example::with('tags')
            ->whereHas('tags', function($q) use($tagResult) {
                $q->whereIn('tag_id', $tagResult);
            })->pluck('meaning_id')->toArray();

            // return $exampleResult;

            $meaning->whereIn('id',$exampleResult);
        }

        if (isset($word)) {
            $meaning->where('word','LIKE','%'.$word.'%')->orWhere('meaning','LIKE','%'.$word.'%');
        }
        return $meaning->with('examples','tags')->get();
    }
    
}
