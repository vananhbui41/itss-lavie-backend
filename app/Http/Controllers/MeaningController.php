<?php

namespace App\Http\Controllers;

use App\Models\Meaning;
use App\Traits\HttpResponses;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ImageResource;

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
        $this->validate($request, [
            'image' => 'required|mimes:pdf,png,jpg|max:9999'
        ]);
        
        $base_location = 'meaning_images';

        // Handle File Upload
        if($request->hasFile('image')) {              
            //Using store(), the filename will be hashed. You can use storeAs() to specify a name.
            //To specify the file visibility setting, you can update the config/filesystems.php s3 disk visibility key,
            //or you can specify the visibility of the file in the second parameter of the store() method like:
            //$imagePath = $request->file('image')->store($base_location, ['disk' => 's3', 'visibility' => 'public']);
            
            $imagePath = $request->file('image')->store($base_location, 's3');
          
        } else {
            return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }
    
        return \response()->json($imagePath);
        //We save new path
        $meaning = new Meaning();
        $meaning->image = $imagePath;
        $meaning->meaning = $request->meaning;
        $meaning->save();
       
        return response()->json(['success' => true, 'message' => 'image successfully uploaded', 'meaning' => new ImageResource($meaning)], 200);
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
