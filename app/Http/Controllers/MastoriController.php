<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Mastori;
use App\User;

use Auth;
use Swagger\Annotations as SWG;



class MastoriController extends Controller
{
  /**
   * @SWG\Get(
   *     path="/mastoria",
   *     description="Returns all the mastoria",
   *     operationId="getMastoria",
   *     tags={"mastoria"},
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
   *         description="All mastoria response",
   *         @SWG\Schema(
   *             type="array",
   *             @SWG\Items(ref="#/definitions/mastori")
   *         ),
   *     ),
   *     @SWG\Response(
   *         response="400",
   *         description="token_not_provided",
   *         @SWG\Schema(
   *             ref="#/definitions/errorModel"
   *         )
   *     )
   * )
   */
    public function index(Request $request)
    {
        $filterColumns = [
            'active'    => 'active',
            'last_name' => 'last_name',
            'avg_rating' => 'avg_rating',
            'profession' => 'mastoria_professions.mastori_id',
            'created_at' => 'created_at'
        ];

        $mastoria = Mastori::join('mastoria_professions', 'mastoria.id', '=', 'mastoria_professions.mastori_id')->filterColumns($filterColumns)->select('mastoria.*');
        if ($request->input('q')) {
            $mastoria = $mastoria->q($request->input('q'));
        }
        if ($request->input('near')) {
            $mastoria = $mastoria->near($request->input('near'), $request->input('radius'));
        }
        if (Auth::user()->userable_type !== 'App\Admin') {
            $mastoria = $mastoria->active();
        }

        return $mastoria->with('user')->with('professions')->paginate($request->input('per_page'));
    }

  /**
   * @SWG\Post(
   *     path="/mastoria",
   *     operationId="addMastori",
   *     tags={"mastoria"},
   *     description="Adds a new mastori in database",
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
   *         @SWG\Schema(ref="#/definitions/mastori")
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="Returns created Mastori object",
   *         @SWG\Schema(ref="#/definitions/mastori")
   *     ),
   *     @SWG\Response(
   *         response="400",
   *         description="unauthorized",
   *         @SWG\Schema(ref="#/definitions/errorModel")
   *     ),
   *     @SWG\Response(
   *         response="500",
   *         description="error",
   *         @SWG\Schema(ref="#/definitions/errorModel")
   *     )
   * )
   */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = $this->validator($data);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        // store
        $mastori = new Mastori;
        $user = new User;

        $addresses = $data['addresses'];
        $professions = array_pluck($data['professions'], 'id');
        $data = array_except($data, ['password_repeat', 'addresses', 'professions']);
        $userdata = array_only($data, ['email', 'username', 'password']);
        $mastoridata = array_except($data, ['email', 'username', 'password']);

        // Encrypt password
        $userdata['password'] = bcrypt($data['password']);

        $newMastori = $mastori->create($mastoridata);
        $newUser = $user->create($userdata);

        // Insert related addresses
        foreach ($addresses as $address) {
            $newUser->addresses()->create($address);
        }
        $newMastori->user()->save($newUser);

        // Sync professions
        $newMastori->professions()->sync($professions);

        return response($newMastori->load('user')->load('professions'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // TODO Add check if mastori is active if NOT admin
        return Mastori::findOrFail($id)->load('user');
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
        $mastori = Mastori::findOrFail($id);

        if ($mastori->id !== Auth::user()->userable->id) {
            return response('Unauthorised', 401);
        }

        $data = $request->all();

        $validator = $this->validator($data, $mastori->user->id);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        $addresses = $data['addresses'];
        $professions = array_pluck($data['professions'], 'id');
        $data = array_except($data, ['password_repeat', 'addresses', 'professions']);
        $userdata = array_only($data, ['email', 'username', 'password']);
        $mastoridata = array_except($data, ['email', 'username', 'password']);

        $data = array_except($data, ['password_repeat', 'addresses', 'professsions']);
        // Encrypt password
        if (isset($userdata['password'])) {
            $userdata['password'] = bcrypt($userdata['password']);
        }
         // store
        $mastori->update($mastoridata);
        $mastori->user()->update($userdata);

        // Insert related addresses
        $mastori->user->addresses()->delete();
        foreach ($addresses as $address) {
            $mastori->user->addresses()->create($address);
        }
        // Sync professions
        $mastori->professions()->sync($professions);

        return $mastori->load('professions');
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
     * Get a validator for a mastori create/update request.
     *
     * @param  array  $data
     * @param  int  $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $user_id = 0)
    {
        $password_required = $user_id == 0 ? 'required' : '';
        return Validator::make($data, [
            'username' => 'required_without:email|max:255|unique:users,username,' . $user_id,
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'paratsoukli' => 'max:255',
            'pricelist' => 'required',
            'photo' => 'image',
            'phone' => 'required|max:255',
            'password' => $password_required.'|max:255|min:6',
            'password_repeat' => $password_required.'|same:password|max:255',
            'email' => 'required_without:username|email|max:255|unique:users,email,' . $user_id,
            'addresses' => 'required|array|min:1|max:10',
            'addresses.*.lat' => 'required|numeric|between:-90,90',
            'addresses.*.lng' => 'required|numeric|between:-180,180',
            'addresses.*.address' => 'required|max:255',
            'addresses.*.friendly_name' => 'max:255',
            'addresses.*.city' => 'required|max:255',
            'addresses.*.country' => 'required|max:255',
            'professions' => 'required|array|min:1|max:5',
            'professions.*.id'  => 'required|exists:professions,id'
        ]);
    }
}
