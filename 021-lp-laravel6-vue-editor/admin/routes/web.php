<?php


use Illuminate\Support\Facades\Storage;

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

Auth::routes();

Route::get('/editor', 'EditorController@index')->name('editor');
//Route::post('/fileupload', 'EditorController@fileupload')->name('fileupload');


Route::post('fileupload',function(){
    //dd(request()->all());

    $file_name = request()->file->getClientOriginalName();
    //dd(request()->file);

    request()->file('file')->storeAs('public/',$file_name);
    //Storage::disk('local')->put($file_name, request()->file);

    //Storage::put('file.jpg', 'ss');

});


Route::post('/add', 'EditorController@addNews')->name('add');

Route::post('/updateNews', 'EditorController@updateNews')->name('update');
Route::post('/deleteNews', 'EditorController@deleteNews')->name('delete');
