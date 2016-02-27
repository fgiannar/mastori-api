<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

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
  protected $hidden = ['user_id', 'id', 'location', 'created_at', 'updated_at'];

  /**
   * Override save method to add geolocation column
   *
   */
  public function save(array $options = array())
  {

      $this->location = DB::raw("POINT($this->lat, $this->lng)");

      parent::save($options);
   }

   /**
     * Scope a query to only include addresses in certain distance from a point
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDistance($query, $location, $dist)
    {
        $dist = $dist ? : 5;

        return $query->whereRaw('ST_Distance(location,POINT('.$location.')) < '. $dist);
    }

}