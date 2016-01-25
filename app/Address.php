<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Address extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'addresses';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['friendly_name', 'lat', 'lng', 'address', 'city', 'country', 'user_id', 'mastori_id'];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = ['password', 'remember_token'];

}