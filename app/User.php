<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'users';

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['username', 'email', 'password'];

    /**
    * The attributes that are NOT mass assignable.
    *
    * @var array
    */
    protected $guarded = ['facebook_id'];

    /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
    protected $hidden = ['password', 'remember_token'];

    public function userable()
    {
      return $this->morphTo();
    }

  /**
    * Get the addresses of the user.
    */
    public function addresses()
    {
      return $this->hasMany('App\Address');
    }

}
