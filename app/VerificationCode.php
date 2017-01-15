<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
  const CODELENGTH = 5; //code lenth
  const CODEDURATION = 600; //expire in 10 minutes
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


  /**
   * Generates a new veridifaction code for user
   * @param int $userId
   * @return  string, random digits row
   */
  public static function generate($userId) {
    $verificationData = [];
    $code = rand(pow(10, self::CODELENGTH-1), pow(10, self::CODELENGTH)-1);
    $verificationData['code'] = $code;
    $verificationData['user_id'] = $userId;
    //expire in 10 minutes
    $verificationData['expiration'] = date('Y-m-d H:i:s', time()+self::CODEDURATION);
    $vObj = VerificationCode::create($verificationData);
    return $vObj;
  }


}
