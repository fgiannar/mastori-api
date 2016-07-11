<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;


/**
*     @SWG\Definition(
*         definition="area",
*         required={"name"},
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

class Area extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'areas';

  public $timestamps = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'polygon', 'parent_id'];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = ['polygon', 'pivot'];

  /**
   * Override save method to add geolocation column
   *
   */
  public function setPolygonAttribute($coords) {
       foreach ($coords as $polygons){
            $arrPoly = array();
            foreach ($polygons as $poly) {

                if (is_array($poly[0])) { // MultiPolygon
                    $arrPoly = array();
                    foreach ($poly as $point){
                        $lat = strval($point[0]);
                        $lng = strval($point[1]);
                        $latLng = $lat . ' ' . $lng;
                        array_push($arrPoly, $latLng);
                    }
                } else { // Polygon
                    $point = $poly;
                    $lat = strval($point[0]);
                    $lng = strval($point[1]);
                    $latLng = $lat . ' ' . $lng;
                    array_push($arrPoly, $latLng);
                }
            }

            $coordsArray[] = '(' . implode($arrPoly, ',') . ')';
        }

        $coordsStr = implode(',', $coordsArray);

        $this->attributes['polygon'] = DB::raw("ST_PolygonFromText('POLYGON($coordsStr)')");
    }

    /**
     * Always json_decode geo_json so they are usable
     */
    // public function getGeoJsonAttribute($value) {
    //     return json_decode($value);
    // }

    /**
     * Always json_encode the geo_json when saving to the database
     */
    // public function setGeoJsonAttribute($value) {
    //     $this->attributes['geo_json'] = json_encode($value);
    // }



   /**
    * Get the mastoria that serve the area
    */
    public function mastoria()
    {
        return $this->belongsToMany('App\Mastori', 'mastoria_areas');
    }


}
