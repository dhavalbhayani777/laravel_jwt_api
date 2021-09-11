<?php

Route::group([

    'prefix' => 'auth'

], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
});

/** token Option */
Route::group([],function () {
    // Route::get('company/{id}', 'CompanyController@getuserwisecompnay');
});

 /** Token Required */
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('user', 'UserController@getAuthenticatedUser');
    Route::get('closed', 'DataController@closed');
    Route::post('getAuthenticatedUser', 'AuthController@getAuthenticatedUser');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('logout', 'AuthController@logout');
    Route::post('updateUser/{id}', 'AuthController@updateUser');
    Route::delete('deleteUser/{id}', 'AuthController@deleteUser');
    Route::post('resetPassword/{id}', 'AuthController@resetPassword');

    Route::get('GetAllUserDetails/{id}', 'AuthController@GetAllUserDetails');

    /******company api ********/

    Route::post('addcompany', 'CompanyController@AddCompany');
    Route::get('company/{id}', 'CompanyController@getuserwisecompnay');
    Route::post('updateCompany/{id}', 'CompanyController@updateCompany');
    Route::delete('DeleteCompany/{id}', 'CompanyController@DeleteCompany');

    /******Account api ********/

    Route::post('AddAccount', 'AccountController@Addaccount');
    Route::post('UpdateAccount/{id}', 'AccountController@updateAccount');
    Route::get('UserAccountDetails/{id}', 'AccountController@GetUserWiseAccountDetails');
    Route::delete('DeleteAccount/{id}', 'AccountController@DeleteAccount');

    /**** shipping api ****/
    Route::post('AddShippingAddress', 'ShippingController@AddShippingAddress');
    Route::post('UpdateShipingDetails/{id}', 'ShippingController@UpdateShipingDetails');
    Route::get('UserShippingDetails/{id}', 'ShippingController@GetUserWiseShippingDetails');
    Route::delete('DeleteShippingAddressDetails/{id}', 'ShippingController@DeleteShippingDetails');

});

// Route::post('register', 'AuthController@register');