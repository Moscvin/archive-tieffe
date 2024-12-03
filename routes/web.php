<?php
Route::middleware('auth')->get('/', 'HomeController@index')->name('home');
Route::middleware('auth')->get('/home', 'HomeController@index')->name('home');
Route::get('/images/{type}/{id}/{filename}', 'Core\CoreImages@show');
Route::group(['as'=>'file'],function (){
    Route::get('/file/{params}', 'FileController@downloadFile')->where('params', '.*');
    Route::get('/api/v1/file/{params}', 'FileController@showImage')->where('params', '.*');
});
//auth
Auth::routes();

Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');
Route::any('/first-login', 'Auth\FirstLoginController@first_login')->name('first-login');
Route::any('/retrieve-password', 'Auth\RetrievePasswordController@getReset')->name('retrieve-password');
Route::get('/password/reset', function () { return redirect('retrieve-password');});
Route::get('/password/email', function () { return redirect('retrieve-password');});

//core_groups = role
Route::group(['as'=>'core_groups'], function(){
    Route::get('/core_groups', 'Core\CoreGroupsController@core_groups');
    Route::any('/core_groups_add/{id?}', 'Core\CoreGroupsController@core_groups_add');
    Route::any('/core_groups_del', 'Core\CoreGroupsController@core_groups_del');
});

//core_admin_option = settings app
Route::group(['as'=>'core_admin_options'],function (){
    Route::get('/core_admin_options/', 'Core\CoreAdminOptionsController@core_admin_options');
    Route::any('/core_admin_options_add/{id?}', 'Core\CoreAdminOptionsController@core_admin_options_add');
    Route::any('/core_admin_options_del', 'Core\CoreAdminOptionsController@core_admin_options_del');
});

//menu
Route::group(['as'=>'core_menu','middleware' => ['auth']],function (){
    Route::get('/core_menu/', 'Core\CoreMenuController@core_menu');
    Route::any('/core_menu_add/{id?}', 'Core\CoreMenuController@core_menu_add');
    Route::get('/core_menu_del/{id}', 'Core\CoreMenuController@core_menu_del');
    Route::any('/getMenuAdmin', 'Core\CoreMenuController@getMenuAdmin');
    Route::any('/setMenuAdmin', 'Core\CoreMenuController@setMenuAdmin');
    Route::any('/del_role_permision', 'Core\CoreMenuController@del_role_permision');
});

//location
Route::group(['as'=>'location','middleware' => ['auth']],function (){
    Route::any('/location', 'LocationController@add');
    Route::any('/location/{id_client?}/add', 'LocationController@add');
    Route::any('/location/{id?}/edit', 'LocationController@edit');
    Route::any('/location/{id?}/view', 'LocationController@view');
    Route::post('/location/{id?}/lock', 'LocationController@lock');
    Route::post('/location/{id?}/delete', 'LocationController@delete');
    Route::any('/location/{id_client}/save', 'LocationController@save');
    Route::any('/location/{id_location}/update', 'LocationController@update');
});

//machinery
Route::group(['as'=>'machinery','middleware' => ['auth']],function (){
    Route::any('/machinery', 'MachineryController@add');
    Route::any('/machinery/{id_location?}/add', 'MachineryController@add');
    Route::any('/machinery/{id?}/edit', 'MachineryController@edit');
    Route::any('/machinery/{id?}/view', 'MachineryController@view');
    Route::post('/machinery/{id?}/delete', 'MachineryController@delete');
    Route::post('/machinery/{id?}/lock', 'MachineryController@lock');
    Route::any('/machinery/{id_location}/save', 'MachineryController@save');
    Route::any('/machinery/{id_machinery}/update', 'MachineryController@update');
});

//user
Route::group(['as'=>'core_user','middleware' => ['auth']],function (){
    Route::any('/core_user/', 'Core\CoreUserController@core_user');
    Route::any('/core_user_add/{id?}/{see?}', 'Core\CoreUserController@core_user_add');
    Route::any('/core_user_block', 'Core\CoreUserController@core_user_block');
    Route::any('/core_user_del', 'Core\CoreUserController@core_user_del');
});

//permission
Route::group(['as'=>'core_permission','middleware' => ['auth']],function (){
    Route::get('/core_permission/', 'Core\CorePermissionController@core_permission');
    Route::any('/core_permission_add', 'Core\CorePermissionController@core_permission_add');
    Route::any('/core_permission_block', 'Core\CorePermissionController@core_permission_block');
    Route::any('core_permission_update','Core\CorePermissionController@core_permission_update');
});

