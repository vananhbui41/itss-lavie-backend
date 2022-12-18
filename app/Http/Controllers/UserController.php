<?php

namespace App\Http\Controllers;

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

        return $user->words;
        
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
