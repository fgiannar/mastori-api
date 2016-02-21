<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EndUser extends Model
{
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


  public function user()
  {
     return $this->morphOne('App\User', 'userable');
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