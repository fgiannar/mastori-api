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
  protected $hidden = ['polygon'];


   /**
    * Get the mastoria that serve the area
    */
    public function mastoria()
    {
        return $this->belongsToMany('App\Mastori', 'mastoria_areas');
    }


}