Route::group(['as'=>'core_permission_exceptions','middleware' => ['auth']],function (){
    Route::get('/core_permission_exceptions', 'Core\CorePermissionExceptionsController@core_permission_exceptions');
    Route::get('/core_permission_exceptions_add/{id}', 'Core\CorePermissionExceptionsController@core_permission_exceptions_add');
    Route::any('/core_permission_exceptions_del', 'Core\CorePermissionExceptionsController@core_permission_exceptions_del');
    Route::any('/core_permission_exceptions_edit', 'Core\CorePermissionExceptionsController@core_permission_exceptions_edit');
});

Route::group(['as'=>'nazioni','middleware' => ['auth']],function (){
    Route::get('/nazioni','Addresses\NazzoniControllers@index');
    Route::get('/nazioni/ajax','Addresses\NazzoniControllers@ajax');
    Route::any('/nazioni_add/{id?}', 'Addresses\NazzoniControllers@add');
    Route::any('/nazioni_del', 'Addresses\NazzoniControllers@delete');
});

Route::group(['as'=>'province','middleware' => ['auth']],function (){
    Route::get('/province','Addresses\ProvinceController@index');
    Route::get('/province/ajax','Addresses\ProvinceController@ajax');
    Route::any('/province_add/{id?}', 'Addresses\ProvinceController@add');
    Route::any('/province_del', 'Addresses\ProvinceController@delete');
});

Route::group(['as'=>'comuni','middleware' => ['auth']],function (){
    Route::get('/comuni/{perpage?}','Addresses\ComuniController@index');
    Route::get('/comuni/ajax','Addresses\ComuniController@ajax');
    Route::any('/comuni_add/{id?}','Addresses\ComuniController@add');
    Route::any('/comuni_del','Addresses\ComuniController@delete');
});

Route::group(['as'=>'customers','middleware' => ['auth']],function (){
   Route::get('/customers','ClientiController@index');
   Route::get('/customers/ajax','ClientiController@ajax');
   Route::any('/customer_add/{id?}/{see?}', 'ClientiController@add');
   Route::any('/customers_block', 'ClientiController@block');
   Route::any('/customers_del', 'ClientiController@delete');
   Route::post('/saveClient', 'ClientiController@saveClient');
   Route::get('/customers/{idUser}/intervention/{id}','ClientiController@getIntervention');
});

//App user
Route::group(['as'=>'app_user','middleware' => ['auth']],function (){
    Route::any('/app_user/', 'Core\CoreUserController@app_user');
    Route::any('/app_user_add/{id?}/{see?}', 'Core\CoreUserController@app_user_add');
    Route::any('/core_user_block', 'Core\CoreUserController@core_user_block');
    Route::any('/core_user_del', 'Core\CoreUserController@core_user_del');
});

//Means
Route::group(['as'=>'mean','middleware' => ['auth']],function (){
    Route::any('/mean/', 'MeanController@index');
    Route::any('/mean_add/{id?}/{see?}', 'MeanController@mean_add');
    Route::any('/mean_block', 'MeanController@mean_block');
    Route::any('/mean_del', 'MeanController@mean_del');
});

//Equipments
Route::group(['as'=>'equipment','middleware' => ['auth']],function (){
    Route::any('/equipment/', 'EquipmentController@index');
    Route::any('/equipment_add/{id?}/{see?}', 'EquipmentController@equipment_add');
    Route::any('/equipment_block', 'EquipmentController@equipment_block');
    Route::any('/equipment_del', 'EquipmentController@equipment_del');
});

//Orders
Route::group(['as'=>'orders', 'middleware' => ['auth']], function (){
    Route::get('/orders/', 'OrderController@index');
    Route::get('/orders/create', 'OrderController@create');
    Route::post('/orders/store', 'OrderController@store');
    Route::get('/orders/{id}/edit', 'OrderController@edit');
    Route::patch('/orders/{id}', 'OrderController@update');
    Route::patch('/orders/{id}/lock', 'OrderController@lock');
    Route::delete('/orders/{id}', 'OrderController@delete');
    Route::get('/order_works/create', 'OrderWorkController@create');
    Route::post('/order_works/store', 'OrderWorkController@store');
    Route::get('/order_works/{id}/edit', 'OrderWorkController@edit');
    Route::patch('/order_works/{id}', 'OrderWorkController@update');
    Route::delete('/order_works/{id}', 'OrderWorkController@delete');
    Route::get('/materials/{id}/edit', 'MaterialController@edit');
    Route::post('materials_add', 'MaterialController@materialAdd');
    Route::patch('materials_update/{id}', 'MaterialController@materialUpdate');
    Route::delete('/materials/{id}', 'MaterialController@delete');
});

