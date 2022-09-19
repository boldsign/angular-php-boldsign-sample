<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\DocumentController;
Route::get('/getDocumentProperties', [DocumentController::class, 'GetDocumentProperties']);
Route::post('/document/send', [DocumentController::class, 'SendDocument']);

use App\Http\Controllers\EmbedDocumentController;
Route::post('/document/createEmbeddedRequestUrl', [EmbedDocumentController::class, 'CreateEmbedDocument']);

use App\Http\Controllers\TemplateController;
Route::post('/template/createEmbeddedRequestUrl', [TemplateController::class, 'CreateEmbeddedRequestUrl']);
Route::post('/embedSigning', [TemplateController::class, 'GetEmbedSigningLink']);
Route::post('/template/send', [TemplateController::class, 'SendTemplate']);