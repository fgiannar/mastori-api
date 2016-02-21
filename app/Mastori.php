<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Rating;

class Mastori extends Model
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
    protected $fillable = ['first_name', 'last_name', 'paratsoukli', 'description', 'pricelist', 'photo', 'phone'];


    public function user()
    {
        return $this->morphOne('App\User', 'userable');
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
    * Override toArray function
    * @return mastori with relations, user data and average rating
    */
    public function toArray()
    {
        $array = parent::toArray();

        if (isset($array['user'])) {
            $array['username'] = $this->user->username;
            $array['email'] = $this->user->email;
            $array['facebook_id'] = $this->user->facebook_id;
            $array['addresses'] = $this->user->addresses;

            unset($array['user']);
        }

        return $array;
    }

}
