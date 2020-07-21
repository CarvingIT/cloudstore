<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/select-drive', 'DrivesController@selectDrive');
Route::post('/set-drive', 'DrivesController@setDrive');

// Administration
Route::get('/admin/dashboard', 'AdminDashboardController@index')->middleware('admin');
Route::get('/admin/drives', 'DrivesController@drives')->middleware('admin');
Route::post('/admin/drives/save', 'DrivesController@save')->middleware('admin');
Route::get('/admin/drives/delete/{drive_id}', 'DrivesController@delete')->middleware('admin');
