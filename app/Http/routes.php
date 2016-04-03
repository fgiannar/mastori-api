<?php

use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Mastori Api specification",
 *         description="",
 *         @SWG\Contact(
 *             email="giannaropoulou.foteini@gmail.com;tania.pets@gmail.com"
 *         )
 *     )
 * )
 */

 /**
 *     @SWG\Definition(
 *         definition="errorModel",
 *         required={"error"},
 *         @SWG\Property(
 *             property="error",
 *             type="string",
 *             readOnly=true
 *         )
 *     )
 */

 /**
 *     @SWG\Definition(
 *         definition="fieldValidationDescription",
 *         @SWG\Property(
 *             property="error_description",
 *             type="string",
  *         )
 *     )
 */

 /**
 *     @SWG\Definition(
 *         definition="validationerror",
 *         @SWG\Property(
 *             property="fieldName",
 *             type="array",
 *             @SWG\Items(ref="#/definitions/fieldValidationDescription")
 *         )
 *     )
 */

 /**
 *     @SWG\Definition(
 *         definition="validationsErrorsModel",
 *         required={"errors"},
 *         @SWG\Property(
 *             property="errors",
 *             type="array",
 *             @SWG\Items(ref="#/definitions/validationerror")
 *         )
 *     )
 */


/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['api']], function () {
  // Register Mastori
  Route::post('mastoria', ['middleware' => ['guest'], 'uses' => 'MastoriController@store']);
  // Register End User
  Route::post('users', ['middleware' => ['guest'], 'uses' => 'EndUserController@store']);
  // Authenticate users
  Route::post('auth', ['middleware' => ['guest'], 'uses' => 'Auth\AuthenticateController@authenticate']);
  // FB Auth
  Route::post('auth/facebook', ['uses' => 'Auth\AuthenticateController@facebook']);

  Route::group(['middleware' => ['jwt.auth'/*, 'jwt.refresh'*/]], function() {
    // Mastoria
    Route::resource('mastoria', 'MastoriController', ['only' => ['index', 'show']]);
    Route::put('mastoria/{id}', ['middleware' => ['roles:mastori'], 'uses' => 'MastoriController@update']);
    // Users
    // TODO admin only
    Route::resource('users', 'EndUserController', ['only' => ['index', 'show']]);
    Route::put('users/{id}', ['middleware' => ['roles:enduser'], 'uses' => 'EndUserController@update']);
    //Route::get('users/mobileactivate', 'EndUserController@mobileactivate');

    // Ratings
    Route::get('ratings', 'RatingController@index');
    Route::post('mastoria/{mastori_id}/ratings', ['middleware' => ['roles:enduser'], 'uses' => 'RatingController@store']);
    Route::put('ratings/{rating_id}', ['middleware' => ['roles:enduser'], 'uses' => 'RatingController@update']);
    // Professions
    Route::resource('professions', 'ProfessionController', ['only' => ['index', 'store', 'update', 'destroy']]);
    // Appointments
    Route::get('appointments', 'AppointmentController@index');
    // Route::get('appointments/{appointment_id}', 'AppointmentController@show');
    Route::post('appointments', ['middleware' => ['roles:enduser'], 'uses' => 'AppointmentController@store']);
    Route::patch('appointments/{appointment_id}', ['middleware' => ['roles:mastori'], 'uses' => 'AppointmentController@arrange']);
    // Favorites
    Route::get('users/{user_id}/favorites', ['middleware' => ['roles:enduser|admin'], 'uses' => 'FavoriteController@favorites']);
    Route::post('favorites', ['middleware' => ['roles:enduser'], 'uses' => 'FavoriteController@add']);
    Route::delete('favorites/{mastori_id}', ['middleware' => ['roles:enduser'], 'uses' => 'FavoriteController@remove']);
    // Unlink social account (up to now Facabook)
    Route::post('auth/unlink', 'Auth\AuthenticateController@unlink');
  });

  Route::get('test', 'TestController@test');

});
