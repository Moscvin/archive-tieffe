<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware('api.appToken')->group(function () {
    Route::any('/',function (Request $request){
       return response()->json(['status'=>'ok','go'=>[
           '/api/v1/login',
           '/api//v1/logout',
           '/api/v1/change_password',
           '/api/v1/first_login',
           '/api/v1/auto_login',
           '/api/v1/retrieve_password',

           '/api/v1/home',
           '/api/v1/calendario',
           '/api/v1/invia_rapporto',
           '/api/v1/invia_rapporto_foto',
           '/api/v1/intervento',
           '/api/v1/pronto_intervento',
           '/api/v1/incarico_pronto_intervento',
           '/api/v1/tecnici',

           '/api/v1/materiali',
           '/api/v1/materiali_add',

           '/api/v1/nuovo_intervento',
           '/api/v1/clienti',
           '/api/v1/riepilogo',
           '/api/v1/rapporti',
           '/api/v1/invia_lavoro_interno',
           '/api/v1/lavori_interni',
           '/api/v1/interventi_non_completati',
           '/api/v1/commesse_list',
           '/api/v1/commesse_lavori_list',
           '/api/v1/commesse_lavori_add',

           '/api/v1/committenti_list'

           //custom routes
       ]]);
    });

    Route::post('/login','Api\ApiAuthController@login');
    Route::post('/first_login','Api\ApiAuthController@firstLogin');
    Route::post('/retrieve_password','Api\ApiAuthController@retrievePassword');

    Route::middleware('api.userToken')->group(function () {
        Route::post('/logout','Api\ApiAuthController@logout');
        Route::post('/change_password','Api\ApiAuthController@passwordChange');
        Route::post('/auto_login','Api\ApiAuthController@automaticLogin');

        Route::post('/invia_rapporto','Api\ReportController@getReport');
        Route::post('/home','Api\HomeController@home');
        Route::post('/riepilogo','Api\ReportController@sendReportsByDate');
        Route::post('/calendario','Api\HomeController@getOperationsByDate');
        Route::post('/intervento','Api\OperationController@getOperationData');
        Route::post('/update_tehnicians','Api\OperationController@updateOperationTehnicians');

        Route::post('/pronto_intervento','Api\OperationController@getUrgentOperationData');
        Route::post('/incarico_pronto_intervento','Api\OperationController@assumeUrgentOperation');
        Route::post('/nuovo_intervento','Api\OperationController@createNewOperation');

        Route::post('/tecnici','Api\GeneralDataController@getTehniciansByUserType');

        Route::post('/materiali','Api\GeneralDataController@getEquipment');

        Route::post('/clienti','Api\GeneralDataController@getClients');
        Route::post('/rapporti','Api\GeneralDataController@rapporti');

        Route::post('/invia_lavoro_interno','Api\InternalWorkController@setInternalWork');
        Route::post('/lavori_interni','Api\InternalWorkController@sendInternalWorks');
        Route::post('/interventi_non_completati','Api\InternalWorkController@sendNotFinishedOperations');

        Route::post('/commesse_list','Api\OrdersController@list');
        Route::post('/commesse_lavori_list','Api\OrdersWorkController@list');
        Route::post('/commesse_lavori_add','Api\OrdersWorkController@add');
        Route::post('/materiali_add','Api\OrdersWorkController@add_material');

        Route::get('/committenti_list','Api\BuyersController@list');

        Route::post('/invia_rapporto_foto','Api\Operation\Report\PhotoController@main');

        Route::group(['namespace' => 'Api'], function() {
            Route::group(['namespace' => 'Client', 'prefix' => 'client'], function() {
                Route::post('/','NewController@main');

                Route::group(['namespace' => 'Headquarter', 'prefix' => '{idClient}/headquarter'], function() {
                    Route::get('/','ListController@main');
                    Route::post('/','NewController@main');

                    Route::group(['namespace' => 'Machinery', 'prefix' => '{idHeadquarter}/machinery'], function() {
                        Route::post('/','NewController@main');
                        Route::put('/edit','NewController@update');
                    });
                });
            });

            Route::group(['namespace' => 'Technician', 'prefix' => 'technician'], function() {
                Route::get('/', 'ListController@main');
                Route::post('/coordinate', 'CoordinateController@main');
            });
            Route::group(['namespace' => 'Operation', 'prefix' => 'operation'], function() {
                Route::post('/', 'NewController@main');
                Route::get('/date', 'DateController@main');
                Route::get('/{id}', 'GetController@main');
                Route::post('/{id}', 'EditController@main');
                Route::post('/{id}/not_assigned', 'EditNotAssignedController@main');

                Route::group(['namespace' => 'Report', 'prefix' => '{idOperation}/report'], function() {
                    Route::get('{id}', 'GetController@main');
                    Route::post('/', 'NewController@main');
                });
            });
        });
    });
});

Route::post('/comuni','Addresses\ComuniController@api');
Route::post('/autocomplete/{id}','ClientiController@search');
Route::post('/clienti/download','ClientiController@download');
