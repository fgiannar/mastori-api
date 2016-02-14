<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;

class UserController extends Controller
{
    // TODO Add middleware for checking permissions after auth implementation

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         // TODO Add filters, pagination and return only active usera if NOT admin
        return User::with('addresses')->get();
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
        $user = new User;
        $addresses = $data['addresses'];
        $data = array_except($data, ['password_repeat', 'addresses']);
        // Encrypt password
        $data['password'] = bcrypt($data['password']);

        $newUser = $user->create($data);
        // Insert related addresses
        foreach ($addresses as $address) {
            $newUser->addresses()->create($address);
        }

        return response($newUser->load('addresses'), 201);
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
        return User::with('addresses')->find($id);
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

        $validator = $this->validator($data, $id);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        // store
        $user = User::find($id);
        $addresses = $data['addresses'];
        $data = array_except($data, ['password_repeat', 'addresses']);
        // Encrypt password
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        // Insert related addresses
        $user->addresses()->delete();
        foreach ($addresses as $address) {
            $user->addresses()->create($address);
        }

        return $user->load('addresses');
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
    protected function validator(array $data, $id = 0)
    {
        $password_required = $id == 0 ? 'required' : '';
        return Validator::make($data, [
            'email' => 'required|max:255|unique:users,email,' . $id,
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
