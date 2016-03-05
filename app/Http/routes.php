<?php

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

	Route::group(['middleware' => ['jwt.auth'/*, 'jwt.refresh'*/]], function() {
		// Mastoria
		Route::resource('mastoria', 'MastoriController', ['only' => ['index', 'show']]);
		Route::put('mastoria/{id}', ['middleware' => ['roles:mastori'], 'uses' => 'MastoriController@update']);
		// Users
		// TODO admin only
		Route::resource('users', 'EndUserController', ['only' => ['index', 'show']]);
		Route::put('users/{id}', ['middleware' => ['roles:enduser'], 'uses' => 'EndUserController@update']);
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
		// Route::get('appointments/{appointment_id}', 'AppointmentController@show');
		Route::post('favorites', ['middleware' => ['roles:enduser'], 'uses' => 'FavoriteController@add']);
		Route::delete('favorites/{mastori_id}', ['middleware' => ['roles:enduser'], 'uses' => 'FavoriteController@remove']);
	});

});