//Interventi
Route::group(['as'=>'interventi','middleware' => ['auth']],function (){
    Route::get('/nuovo-intervento','InterventiController@index');
    Route::get('/interventi-map','InterventiMapController@index');
    Route::post('/get-map-interventions','InterventiMapController@getMapInterventions');
    //Route::post('/intervento-macchinari-by-client-id','InterventiController@MacchinariByClientId');
    Route::get('/interventi-by-date','InterventiController@getOperationsByDate');
    Route::get('/interventi-get-tecnici','InterventiController@getTechnicians');
    Route::post('/interventi-save','InterventiController@saveInterventi');
    Route::post('/client-search','InterventiController@clientSearch');
    Route::post('/client-search-id','InterventiController@clientSearchId');
});

Route::group(['as'=>'map','middleware' => ['auth']],function (){
    Route::get('/interventi-map','InterventiMapController@index');
    Route::post('/get-map-interventions','InterventiMapController@getMapInterventions');
});

//Gestione Interventi Calendario
Route::group(['as'=>'monitoring','middleware' => ['auth']],function (){
    Route::get('/monitoring','CalendarioController@index');
    Route::post('/monitoring/getIntervenitByDateStartToEnd','MonitoringController@getIntervenitByDateStartToEnd');
    Route::post('/monitoring/getOperationsByClient','MonitoringController@getOperationsByClient');
    Route::post('/monitoring/getEventsByViewModeAndDate','MonitoringController@getEventsByViewModeAndDate');
});

Route::group(['as'=>'planning','middleware' => ['auth']],function (){
    Route::get('/planning','PlaningController@index');
    Route::get('/planning/operation','PlaningController@getOperations');
    Route::get('/planning/operation/calendar','PlaningController@getCalendarData');
    Route::get('/planning/operation/hours_calendar','PlaningController@getHoursCalendarData');
    Route::post('/planning/operation/create','PlaningController@create');
    Route::post('/planning/operation/{id}','PlaningController@update');
    Route::post('/planning/operation/{id}/replan','PlaningController@replan');
    Route::get('/getDashboardData', 'HomeController@getDashboardData');
});

Route::group(['as'=>'operation','middleware' => ['auth'], 'namespace' => 'Operation', 'prefix' => 'operation'],function (){
    Route::delete('{id}','OperationController@destroy');
});

Route::group(['as'=>'/lavori_in_corso','middleware' => ['auth']],function (){
    Route::get('/lavori_in_corso','ReportController@worksInProgress');
    Route::post('/getIntervenitInCorso','ReportController@worksInProgressAjax');
});

Route::group(['as'=>'/updatehours','middleware' => ['auth']],function (){
    Route::post('/updatehours','ReportController@updateHours');
});

Route::group(['as'=>'/lavori_da_fatturare','middleware' => ['auth']],function (){
    Route::get('/lavori_da_fatturare','ReportController@worksToInvoice');
    Route::post('/getReportsToInvoice','ReportController@worksToInvoiceAjax');
    Route::get('/lavori_da_fatturare_add/{id}/{mode}', 'ReportController@workToInvoiceEdit');
    Route::post('/saveReport', 'ReportController@saveReport');
    Route::get('/downloadReport/{id}', 'ReportController@downloadPDF');
    Route::get('/downloadExcel/{id}', 'ReportController@downloadEXC');

});

Route::group(['as'=>'/lavori_mensili','middleware' => ['auth']],function (){
    Route::get('/lavori_mensili','ReportController@worksPerMonth');
    Route::post('/getReportsPerMonth','ReportController@worksPerMonthAjax');
    Route::get('/lavori_mensili_add/{id}/{mode}', 'ReportController@workPerMonthEdit');
});

Route::group(['as'=>'/lavori_fatturati','middleware' => ['auth']],function (){
    Route::get('/lavori_fatturati','ReportController@worksInvoiced');
    Route::post('/worksInvoicedAjax','ReportController@worksInvoicedAjax');
    Route::get('/lavori_fatturati_add/{id}/{mode}', 'ReportController@workInvoicedEdit');
});

Route::group(['as'=>'/daily_summary','middleware' => ['auth']],function (){
    Route::get('/daily_summary','SummaryController@dailySummary');
    Route::get('/getSummaryByDate','SummaryController@getSummaryByDate');
    Route::get('/searchTehnician', 'SummaryController@searchTehnician');
    Route::post('/deleteInternalWork', 'TehnicianController@deleteInternalWork');
});

