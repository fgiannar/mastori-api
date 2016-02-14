<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Profession;

class ProfessionController extends Controller
{
    // TODO Add middleware for checking permissions after auth implementation, ADMIN only

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Profession::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = $this->validator($data);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        // store
        $profession = new Profession;

        $newProfession = $profession->create($data);

        return response($newProfession, 201);
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
        $profession = Profession::findOrFail($id);

        $data = $request->all();

        $validator = $this->validator($data, $id);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        // store
        $profession->update($data);

        return $profession;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $profession = Profession::findOrFail($id);

        $profession->delete();
    }

    /**
     * Get a validator for a user create/update request.
     *
     * @param  array  $data
     * @param  int  $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $id = 0)
    {

        return Validator::make($data, [
            'tag' => 'required|unique:professions,tag,' . $id,
            'title' => 'required'
        ]);
    }
}
