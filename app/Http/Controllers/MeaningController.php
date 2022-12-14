<?php

namespace App\Http\Controllers;

use App\Models\Meaning;
use App\Traits\HttpResponses;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MeaningController extends Controller
{
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
        $validator = Validator::make($request->all(), ['meaning' => 'required|unique:meanings,meaning|max:255']);
        $data = $request->all();

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $meaning = Meaning::create($data);
            return $this->success($meaning, 'Meaning has been created successfully');
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
        $meanings = Meaning::with('tags')->get();
        return \response()->json($meanings);
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
        $validator = Validator::make($request->all(), ['meaning' => 'required|unique:meanings,meaning|max:255']);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $meaning = Meaning::find($id);
        $meaning->update($request->all());
        return $this->success($meaning,'Meaning has been updated successfully');
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
            Meaning::destroy($id);
            return $this->success(\null,'Meaing delete successful');
        } catch (QueryException $th) {
            return \response()->json($th->errorInfo);
        }
    }
}
