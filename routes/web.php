<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::group(['namespace'=>'App\Http\Controllers'], function() {
    Route::get('/', 'ProductController@index')->name('product.index');
    Route::post('/json/upload', 'ProductController@upload')->name('product.index.upload.post');

    Route::get('/product/{id}', 'ProductController@detail')->name('product.detail');
    Route::get('/product/delete/{id}', 'ProductController@delete')->name('product.delete');
});
    
Route::get('/schedule' , function(){
    Artisan::call('app:productupload');
    return 'OK';
});