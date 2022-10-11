<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/register/confirm/{token}', 'Auth\RegisterController@confirmEmail')->name('confirm.email');
Route::get('/register/validation/unique/email', 'Auth\RegisterController@validationEmail')->name('validation.unique.email');
Route::get('/jobs/{job_id}/share_request_from/{user_id}', 'HomeController@shareRequestFromMonitoringUser');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('countries', 'HomeController@listCountries')->name('list.countries');
Route::get('counties', 'HomeController@listCounties')->name('list.counties');
Route::get('address_sources', 'HomeController@listAddressSources')->name('list.addresssources');
Route::get('states/{country_name?}', 'HomeController@listStates')->name('list.states');
