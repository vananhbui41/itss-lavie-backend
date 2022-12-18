<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Meaning;
use App\Models\Tag;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tags = Tag::with('category');

        if ($request->has('name')) {
            try {
                $tags->where('name', 'like', '%'.$request->name.'%');
                if ($tags->count() == 0) {
                    return $this->error(null, "Tag with the name not found", 200);
                }
            } catch (Exception $th) {
                return $this->error(null, $th->getMessage(),200);
            }

        }

        if ($request->has('category')) {
            $category = Category::where('name', $request->category)->first();
            $tags->whereBelongsTo($category);
        }

        return \response()->json($tags->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|unique:tags,name|max:255',
                'category_id' => 'required|exists:categories,id'
            ]
        );
        $data = $request->all();

        if ($validator->fails()) {
            return $this->error(null, $validator->errors(), 422);
        }
        try {
            $tag = Tag::create($data);
            return $this->success($tag, 'Tag has been created successfully');
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
        try {
            $tag = Tag::findOrFail($id);
        } catch (ModelNotFoundException $th) {
            return $this->error(null, "Tag not found", 404);
        }
        $tag->category;
        return \response()->json($tag);
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
        $validator = Validator::make($request->all(),
            [
                'name' => 'unique:tags,name|max:255',
                'category_id' => 'exists:categories,id'
            ]
        );

        if ($validator->fails()) {
            return $this->error(null, $validator->errors(), 422);
        }

        try {
            $tag = Tag::findOrFail($id);
        } catch (ModelNotFoundException $th) {
            return $this->error(null, "Tag not found", 404);
        }

        try {
            $tag->update($request->all());
        } catch (QueryException $th) {
            return $this->error(null, $th->getMessage(), 200);
        }
        return $this->success($tag,'Tag has been updated');
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
            $tag = Tag::find($id);
            $tag->meanings()->sync([]);
            Tag::destroy($id);
            return $this->success(\null,'Tag deleted successful');
        } catch (QueryException $th) {
            return $this->error(null, $th->getMessage(), 200);
        }
    }
}
