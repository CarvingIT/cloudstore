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
/*
Route::get('/select-drive', 'DrivesController@selectDrive');
Route::post('/set-drive', 'DrivesController@setDrive');
*/

Route::get('/cloud-drives', 'DrivesController@index');
Route::get('/browse-drive/{drive_id}', 'DrivesController@browse');
Route::get('/list-files/{drive_id}', 'DrivesController@listFiles');

// Administration
Route::get('/admin/dashboard', 'AdminDashboardController@index')->middleware('admin');
Route::get('/admin/drives', 'DrivesController@index')->middleware('admin');
Route::post('/admin/drive/save', 'DrivesController@save')->middleware('admin');
Route::get('/admin/drive/delete/{drive_id}', 'DrivesController@delete')->middleware('admin');

Route::get('/admin/sources', 'SourcesController@index')->middleware('admin');
Route::post('/admin/source/save', 'SourcesController@save')->middleware('admin');
Route::get('/admin/source/delete/{source_id}', 'SourcesController@delete')->middleware('admin');
