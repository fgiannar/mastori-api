<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\EndUser;
use App\Mastori;

use Auth;

class FavoriteController extends Controller
{

  /**
   * @SWG\Get(
   *     path="/users/{user_id}/favorites",
   *     operationId="getUserFavorites",
   *     tags={"users"},
   *     description="Gets an end user's favorite mastoria",
   *     produces={"application/json"},
   *     @SWG\Parameter(
   *         description="Access token",
   *         in="header",
   *         name="Authorization",
   *         required=true,
   *         type="string"
   *     ),
   *     @SWG\Parameter(
   *         description="ID of end user",
   *         in="path",
   *         name="user_id",
   *         required=true,
   *         type="integer"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="Returns favorite mastoria",
   *         @SWG\Schema(
   *             type="array",
   *             @SWG\Items(ref="#/definitions/mastori")
   *         ),
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function favorites(Request $request, $end_user_id)
    {

        $endUser = EndUser::findOrFail($end_user_id);

        if (Auth::user()->userable_type !== 'App\EndUser' && $endUser->id !== Auth::user()->userable->id) {
            return response('Unauthorised', 401);
        }

        return $endUser->favorites;
    }

    /**
     * @SWG\Post(
     *     path="/favorites",
     *     operationId="addFavorite",
     *     tags={"favorites"},
     *     description="Adds a mastori into favorite list",
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
     *         @SWG\Schema(ref="#/definitions/mastoriId")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK"
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
     * Attach a mastori to current user as favorite.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'mastori_id' => 'required|exists:mastoria,id|unique:favorites,mastori_id,NULL,id,end_user_id,' . Auth::user()->userable->id
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        $favorites = Auth::user()->userable->favorites();
        // store
        return $favorites->attach($data['mastori_id']);
    }



    /**
      *@SWG\Delete(
      *     path="/favorites/{mastori_id}",
      *     @SWG\Parameter(
      *         description="id of mastori to be removed",
      *         in="path",
      *         name="mastori_id",
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
      *     operationId="removefavorite",
      *     tags={"favorites"},
      *     description="Removes a mastori from favorites list",
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
     * Detach a mastori from current user favorites.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove($mastori_id)
    {
        $validator = Validator::make(['mastori_id' => $mastori_id], [
            'mastori_id' => 'required|exists:favorites,mastori_id,end_user_id,' . Auth::user()->userable->id
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        $favorites = Auth::user()->userable->favorites();
        // store
        return $favorites->detach($mastori_id);
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
