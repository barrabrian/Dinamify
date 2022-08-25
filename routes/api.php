<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\AlternativesController;
use App\Http\Controllers\ActiveCampaignPluginsController;
use App\Http\Controllers\ResponsesController;
use App\Http\Controllers\AutomationsController;

use App\Http\Controllers\Api\TypeformApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/questions', [QuestionsController::class, 'index']);
Route::get('/responses', [ResponsesController::class, 'index']);
Route::get('/alternatives', [AlternativesController::class, 'index']);
Route::get('/flows/test', [AutomationsController::class, 'test']);
Route::get('/activecampaign/callApi', [ActiveCampaignPluginsController::class, 'callApi']);
Route::get('flows/execute', [AutomationsController::class, 'executeOnly']);


Route::post('/typeform', [TypeformApiController::class, 'handleWebhook'])
    ->name('typeform.webhooktarget');