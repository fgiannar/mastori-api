<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\EndUser;
use App\User;

use Auth;

class EndUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         // TODO Add filters, pagination and return only active usera if NOT admin
        return EndUser::with('user')->get();
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
        $endUser = new EndUser;
        $user = new User;
        $addresses = $data['addresses'];
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

        // Insert related addresses
        $endUser->user->addresses()->delete();
        foreach ($addresses as $address) {
            $endUser->user->addresses()->create($address);
        }

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
            'photo' => 'image',
            'phone' => 'required|max:255',
            'password' => $password_required.'|max:255|min:6',
            'password_repeat' => $password_required.'|same:password|max:255',
            'addresses' => 'required|array',
            'addresses.*.lat' => 'required|numeric',
            'addresses.*.lng' => 'required|numeric',
            'addresses.*.address' => 'required|max:255',
            'addresses.*.friendly_name' => 'max:255',
            'addresses.*.city' => 'required|max:255',
            'addresses.*.country' => 'required|max:255'
        ]);
    }
}
