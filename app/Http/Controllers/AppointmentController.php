<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Appointment;
use App\Address;

use Auth;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         // TODO Add filters, pagination and return appointment depending on user role
        return Appointment::all();
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

        // create appointment address (identical to the user's address but NOT the same)
        $oldAddress = Address::find($data['address_id']);
        $newAddress = new Address();
        $address =  $newAddress->create(array_except($oldAddress->toArray(), ['id']));
        $data = array_except($data, ['address_id']);
        $data['end_user_id'] = Auth::user()->userable->id;
        // store
        $appointment = new Appointment;
        $newAppointment = $appointment->create($data);

        // Insert related address
        $newAppointment->address()->associate($address);

        return response($newAppointment->load('address')->load('mastori'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Appointment::with('address')->with('user')->with('mastori')->findOrFail($id);
    }

    /**
     * Update appointment status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function arrange(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->mastori->id !== Auth::user()->userable->id) {
            return response('Unauthorised', 401);
        }

        if ($appointment['status'] !== 'pending') {
            return response('Appointment has already been ' . $appointment['status'], 401);
        }

        $data = $request->only('status');

        $validator = Validator::make($data, [
            'status' => 'required|in:approved,cancelled'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }

        // store
        $appointment->status = $data['status'];
        $appointment->save();

        return $appointment->load('user');
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
     * Get a validator for a appointment create/update request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // TODO: When authentication is implemented make sure address belongs to current user
        return Validator::make($data, [
            'available_datetimes' => 'required|array',
            'deadline' => 'required|date',
            'issue' => 'required',
            'address_id' => 'required|exists:addresses,id,user_id,' . Auth::user()->id,
            'mastori_id' => 'required|exists:mastoria,id'
        ]);
    }
}
