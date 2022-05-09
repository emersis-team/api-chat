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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group([
//     'prefix' => 'v1',
//     'namespace' => 'API',
//     'name' => 'api.',

// ], function () {
//     Route::prefix('auth')->group(function () {
//         Route::post('/login', 'LoginController@login')->name('login');
//         Route::post('/logout', 'LoginController@logout')->middleware('auth:sanctum');
//     });
//  });

// Route::group([
//     'middleware' => ['auth:sanctum'],
//     'prefix' => 'v1',
//     'namespace' => 'API',
//     'name' => 'api.',

// ], function () {
//     Route::prefix('messages')->group(function () {
//         Route::get('/', 'MessagesController@getConversations');
//         Route::get('/{conversation_id}', 'MessagesController@getMessagesFromConversation');
//         Route::post('/textMessage', 'MessagesController@createTextMessage');
//         Route::post('/fileMessage', 'MessagesController@createFileMessage');
//         Route::post('/positionMessage', 'MessagesController@createPositionMessage');
//     });
//  });

 Route::group([
    'namespace' => 'API',

], function () {
        Route::get('/conversations?user_id={user_id}', 'MessagesController@getConversations');
        Route::get('/{user_id}/conversations?user_id={user_id}&conversation_id={conversation_id}', 'MessagesController@getMessagesFromConversation');
        Route::post('/{user_id}/textMessage', 'MessagesController@createTextMessage');
        Route::post('/{user_id}/fileMessage', 'MessagesController@createFileMessage');
        Route::post('/{user_id}/positionMessage', 'MessagesController@createPositionMessage');

 });;
