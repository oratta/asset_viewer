<?php

Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/user', function () {
    return Auth::user();
})->name('user');
Route::get('/reflesh-token', function (Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();
    return response()->json();
});

//動作確認用
Route::get('/categories', 'CategoryController@index')->name('category.index');

//デバッグメニュー用
Route::post('/debug/add_asset', 'DebugController@addAsset')->name('debug.addAsset');

//本番用
Route::get('/portfolio', 'UserCategoryController@portfolio')->name('portfolio');
Route::get('/user_asset', 'UserAssetController@index')->name('userAsset');
Route::post('/categorize', 'UserAssetController@categorize')->name('categorize.save');
Route::post('/load', 'UserAssetController@load')->name('load');
Route::get('/goal')->name('goal.view');
Route::post('/goal')->name('goal.save');