Route::group(['as'=>'/monthly_summary','middleware' => ['auth']],function (){
    Route::get('/monthly_summary','SummaryController@monthlySummary');
    Route::get('/getSummaryByMonth','SummaryController@getSummaryByMonth');
});

Route::get('new_report', 'ReportController@newReport');
Route::post('saveReportManually', 'ReportController@saveReportManually');

Route::group(['as'=>'/sendnotification','middleware' => ['auth']],function (){
    Route::any('/sendnotification', 'Controller@sendRequest');
});

//Reports to check
Route::group(['as'=>'reports_to_check', 'middleware' => ['auth'], 'namespace' => 'Report'], function (){
    Route::get('/reports_to_check', 'ToCheckController@index');
    Route::get('/reports/search/client', 'ToCheckController@searchClient');
    Route::get('/reports_to_check/{id}', 'ToCheckController@show');
});

//Reports list
Route::group(['as'=>'reports_list', 'middleware' => ['auth'], 'namespace' => 'Report'], function (){
    Route::get('/reports_list/ajax', 'ListController@ajax');
    Route::get('/reports_list', 'ListController@index');
    Route::get('/reports_list/{id}', 'ListController@show');
    Route::put('/reports_list/{id}/read', 'ListController@read');
});
Route::group(['as'=>'report', 'middleware' => ['auth'], 'namespace' => 'Report'], function (){
    Route::delete('/report/{id}', 'ReportController@delete');
});

//Summary Hours
Route::group(['as'=>'reports_to_check', 'middleware' => ['auth'], 'namespace' => 'Report'], function (){
    Route::get('/riepilogo_ore', 'SummaryHoursController@index');
    Route::post('/riepilogo_ore/data', 'SummaryHoursController@getData');
    Route::get('/riepilogo_ore/{id}', 'SummaryHoursController@show');
    Route::get('/riepilogo_ore/search/client', 'SummaryHoursController@searchClient');
});


Route::group(['as'=>'download_pdf'],function(){
    Route::get('/download_pdf/{id}','ReportController@download_pdf');
    Route::get('/pdf/{id}','ReportController@show_pdf');
});

Route::group(['as'=>'/daily_works','middleware' => ['auth']],function (){
    Route::get('/daily_works','TehnicianController@dailyWork');
    Route::get('/getWorksByDate/{date?}','TehnicianController@getWorksByDate');
    Route::post('/setInternalWork', 'TehnicianController@setInternalWork');
    Route::get('/getNotFinishedOperations', 'TehnicianController@sendNotFinishedOperations');

});

Route::group(['as' => 'client', 'middleware' => ['auth'], 'namespace' => 'Client', 'prefix' => 'client'],function () {
    Route::get('/{id}/locations', 'ClientLocationController@list');
});
Route::group(['as' => 'location', 'middleware' => ['auth'], 'namespace' => 'Location', 'prefix' => 'location'],function () {
    Route::get('/{id}/machineries', 'LocationMachineryController@list');
});

Route::group(['as'=>'planning','middleware' => ['auth']],function (){
    Route::get('/planning','PlaningController@index');
});

Route::get('/error_autorization',function (){
    return  view('error_autorization');
});

Route::group(['as'=>'wrong_vat_number','middleware' => ['auth']],function (){
    Route::get('/wrong_vat_number','VatNumberController@index');
    Route::get('/wrong_vat_number/ajax','VatNumberController@ajax');
    Route::any('/wrong_vat_number_add/{id}/{see?}', 'VatNumberController@add');
    Route::any('/wrong_vat_number_block', 'VatNumberController@block');
    Route::any('/wrong_vat_number_del', 'VatNumberController@delete');
 });

Route::group(['as'=>'import_excel','middleware' => ['auth']],function () {
    Route::get('/import_excel', 'Imports\ClientiImportController@index');
    Route::post('/import_excel/import', 'Imports\ClientiImportController@import');
});

//Query lists
Route::group(['as'=>'query', 'middleware' => ['auth'], 'namespace' => 'Query'], function (){
    Route::get('/query/ajax', 'ListController@ajax');
    Route::get('/query', 'ListController@index');

});
//Download Query
Route::group(['as'=>'download_pdf'],function(){
    Route::get('/download_pdf/{id}','ReportController@download_pdf');
    Route::get('/pdf/{id}','ReportController@show_pdf');
});



Route::get('/test1', 'Api\Operation\Report\PhotoControllerTest@main');

Route::fallback(function(){
    if (!empty(auth()->user())){
        return  view('error_autorization');
    }
    return redirect('/');
});
