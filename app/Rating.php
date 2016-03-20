<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;


/**
*     @SWG\Definition(
*         definition="rating",
*         required={"id", "rating", "editing_expires_at", "body", "status", "enduser", "mastori"},
*         @SWG\Property(
*             property="id",
*             type="integer",
*             readOnly=true
*         ),
*         @SWG\Property(
*             property="rating",
*             type="integer"
*         ),
*         @SWG\Property(
*             property="editing_expires_at",
*             type="string",
*             format="date-time",
*         ),
*         @SWG\Property(
*             property="body",
*             type="string"
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


/**
*     @SWG\Definition(
*         definition="rating_post",
*         required={ "rating", "body"},
*         @SWG\Property(
*             property="rating",
*             type="integer"
*         ),
*         @SWG\Property(
*             property="body",
*             type="string"
*         )
*     )
*/


/**
*     @SWG\Definition(
*         definition="userShortInfo",
*         required={"name", "id"},
*         @SWG\Property(
*             property="id",
*             type="integer",
*             readOnly=true
*         ),
*         @SWG\Property(
*             property="name",
*             type="string"
*         )
*     )
*/


/**
*     @SWG\Definition(
*         definition="idObj",
*         required={ "id"},
*         @SWG\Property(
*             property="id",
*             type="integer"
*         )
*     )
*/

class Rating extends Model
{

    use \Heroicpixels\Filterable\FilterableTrait;

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'ratings';

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['end_user_id', 'mastori_id', 'body', 'rating', 'editing_expires_at', 'status'];

    // protected $guarded = ['editing_expires_at', 'status', 'user_id', 'mastori_id'];
    /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
    protected $hidden = ['end_user_id', 'mastori_id'];

    /**
    * Get the mastori of the rating.
    */
    public function mastori()
    {
        return $this->belongsTo('App\Mastori')->select(array('id', DB::raw('CONCAT(first_name, " ", last_name) AS name')));
    }

    /**
    * Get the user that created the rating
    */
    public function user()
    {
        return $this->belongsTo('App\EndUser', 'end_user_id')->select(array('id', 'name'));
    }

    /**
     * Scope a query to only include active ratings.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

}
