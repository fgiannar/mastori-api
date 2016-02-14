<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Rating;
use App\Mastori;

class RatingController extends Controller
{
    // TODO Add middleware for checking permissions after auth implementation

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         // TODO Add filters, pagination
        return Rating::with('mastori')->with('user')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $mastori_id)
    {
        $data = $request->all();

        $validator = $this->validator($data);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        // store
        $mastori = Mastori::findOrFail($mastori_id);

        $data['user_id'] = 1; // TODO: Change this when auth is implemented
        $data['editing_expires_at'] = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $asdf = $mastori->ratings()->create($data);

        return response($asdf, 201);
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
        $rating = Rating::findOrFail($id);
        if ($rating['editing_expires_at'] < Carbon::now()) {
            return response('Rating editing expired', 401);
        }
        $data = $request->all();

        $validator = $this->validator($data);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        // store
        $rating->update($data);

        return $rating;
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

    /**
     * Get a validator for a user create/update request.
     *
     * @param  array  $data
     * @param  int  $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'rating' => 'required|numeric',
            'body' => 'required'
        ]);
    }
}
