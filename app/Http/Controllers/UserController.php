<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getHistories()
    {
        $user = Auth::user();

        $words = $user->words;
        
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyHistory($id)
    {
        $user = Auth::user(); 
        $user->words()->detach($id);
        return $this->success(\null,'History deleted successful');
    }
}
