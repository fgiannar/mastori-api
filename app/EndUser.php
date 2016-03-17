<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
*     @SWG\Definition(
*         definition="endUser",
*         required={"name", "phone"},
*         @SWG\Property(
*             property="id",
*             type="integer",
*             readOnly=true
*         ),
*         @SWG\Property(
*             property="name",
*             type="string"
*         ),
*         @SWG\Property(
*             property="phone",
*             type="string"
*         ),
*         @SWG\Property(
*             property="photo",
*             type="string"
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
*         @SWG\Property(
*             property="favorites",
*             type="array",
*             @SWG\Items(ref="#/definitions/mastori")
*         ),
*     )
*/

/**
*     @SWG\Definition(
*         definition="endUserPost",
*         required={"name", "email", "phone", "password", "password_repeat", "addresses"},
*         @SWG\Property(
*             property="name",
*             type="string"
*         ),
*         @SWG\Property(
*             property="phone",
*             type="string"
*         ),
*         @SWG\Property(
*             property="email",
*             type="string"
*         ),
*         @SWG\Property(
*             property="password",
*             type="string"
*         ),
*         @SWG\Property(
*             property="password_repeat",
*             type="string"
*         ),
*         @SWG\Property(
*             property="photo",
*             type="string"
*         ),
*         @SWG\Property(
*             property="addresses",
*             type="array",
*             @SWG\Items(ref="#/definitions/address")
*         ),
*         )
*     )
*/



class EndUser extends Model
{

  use \Heroicpixels\Filterable\FilterableTrait;

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'end_users';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'phone' ,'photo'];

   /**
   * The attributes that are hidden from the response.
   *
   * @var array
   */
  protected $hidden = ['pivot'];


  public function user()
  {
     return $this->morphOne('App\User', 'userable');
  }

  public function favorites()
  {
     return $this->belongsToMany('App\Mastori', 'favorites', 'end_user_id', 'mastori_id');
  }

  /**
    * Override toArray function
    * @return array enduser with user data
    */
    public function toArray()
    {
        $array = parent::toArray();

        if (isset($array['user'])) {
          $array['username'] = $this->user->username;
          $array['email'] = $this->user->email;
          $array['facebook_id'] = $this->user->facebook_id;
          $array['addresses'] = $this->user->addresses;

          unset($array['user']);
        }

        return $array;
    }

}
