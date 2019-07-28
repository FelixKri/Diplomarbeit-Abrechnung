<?php

use Illuminate\Support\Facades\Log;

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
    return dd(App\Fos_user::take(20)->get());
});

Route::get('/prescribing/new', 'PrescribingController@create');
Route::post('/prescribing/new', 'PrescribingController@store');
Route::get('/prescribing/show', 'PrescribingController@show');

Route::get('/invoice/new', 'InvoiceController@create');
Route::post('/invoice/new', 'InvoiceController@store');

Route::post('/getUsers','AjaxController@getUsers');
//Route::get('/class/autocomplete', 'PrescribingController@autocompleteClass');
//Route::get('/user/autocomplete', 'PrescribingController@autocompleteUser');