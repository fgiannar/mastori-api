<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
*     @SWG\Definition(
*         definition="appointment",
*         required={"id", "issue", "available_datetimes", "deadline", "additional_comments", "status", "mastori", "user", "address"},
*         @SWG\Property(
*             property="id",
*             type="integer",
*             readOnly=true
*         ),
*         @SWG\Property(
*             property="issue",
*             type="string"
*         ),
*         @SWG\Property(
*             property="available_datetimes",
*             type="string"
*         ),
*         @SWG\Property(
*             property="additional_comments",
*             type="string"
*         ),
*         @SWG\Property(
*             property="deadline",
*             type="string",
*             format="date-time",
*         ),
*         @SWG\Property(
*             property="status",
*             type="string",
*             enum={ "pending", "approved", "cancelled"},
*             default="pending"
*         ),
*         @SWG\Property(
*             property="mastori",
*             ref= "#/definitions/userShortInfo"
*         ),
*         @SWG\Property(
*             property="user",
*             ref= "#/definitions/userShortInfo"
*         ),
*         @SWG\Property(
*             property="address",
*             ref= "#/definitions/address"
*         ),
*         @SWG\Property(
*             property="created_at",
*              type="string",
*              format="date-time",
*             readOnly=true
*         ),
*         @SWG\Property(
*             property="updated_at",
*              type="string",
*              format="date-time",
*             readOnly=true
*         ),
*     )
*/
class Appointment extends Model
{

    use \Heroicpixels\Filterable\FilterableTrait;

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'appointments';

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['end_user_id', 'mastori_id', 'address_id', 'issue', 'available_datetimes', 'deadline', 'additional_comments'];

    /**
    * The attributes that are NOT mass assignable.
    *
    * @var array
    */
    protected $guarded = ['status'];

    /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
    protected $hidden = ['end_user_id', 'mastori_id', 'response_time', 'address_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'available_datetimes' => 'array'
    ];

    /**
    * Get the mastori of the appointment.
    */
    public function mastori()
    {
        return $this->belongsTo('App\Mastori')->select(array('id', DB::raw('CONCAT(first_name, " ", last_name) AS name')));
    }

    /**
    * Get the user that created the appointment
    */
    public function user()
    {
        $user = $this->belongsTo('App\EndUser', 'end_user_id');

        return $this->status == 'approved' ? $user->select(array('id', 'name', 'phone')) : $user->select(array('id', 'name'));
    }


    /**
    * Get the address
    */
    public function address()
    {
        return $this->belongsTo('App\Address');
    }

}
