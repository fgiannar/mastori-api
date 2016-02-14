<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Rating;

class Mastori extends Authenticatable
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mastoria';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'first_name', 'last_name', 'paratsoukli', 'email', 'password', 'description', 'pricelist', 'photo', 'phone'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'pivot'];

    /**
    * Get the addresses of the mastori.
    */
    public function addresses()
    {
      return $this->hasMany('App\Address');
    }

    /**
    * Get the ratings of the mastori.
    */
    public function ratings()
    {
      return $this->hasMany('App\Rating');
    }

    /**
    * Get the professions of the mastori.
    */
    public function professions()
    {
        return $this->belongsToMany('App\Profession', 'mastoria_professions');
    }

    /**
    * Override method to return average rating
    */
    public function toArray()
    {
        $array = parent::toArray();
        $array['rating'] = $this->ratings()->avg('rating');

        return $array;
    }

}
