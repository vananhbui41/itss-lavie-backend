<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use App\Models\RequestMeaning;
use App\Models\Tag;
use App\Models\Word;
use App\Traits\HttpResponses;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        if ($request->header('Authorization')) {
            $user = auth('sanctum')->user();
        } else {
            return $this->error(null, "Please login to access this resource", 404);
        }

        $validator = Validator::make($request->all(), [
            'word' => 'required|unique:words,word|max:255',
            'type' => 'required',
            'meanings.*.meaning' => 'required',
            'meanings.*.explanation_of_meaning' => 'required',
            'meanings.*.context' => 'required',
            'meanings.*.topic' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['user_id'] = $user->id;

        $type = Tag::where('name', $data['type'])->first();
        if (isset($type)) {
            $data['type'] = $type->id;
        } else {
            return $this->error(null, 'Invalid type ['. $data['type'] .']', 404);
        }

        $arr_meanings = $request->meanings;
        $synonym = $request->synonym;
        $antonym = $request->antonym;


        if (isset($synonym)) {
            $synonym = \explode(',', $synonym);
            foreach ($synonym as $value) {
                $word2_id = Word::where('word', $value)->first();
                if ($word2_id == null) {
                    return $this->error(null, 'Synonym word: '. $value . ' Not found', 404);
                }
            }
        }
        if (isset($antonym)) {
            $antonym = \explode(',', $antonym);
            foreach ($antonym as $value) {
                $word2_id = Word::where('word', $value)->first();
                if ($word2_id == null) {
                    return $this->error(null, 'Antonym word: '. $value . ' Not found', 404);
                }
            }
        }
        
        DB::beginTransaction();
        try {
            $word = ModelsRequest::create($data);

            if (isset($arr_meanings)) {
                foreach($arr_meanings as $x){
                    $x['image'] = !empty($x['image']) ? $x['image'] : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNET4coNATuFn3TwH9_dn5FvMp2hjKPANHGA&usqp=CAU';
                    $x['request_id'] = $word->id;

                    $context = Tag::where('name', $x['context'])->first();
                    if (isset($context)) {
                        $x['context'] = $context->id;
                    } else {
                        return $this->error(\null, 'Invalid context ['. $x['context'] .']', 404);
                    }

                    $topics = $x['topic'];
                    $topic_id = array();
                    foreach ($topics as $topic) {
                        $result = Tag::where('name', $topic)->first();
                        if (isset($result)) {
                            $topic_id[] = $result->id;
                        } else {
                            return $this->error(\null, 'Invalid topic [' .$topic . ']' , 404);
                        }
                    }
                    $x['topic'] = implode(',', $topic_id);
                    $meaning = RequestMeaning::create($x);
                }
            }
            DB::commit();
            return $this->success($word, 'Request has been created successfully');
        } catch (QueryException $th) {
            DB::rollBack();
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
        $data = $request->all();
        $synonym = $request->synonym;
        $antonym = $request->antonym;
        if (isset($data['type'])) {  
            $type = Tag::where('name', $data['type'])->first();
            if (isset($type)) {
                $data['type'] = $type->id;
            } else {
                return $this->error(null, 'Invalid type ['. $data['type'] .']', 404);
            }
        }
        if (isset($synonym)) {
            $synonym = \explode(',', $synonym);
            foreach ($synonym as $value) {
                $word2_id = Word::where('word', $value)->first();
                if ($word2_id == null) {
                    return $this->error(null, 'Synonym word: '. $value . ' Not found', 404);
                }
            }
        }
        if (isset($antonym)) {
            $antonym = \explode(',', $antonym);
            foreach ($antonym as $value) {
                $word2_id = Word::where('word', $value)->first();
                if ($word2_id == null) {
                    return $this->error(null, 'Antonym word: '. $value . ' Not found', 404);
                }
            }
        }
        $arr_meanings = $request->meanings;
        
        DB::beginTransaction();
        try {
            $word = ModelsRequest::findOrFail($id);
            $word->update($data);
            if (isset($arr_meanings)) {
                foreach($arr_meanings as $x){
                    $x['image'] = !empty($x['image']) ? $x['image'] : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNET4coNATuFn3TwH9_dn5FvMp2hjKPANHGA&usqp=CAU';
                    $x['request_id'] = $word->id;

                    $context = Tag::where('name', $x['context'])->first();
                    if (isset($context)) {
                        $x['context'] = $context->id;
                    } else {
                        return $this->error(\null, 'Invalid context ['. $x['context'] .']', 404);
                    }

                    $topics = $x['topic'];
                    $topic_id = array();
                    foreach ($topics as $topic) {
                        $result = Tag::where('name', $topic)->first();
                        if (isset($result)) {
                            $topic_id[] = $result->id;
                        } else {
                            return $this->error(\null, 'Invalid topic [' .$topic . ']' , 404);
                        }
                    }
                    $x['topic'] = implode(',', $topic_id);
                    if (isset($x['id'])) {
                        $meaning = RequestMeaning::findOrFail($x['id']);
                        $meaning->update($x);
                    } else {
                        $meaning = RequestMeaning::create($x);
                    }
                }
            }
            DB::commit();
            return $this->success($word, 'Request has been updated successfully');
        } catch (QueryException $th) {
            DB::rollBack();
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
        ModelsRequest::destroy($id);

        return $this->success('Request deleted successfully.');
    }
}
