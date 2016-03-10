<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
*     @SWG\Definition(
*         definition="address",
*         required={"lat", "lng", "address", "city", "country"},
*         @SWG\Property(
*             property="id",
*             type="integer",
*             readOnly=true
*         ),
*         @SWG\Property(
*             property="lat",
*             type="double"
*         ),
*         @SWG\Property(
*             property="lng",
*             type="double"
*         ),
*         @SWG\Property(
*             property="address",
*             type="string"
*         ),
*         @SWG\Property(
*             property="friendly_name",
*             type="string"
*         ),
*         @SWG\Property(
*             property="city",
*             type="string"
*         ),
*         @SWG\Property(
*             property="country",
*             type="string"
*         ),
*         @SWG\Property(
*             property="created_at",
*             type="dateTime",
*             readOnly=true
*         ),
*         @SWG\Property(
*             property="updated_at",
*             type="dateTime",
*             readOnly=true
*         ),
*     )
*/

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
