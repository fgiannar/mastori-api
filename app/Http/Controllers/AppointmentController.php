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
   * @SWG\Get(
   *     path="/appointments",
   *     description="Returns all the appointments of the auth user",
   *     operationId="getAppointments",
   *     tags={"appointments"},
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
   *         description="All users appointments response",
   *         @SWG\Schema(
   *             type="array",
   *             @SWG\Items(ref="#/definitions/appointment")
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
            'user_id'   => 'end_user_id',
            'mastori_id' => 'mastori_id',
            'status' => 'status',
            'created_at' => 'created_at'
        ];

        $appointments = Appointment::filterColumns($filterColumns)->with('address');
        switch (Auth::user()->userable_type) {
            case 'App\EndUser':
                $appointments = $appointments->where('end_user_id', Auth::user()->userable->id)->with('mastori');
                break;
            case 'App\Mastori':
                $appointments = $appointments->where('mastori_id', Auth::user()->userable->id)->with('user');
                break;
            default:
                # code...
                break;
        }

        return $appointments->paginate($request->input('per_page'));
    }



    /**
     * @SWG\Post(
     *     path="/appointments",
     *     operationId="addAppointment",
     *     tags={"appointments"},
     *     description="Adds a new appointment in database",
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
     *         @SWG\Schema(ref="#/definitions/appointmentPost")
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returns created appointment object",
     *         @SWG\Schema(ref="#/definitions/appointment")
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
     * @SWG\Patch(
     *     path="/appointments/{appointment_id}",
     *     operationId="editAppointment",
     *     tags={"appointments"},
     *     description="Edits an appointment in database",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Access token",
     *         in="header",
     *         name="Authorization",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="ID of appointement to edit",
     *         in="path",
     *         name="appointment_id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/appointmentPost")
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returns edited appointment object",
     *         @SWG\Schema(ref="#/definitions/appointment")
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
     *     )
     * )
     */
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
