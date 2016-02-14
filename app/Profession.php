<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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