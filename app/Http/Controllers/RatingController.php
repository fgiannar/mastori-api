<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Rating;
use App\Mastori;

use Auth;

class RatingController extends Controller
{

  /**
   * @SWG\Get(
   *     path="/ratings",
   *     description="Returns all the ratings",
   *     operationId="getRatings",
   *     tags={"ratings"},
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
   *         description="All ratings response",
   *         @SWG\Schema(
   *             type="array",
   *             @SWG\Items(ref="#/definitions/rating")
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
    public function index(Request $request)
    {
        $filterColumns = [
            'user_id'   => 'end_user_id',
            'mastori_id' => 'mastori_id',
            'rating' => 'rating',
            'created_at' => 'created_at'
        ];
        $ratings = Rating::with('mastori')->with('user')->filterColumns($filterColumns);

        if (Auth::user()->userable_type !== 'App\Admin') {
            $ratings = $ratings->approved();
        }

        return $ratings->paginate($request->input('per_page'));
    }



    /**
     * @SWG\Post(
     *     path="/mastoria/{mastori_id}/ratings",
     *     operationId="addRating",
     *     tags={"mastoria"},
     *     description="Adds a new rating in database",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Access token",
     *         in="header",
     *         name="Authorization",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="ID of mastori to rate",
     *         in="path",
     *         name="mastori_id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/rating_post")
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returns created rating object",
     *         @SWG\Schema(ref="#/definitions/rating")
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
    public function store(Request $request, $mastori_id)
    {
        $mastori = Mastori::findOrFail($mastori_id);

        $data = $request->all();

        $validator = $this->validator($data);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        // store
        $data['end_user_id'] = Auth::user()->userable->id;
        $data['editing_expires_at'] = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $rating = $mastori->ratings()->create($data);

        // Update mastori average rating
        $mastori['avg_rating'] = $mastori->ratings()->avg('rating');
        $mastori->save();

        return response($rating, 201);
    }

    /**
      *@SWG\Put(
      *     path="/ratings/{id}",
      *     @SWG\Parameter(
      *         description="ID of rating to update",
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
      *     operationId="editrating",
      *     tags={"ratings"},
      *     description="Edits a rating",
      *     produces={"application/json"},
      *     @SWG\Parameter(
      *         name="body",
      *         in="body",
      *         required=true,
      *         @SWG\Schema(ref="#/definitions/rating_post")
      *     ),
      *     @SWG\Response(
      *         response=200,
      *         description="Rating response",
      *         @SWG\Schema(ref="#/definitions/rating")
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
        $rating = Rating::findOrFail($id);

        if ($rating['end_user_id'] !== Auth::user()->userable->id) {
            return response('Unauthorized', 401);
        }

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

        // Update mastori average rating
        $mastori = Mastori::find($rating['mastori_id']);
        $mastori['avg_rating'] = $mastori->ratings()->avg('rating');
        $mastori->save();

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
