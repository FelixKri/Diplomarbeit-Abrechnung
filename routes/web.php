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
    return view('index');
});

Route::get('/prescribing/new', 'PrescribingController@create');
Route::post('/prescribing/new', 'PrescribingController@store');
Route::get('/prescribing/list', 'PrescribingController@show');
Route::get('/prescribing/list/getPrescribings', 'PrescribingController@getPrescribings');
Route::get('/prescribing/view/{id}', 'PrescribingController@showDetail');
Route::get('/prescribing/view/getPrescribing/{id}', 'PrescribingController@getPrescribingById');
Route::post('/prescribing/update', 'PrescribingController@update');

Route::post('/prescribing/setApproved/{id}', 'PrescribingController@setApproved');
Route::post('/prescribing/setFinished/{id}', 'PrescribingController@setFinished');
Route::post('/prescribing/reject/{id}', 'PrescribingController@reject');

Route::get('/invoice/new', 'InvoiceController@create');
Route::post('/invoice/new', 'InvoiceController@store');
Route::get('/invoice/list', 'InvoiceController@show');
Route::get('/invoice/view/{id}', 'InvoiceController@showDetail');
Route::post('/invoice/update', 'InvoiceController@update');
Route::get('/invoices/list/getInvoices', 'InvoiceController@getInvoices');
Route::get('/invoices/view/getInvoice/{id}', 'InvoiceController@getInvoiceById');

Route::get('/invoice/download/{id}', 'PDFController@downloadInvoiceById');
Route::get('/prescribing/download/{id}', 'PDFController@downloadPrescribingById');

Route::post('/getUsers','AjaxController@getUsers');  //Sollte Get request sein
Route::get('/user/getById/{id}', 'AjaxController@getUserById');
Route::post('/getAllGroups','AjaxController@getAllGroups'); // Method: POST Name: GETAllGroups *** Das sollte eine get request sein, man gettet ja was
Route::get('/getReasons', 'AjaxController@getReasons');


Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
