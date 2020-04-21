<?php

use Illuminate\Support\Facades\Log;

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

Route::post('/prescribing/release/{id}', 'PrescribingController@release');
Route::post('/prescribing/setFinished/{id}', 'PrescribingController@setFinished');
Route::post('/prescribing/reject/{id}', 'PrescribingController@reject');

Route::get('/invoice/new', 'InvoiceController@create');
Route::post('/invoice/new', 'InvoiceController@store');
Route::get('/invoice/list', 'InvoiceController@show');
Route::get('/invoice/view/{id}', 'InvoiceController@showDetail');
Route::post('/invoice/update', 'InvoiceController@update');
Route::post('/invoice/release/{id}', 'InvoiceController@release');
Route::post('/invoice/reject/{id}', 'InvoiceController@reject');
Route::post('/invoice/setFinished/{id}', 'InvoiceController@setFinished');
Route::get('/invoices/list/getInvoices', 'InvoiceController@getInvoices');
Route::get('/invoices/view/getInvoice/{id}', 'InvoiceController@getInvoiceById');

Route::get('/invoice/download/{id}', 'PDFController@downloadInvoiceById');
Route::get('/prescribing/download/{id}', 'PDFController@downloadPrescribingById');

Route::post('/getUsers','AjaxController@getUsers');  //Sollte Get request sein
Route::get('/user/getById/{id}', 'AjaxController@getUserById');
Route::get('/getGroups','AjaxController@getGroups');
Route::get('/getReasons', 'AjaxController@getReasons');

//DEBUG?
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
