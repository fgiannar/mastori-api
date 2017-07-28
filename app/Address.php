<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Area;
use Log;

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
*             type="number",
*             format="double"
*         ),
*         @SWG\Property(
*             property="lng",
*             type="number",
*             format="double"
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
*              type="string",
*              format="date-time",
*              readOnly=true
*         ),
*         @SWG\Property(
*             property="updated_at",
*              type="string",
*              format="date-time",
*             readOnly=true
*         )
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
    protected $fillable = ['friendly_name', 'lat', 'lng', 'address', 'city', 'country', 'streetname', 'streetnumber', 'zipcode', 'notes', 'user_id', 'area_id'];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
    protected $hidden = ['user_id', 'location', 'created_at', 'updated_at'];

  /**
   * Override save method to add geolocation and area_id column
   *
   */
    public function save(array $options = array())
    {

        $this->location = DB::raw("POINT($this->lat, $this->lng)");

        $locationArea = Area::getAreaFromLocation($this->lng, $this->lat);
        $this->area_id = is_null($locationArea) ? null : $locationArea->id;

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

    /**
      * Get the user of the address
      */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
