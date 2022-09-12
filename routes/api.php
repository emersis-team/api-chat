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
    //'prefix' => 'v1',
    'namespace' => 'API',
    'name' => 'api.',

], function () {
        //Route::get('/{user_id}/conversations', 'MessagesController@getConversations');
        Route::get('/{user_id}/conversations/{conversation_id}', 'MessagesController@getMessagesFromConversation');        
        Route::post('/textMessage', 'MessagesController@createTextMessage');
        Route::post('/fileMessage', 'MessagesController@createFileMessage');
        Route::post('/positionMessage', 'MessagesController@createPositionMessage');

        //PRUEBA de llamada a API validando el TOKEN via Middleware
        Route::get('/conversations/{conversation_id}', 'MessagesController@getMessagesFromConversationWithMiddlewareTOKEN')->middleware('JWTCidesoMiddleware');

        //PRUEBA de llamada a API validando el TOKEN recibido por header
       // Route::get('/conversations/{conversation_id}', 'MessagesController@getMessagesFromConversationTOKEN');

        

        //User Position
        Route::prefix('position')->group(function () {
            Route::get('/{user_id}/user_positions', 'UserPositionController@getUserPositions');
            Route::get('/{user_id}/last_user_position', 'UserPositionController@getLastUserPosition');
            Route::post('/user_position', 'UserPositionController@createUserPosition');
            Route::get('/{user_id}/user_contacts_positions', 'UserPositionController@getContactsPositions');
        });

 });
