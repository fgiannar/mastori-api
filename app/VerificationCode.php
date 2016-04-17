<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
  use \Heroicpixels\Filterable\FilterableTrait;

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'verification_codes';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['code', 'user_id', 'expiration', 'active'];


}
