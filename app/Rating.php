<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Rating extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'ratings';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['end_user_id', 'mastori_id', 'body', 'rating', 'editing_expires_at', 'status'];

  // protected $guarded = ['editing_expires_at', 'status', 'user_id', 'mastori_id'];
  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = ['end_user_id', 'mastori_id'];

  /**
    * Get the mastori of the rating.
    */
  public function mastori()
  {
    return $this->belongsTo('App\Mastori')->select(array('id', DB::raw('CONCAT(first_name, " ", last_name) AS name')));
  }

    /**
    * Get the user that created the rating
    */
  public function user()
  {
    return $this->belongsTo('App\EndUser', 'end_user_id')->select(array('id', 'name'));
  }

}