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
  // Mastoria
	Route::resource('mastoria', 'MastoriController', ['only' => ['index', 'show', 'store', 'update']]);
	// Users
	Route::resource('users', 'UserController', ['only' => ['index', 'show', 'store', 'update']]);
	// Ratings
	Route::get('ratings', 'RatingController@index');
	Route::post('mastoria/{mastori_id}/ratings', 'RatingController@store');
	Route::put('ratings/{rating_id}', 'RatingController@update');
	// Professions
	Route::resource('professions', 'ProfessionController', ['only' => ['index', 'store', 'update', 'destroy']]);
	// Appointments
	Route::get('appointments', 'AppointmentController@index');
	Route::get('appointments/{appointment_id}', 'AppointmentController@show');
	Route::post('appointments', 'AppointmentController@store');
	Route::patch('appointments/{appointment_id}', 'AppointmentController@arrange');
});
