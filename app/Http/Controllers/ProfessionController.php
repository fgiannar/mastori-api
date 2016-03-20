<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Profession;

class ProfessionController extends Controller
{
    // TODO Add middleware again after testing period
    public function __construct()
    {
        // $this->middleware('roles:admin');
    }


    /**
     * @SWG\Get(
     *     path="/professions",
     *     description="Returns all the professions",
     *     operationId="getprofessions",
     *     tags={"professions"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Access token",
     *         in="header",
     *         name="Authorization",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="All professions response",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/profession")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="token_not_provided/token_invalid",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     )
     * )
     */
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
     * @SWG\Post(
     *     path="/professions",
     *     operationId="addProfession",
     *     tags={"professions"},
     *     description="Adds a new profession in database",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Access token",
     *         in="header",
     *         name="Authorization",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/profession")
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returns created profession object",
     *         @SWG\Schema(ref="#/definitions/profession")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="validation errors",
     *         @SWG\Schema(ref="#/definitions/validationsErrorsModel")
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="token_not_provided/token_invalid/Unauthorized",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     ),
     * )
     */
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
      *@SWG\Put(
      *     path="/professions/{id}",
      *     @SWG\Parameter(
      *         description="ID of profession to update",
      *         in="path",
      *         name="id",
      *         required=true,
      *         type="integer"
      *     ),
      *     @SWG\Parameter(
      *         description="Access token",
      *         in="header",
      *         name="Authorization",
      *         required=true,
      *         type="string"
      *     ),
      *     operationId="editprofession",
      *     tags={"professions"},
      *     description="Edits a professions",
      *     produces={"application/json"},
      *     @SWG\Parameter(
      *         name="body",
      *         in="body",
      *         required=true,
      *         @SWG\Schema(ref="#/definitions/profession")
      *     ),
      *     @SWG\Response(
      *         response=200,
      *         description="Professions response",
      *         @SWG\Schema(ref="#/definitions/profession")
      *     ),
      *     @SWG\Response(
      *         response="401",
      *         description="token_not_provided/token_invalid",
      *         @SWG\Schema(
      *             ref="#/definitions/errorModel"
      *         )
      *     ),
      *     @SWG\Response(
      *         response="400",
      *         description="validation errors",
      *         @SWG\Schema(ref="#/definitions/validationsErrorsModel")
      *     )
      * )
      */
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
      *@SWG\Delete(
      *     path="/professions/{id}",
      *     @SWG\Parameter(
      *         description="ID of profession to delete",
      *         in="path",
      *         name="id",
      *         required=true,
      *         type="integer"
      *     ),
      *     @SWG\Parameter(
      *         description="Access token",
      *         in="header",
      *         name="Authorization",
      *         required=true,
      *         type="string"
      *     ),
      *     operationId="deleteprofession",
      *     tags={"professions"},
      *     description="Delete a profession",
      *     produces={"application/json"},
      *     @SWG\Response(
      *         response=200,
      *         description="OK"
      *     ),
      *     @SWG\Response(
      *         response="401",
      *         description="token_not_provided/token_invalid",
      *         @SWG\Schema(
      *             ref="#/definitions/errorModel"
      *         )
      *     ),
      *     @SWG\Response(
      *         response="500",
      *         description="Cannot be deleted.",
      *         @SWG\Schema(
      *             ref="#/definitions/errorModel"
      *         )
      *     )
      * )
      */
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
