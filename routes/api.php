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

//本番用
Route::get('/portfolio', 'UserCategoryController@portfolio')->name('portfolio');
Route::get('/categorize', 'UserAssetController@index')->name('userAsset');
Route::post('/categorize')->name('categorize.save');
Route::get('/goal')->name('goal.view');
Route::post('/goal')->name('goal.save');
