<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

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
    protected $hidden = ['password', 'remember_token'];

}
