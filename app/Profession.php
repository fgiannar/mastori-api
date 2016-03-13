<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
*     @SWG\Definition(
*         definition="profession",
*         required={"tag", "title"},
*         @SWG\Property(
*             property="id",
*             type="integer",
*             readOnly=true
*         ),
*         @SWG\Property(
*             property="tag",
*             type="string"
*         ),
*         @SWG\Property(
*             property="title",
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
*     )
*/

class Profession extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'professions';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['tag', 'title'];

  /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
  protected $hidden = ['created_at', 'updated_at', 'pivot'];

  /**
    * Get the mastoria of the profession.
    */
    public function mastoria()
    {
        return $this->belongsToMany('App\Mastori', 'mastoria_professions');
    }
}
