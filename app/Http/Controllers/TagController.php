<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Traits\HttpResponses;
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
    public function index()
    {
        $tags = Tag::with('category')->get();
        return \response()->json($tags);
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
        $validator = Validator::make($request->all(), ['name' => 'required|unique:tags,name|max:255']);
        $data = $request->all();

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
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
        $tag = Tag::with('category')->where('id',$id)->get();
        return \response()->json($tag);
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
        $validator = Validator::make($request->all(), ['name' => 'required|unique:tags,name|max:255']);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tag = Tag::find($id);
        $tag->update($request->all());
        return $this->success($tag, 'Tag has been updated successfully');
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
            Tag::destroy($id);
            return $this->success(\null, 'Tag delete successful');
        } catch (QueryException $th) {
            return \response()->json($th->errorInfo);
        }
    }
}
