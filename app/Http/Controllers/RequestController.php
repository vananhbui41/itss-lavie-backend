<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use App\Traits\HttpResponses;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    use HttpResponses;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $tags = $request->tags;
        $requests = ModelsRequest::query()->orderBy('id', 'asc');

        if (isset($keyword)) {
            try {
                $requests->where('word','LIKE','%'.$keyword.'%');
                if ($requests->count() == 0) {
                    return $this->success(null,'Request not found.');
                }
            } catch (QueryException $th) {
                return $this->error(null,$th->getMessage(),400);
            }
        }
        $requests = $requests->get();
        if (isset($tags)) {
            foreach ($requests as $key => $requestModel) {
                $tagsName = $requestModel->getListTagsNames();
                foreach ($tags as $tag) {
                    if (in_array($tag, $tagsName)) {
                        continue;
                    } else {
                        unset($requests[$key]);
                        break;
                    }
                }
            }
        }

        foreach ($requests as $key => $requestModel) {
            $requests[$key]['type'] = $requestModel->getType();
            $requests[$key]['context'] = $requestModel->getContext();
            $requests[$key]['topic'] = $requestModel->getTopic();
        }

        return \response()->json($requests);
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
        $request = ModelsRequest::findOrFail($id);
        $request->requestMeanings;
        return \response()->json($request);  
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
        ModelsRequest::destroy($id);

        return $this->success('Request deleted successfully.');
    }
}
