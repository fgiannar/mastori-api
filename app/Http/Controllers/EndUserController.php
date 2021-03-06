<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Address;
use App\EndUser;
use App\User;
use Illuminate\Support\Collection;
use Auth;
use App\Events\UserRegistered;

class EndUserController extends Controller
{

  /**
   * @SWG\Get(
   *     path="/users",
   *     description="Returns all the end users",
   *     operationId="getEndUsers",
   *     tags={"users"},
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
   *         description="All end users response",
   *         @SWG\Schema(
   *             type="array",
   *             @SWG\Items(ref="#/definitions/endUser")
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filterColumns = [
            'name' => 'name',
            'created_at' => 'created_at'
        ];

        return EndUser::with('user')->filterColumns($filterColumns)->paginate($request->input('per_page'));
    }



    /**
     * @SWG\Post(
     *     path="/users",
     *     operationId="addEndUser",
     *     tags={"users"},
     *     description="Adds a new end user in database",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/endUserPost")
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returns created end user object",
     *         @SWG\Schema(ref="#/definitions/mastori")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="validation errors",
     *         @SWG\Schema(ref="#/definitions/validationsErrorsModel")
     *     )
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
        $endUser = new EndUser;
        $user = new User;
        $addresses = isset($data['addresses']) ? $data['addresses'] : [];
        $data = array_except($data, ['password_repeat', 'addresses']);
        $userdata = array_only($data, ['email', 'username', 'password']);
        $enduserdata = array_except($data, ['email', 'username', 'password']);

        // Encrypt password
        $userdata['password'] = bcrypt($data['password']);

        $newEndUser = $endUser->create($enduserdata);
        $newUser = $user->create($userdata);

        // Insert related addresses
        foreach ($addresses as $address) {
            $newUser->addresses()->create($address);
        }
        $newEndUser->user()->save($newUser);

        //@todo make job for this
        event(new UserRegistered($newUser));


        return response($newEndUser->load('user'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // TODO Add check if user is active if NOT admin
        return EndUser::findOrFail($id)->load('user');
    }



    /**
      *@SWG\Put(
      *     path="/users/{id}",
      *     @SWG\Parameter(
      *         description="ID of end user to update",
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
      *     operationId="editenduser",
      *     tags={"users"},
      *     description="Edits an end user",
      *     produces={"application/json"},
      *     @SWG\Parameter(
      *         name="body",
      *         in="body",
      *         required=true,
      *         @SWG\Schema(ref="#/definitions/endUserPost")
      *     ),
      *     @SWG\Response(
      *         response=200,
      *         description="End user response",
      *         @SWG\Schema(ref="#/definitions/endUser")
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
        $endUser = EndUser::findOrFail($id);

        if ($endUser->id !== Auth::user()->userable->id) {
            return response('Unauthorised', 401);
        }

        $data = $request->all();

        $validator = $this->validator($data, $endUser->user->id);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }


        $addresses = $data['addresses'];
        $data = array_except($data, ['password_repeat', 'addresses']);
        $userdata = array_only($data, ['email', 'username', 'password']);
        $enduserdata = array_except($data, ['email', 'username', 'password']);

        // Encrypt password
        if (isset($userdata['password'])) {
            $userdata['password'] = bcrypt($userdata['password']);
        }
         // store
        $endUser->update($enduserdata);
        $endUser->user()->update($userdata);


        // Insert - update - delete related addresses
        $adressesSentCollection = collect($addresses);
        $curentAddresses = $endUser->user->addresses;
        foreach($curentAddresses as $existedAddress) {
          //remove address that is missed from posted
          $keepAddress= $adressesSentCollection->search(function ($item, $key) use($existedAddress){
            if (!isset($item['id'])) { //keep new addresses
              return false;
            }
            return ($item['id'] == $existedAddress->id);
          });
          if ($keepAddress === false) {
            Address::destroy($existedAddress->id);
          }
        }
        //update changed address
        foreach($adressesSentCollection as $updatedAddress) {
          //add new
          $updatedAddress['user_id'] = Auth::user()->id;
          $aId = isset($updatedAddress['id']) && $updatedAddress['id'] > 0 ? $updatedAddress['id'] : 0;
          Address::updateOrCreate(['id'=>$aId], $updatedAddress);
        }
        $endUser->user->load('addresses');

        return $endUser;
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
    protected function validator(array $data, $user_id = 0)
    {
        $password_required = $user_id == 0 ? 'required' : '';
        return Validator::make($data, [
            'email' => 'required|max:255|unique:users,email,' . $user_id,
            'name' => 'required|max:255',
            'photo' => 'nullable|image',
            'phone' => 'required|max:255',
            'password' => $password_required.'|max:255|min:6',
            'password_repeat' => $password_required.'|same:password|max:255',
            'addresses' => 'sometimes|array',
            'addresses.*.lat' => 'required_with:addresses|numeric|between:-90,90',
            'addresses.*.lng' => 'required_with:addresses|numeric|between:-180,180',
            'addresses.*.address' => 'required_with:addresses|max:255',
            'addresses.*.friendly_name' => 'max:255',
            'addresses.*.city' => 'required_with:addresses|max:255',
            'addresses.*.country' => 'required_with:addresses|max:255'
        ]);
    }
}
