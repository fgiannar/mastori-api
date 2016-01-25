<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Mastori;

class MastoriController extends Controller
{
    // TODO Add middleware for checking permissions after auth implementation

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         // TODO Add filters, pagination and return only active mastoria if NOT admin
        return Mastori::with('addresses')->get();
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
            return response($validator->messages(), 400);
        }

        // store
        $mastori = new Mastori;
        $addresses = $data['addresses'];
        $data = array_except($data, ['password_repeat', 'addresses']);
        // Encrypt password
        $data['password'] = bcrypt($data['password']);

        $newMastori = $mastori->create($data);
        // Insert related addresses
        foreach ($addresses as $address) {
            $newMastori->addresses()->create($address);
        }

        return response($newMastori->load('addresses'), 201);
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
        return Mastori::with('addresses')->find($id);
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
            return response($validator->messages(), 400);
        }

        // store
        $mastori = Mastori::find($id);
        $addresses = $data['addresses'];
        $data = array_except($data, ['password_repeat', 'addresses']);
        // Encrypt password
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $mastori->update($data);

        // Insert related addresses
        $mastori->addresses()->delete();
        foreach ($addresses as $address) {
            $mastori->addresses()->create($address);
        }

        return $mastori->load('addresses');
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
    protected function validator(array $data, $id = 0)
    {
        $password_required = $id == 0 ? 'required' : '';
        return Validator::make($data, [
            'username' => 'required|max:255|unique:mastoria,username,' . $id,
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'paratsoukli' => 'max:255',
            'pricelist' => 'required',
            'photo' => 'image',
            'phone' => 'required|max:255',
            'password' => $password_required.'|max:255|min:6',
            'password_repeat' => $password_required.'|same:password|max:255',
            'email' => 'email|max:255',
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
