<?php

namespace App;

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
  protected $fillable = ['friendly_name', 'lat', 'lng', 'address', 'city', 'country'];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = ['user_id', 'mastori_id', 'id'];

}