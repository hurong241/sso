<?php
//首页
Route::get('/','Index@index');
//登录
Route::get('/login', 'Login@index');
//登录检查
Route::post('/login_check','Login@checkLogin');
//token检查
Route::get('/token','Token@index');
//退出
Route::get('/loginout','Login@logout');
?>