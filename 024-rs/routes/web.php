<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SigninController;
use App\Http\Controllers\ReportWeeklyController;
use App\Http\Controllers\ReportDailyController;

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
    return view('signin');
});

Route::post('/signin', [SigninController::class, 'signin']);

Route::get('/report_weekly', [ReportWeeklyController::class, 'index']);
Route::post('/report_weekly', [ReportWeeklyController::class, 'index']);

Route::get('/report_daily', [ReportDailyController::class, 'index']);
Route::post('/report_daily/save', [ReportDailyController::class, 'saveReports']);
Route::get('/report_daily/signout', [ReportDailyController::class, 'signOut']);

