<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Rating;

class Mastori extends Model
{
    use \Heroicpixels\Filterable\FilterableTrait;

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

    /**
   * The attributes that are hidden from the response.
   *
   * @var array
   */
    protected $hidden = ['pivot'];



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

    /**
     * Scope a query to only include active mastoria.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * Scope a query to search for q in first_name, last_name, paratsouki, description.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQ($query, $q)
    {
        $like = '%' . $q . '%';
        return $query->where('first_name', 'like', $like)->orWhere('last_name', 'like', $like)->orWhere('paratsoukli', 'like', $like)->orWhere('description', 'like', $like);
    }

    /**
     * Scope a query to only include mastoria near (deafult radius is 5km) a certain location.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNear($query, $location, $radius)
    {
        $bindings['location'] = $location;
        $bindings['radius'] = $radius;

        return $query->whereHas('user.addresses', function ($q) use ($bindings) {
          $q->distance($bindings['location'], $bindings['radius']);
        });
    }

}
