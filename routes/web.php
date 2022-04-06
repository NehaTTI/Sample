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

Route::get('/chatdemo','App\Http\Controllers\FirebaseController@index')->name('chatdemo');
Route::get('/newchat','App\Http\Controllers\FirebaseController@demo')->name('newchat');
Route::get('/testing','App\Http\Controllers\FirebaseController@testing')->name('testing');
Route::get('/whatsapp','App\Http\Controllers\FirebaseController@whatsapp')->name('whatsapp');
Route::any('/userchathistory','App\Http\Controllers\FirebaseController@userchathistory')->name('userchathistory');
Route::any('/storePopulateCustomers','App\Http\Controllers\CommonController@storePopulateCustomers');
Route::any('/storePopulateBrokers','App\Http\Controllers\CommonController@storePopulateBrokers');
Route::any('/storepopulateproducts','App\Http\Controllers\CommonController@storePopulateProducts');
// Route::any('/storePopulateSaleName','App\Http\Controllers\CommonController@storePopulateSaleName');