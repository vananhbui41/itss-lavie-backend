<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Word;
use App\Traits\HttpResponses;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $bookmark = Bookmark::where('user_id', $user->id);
        if ($bookmark->count() == 0) {
            return $this->success(null,'There no bookmark');
        }
        $wordIds = $bookmark->pluck('word_id');
        $words = Word::whereIn('id',$wordIds);
        $words = $words->get();
        
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
        $user = Auth::user();
        $bookmarks = Bookmark::where('user_id', $user->id);
        foreach ($bookmarks->get() as $bookmark) {
            if ($bookmark->word_id == $request->word_id) {
                return $this->error(null, 'This word has already been added to the bookmarks list', 200);
            }
        }
        $data = $request->all();
        $data['user_id'] = $user->id;
        try {
            $bookmark = Bookmark::create($data);
            return $this->success($bookmark, 'Bookmark added');
        } catch (QueryException $e) {
            return $this->error(null, $e->getMessage(), 400);
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
        $bookmark = Bookmark::find($id);
        if (!$bookmark) {
            return $this->errorNotFound('Bookmark not found');
        }
        return $bookmark;
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
        try {
            $bookmark = Bookmark::findOrFail($id);
            $bookmark->delete();
            return $this->success('Bookmark deleted');
        } catch (QueryException $e) {
            return $this->error(null, $e->getMessage(), 400);
        }
    }
}
