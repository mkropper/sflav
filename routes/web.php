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
    return view('welcome');
});


Route::get('/hello', function () {
    return 'Hello World';
});


/*
Route::get('/testsalesforce', function () {
    // return view('salesforce');

});
*/

Route::get('/testsalesforce', [
    'uses' => 'ForcecomController@testapi'
]);

Route::get('/export', [
    'uses' => 'ForcecomExportController@export'
]);
