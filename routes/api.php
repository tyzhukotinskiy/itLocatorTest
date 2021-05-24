<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login'])->name('login');

Route::middleware('auth:api')->group( function () {
    Route::post('details', [\App\Http\Controllers\UserController::class, 'details']);
    Route::post('landings/get-domains', [LandingController::class, 'getDomains']);
    Route::post('landings/get-templates', [LandingController::class, 'getTemplates']);
    Route::post('landings/get-my-landing', [LandingController::class, 'getMyLanding']);
    Route::post('landings/add-landing', [LandingController::class, 'addLanding']);
    Route::post('landings/add-image', [LandingController::class, 'addImage'])->name('images.store');
    Route::resource('landings', LandingController::class);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
