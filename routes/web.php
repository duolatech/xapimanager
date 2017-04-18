<?php

/**
 * xAPI Manager 路由信息
 * @author gang
 */

//首页
Route::group(['middleware'=>'isAuth'], function(){
    Route::get('/', ['as'=>'home', 'uses'=>'IndexController@index']);
    Route::get('Index/index', ['as'=>'Index.index', 'uses'=>'IndexController@index']);
    Route::get('Index/area', ['as'=>'Index.area', 'uses'=>'IndexController@area']);
    Route::get('Cache/index', ['as'=>'cache.index', 'uses'=>'CacheController@index']);
});
//登录相关
Route::group(['namespace'=>'Auth'], function(){
    Route::any('Login/index', ['as'=>'login.index', 'uses'=>'LoginController@index']);
    Route::get('Login/logout', 'LoginController@logout');
});
//验证码
Route::get('captcha/mews', 'CaptchaController@mews');
//测试页面 TODO
Route::get('Test/index', 'TestController@index');
//接口信息
Route::group(['prefix'=>'/Api', 'middleware'=>'isAuth'], function(){
    Route::get('list', ['as'=>'Api.list', 'uses'=>'ApiController@getApiList']);
    Route::get('ajaxList', ['as'=>'Api.ajaxList', 'uses'=>'ApiController@ajaxApiList']);
    Route::get('detail', ['uses'=>'ApiController@getApiDetail']);
    Route::get('search', ['uses'=>'ApiController@getSearch']);
    Route::get('info', ['as'=>'Api.info', 'uses'=>'ApiController@infoApi']);
    Route::any('audit', ['as'=>'Api.audit', 'uses'=>'ApiController@audit']);
    Route::post('store', ['as'=>'Api.store', 'uses'=>'ApiController@apiStore']);
    Route::post('operate', ['as'=>'Api.operate', 'uses'=>'ApiController@operate']);
    Route::post('discard', ['as'=>'Api.discard', 'uses'=>'ApiController@discard']);
    Route::get('debug', ['uses'=>'DebugController@index']);
});
//接口调试
Route::any('test', ['as'=>'Api.test', 'uses'=>'DebugController@test']);
//接口分类信息
Route::group(['prefix'=>'/Category', 'middleware'=>'isAuth'], function(){
    Route::get('index', ['as'=>'Category.index', 'uses'=>'CategoryController@getCategory']);
    Route::get('list', ['uses'=>'CategoryController@getCategoryList']);
    Route::get('sub', ['as'=>'Category.sub', 'uses'=>'CategoryController@subCategory']);
    Route::get('info', ['uses'=>'CategoryController@infoCategory']);
    Route::get('infoSub', ['as'=>'Category.infoSub', 'uses'=>'CategoryController@infoSubCategory']);
    Route::post('store', ['as'=>'Category.store', 'uses'=>'CategoryController@categoryStore']);
    Route::get('v1/classify/{cid}', ['uses'=>'CategoryController@classify']);
});
//用户和组
Route::group(['middleware'=>'isAuth'], function(){
    Route::get('User/index', ['as'=>'user.index', 'uses'=>'UserController@index']);
    Route::get('ajaxUser', ['as'=>'ajaxUser', 'uses'=>'UserController@ajaxUser']);
    Route::get('User/info', ['uses'=>'UserController@addUser']);
    Route::post('User/store', ['as'=>'user.store', 'uses'=>'UserController@userStore']);
    Route::post('User/check', ['as'=>'user.check', 'uses'=>'UserController@checkUser']);
    Route::get('Group/index', ['as'=>'group.index', 'uses'=>'GroupController@index']);
    Route::get('Group/info', ['as'=>'group.add', 'uses'=>'GroupController@addGroup']);
    Route::post('Group/operate', ['as'=>'group.operate', 'uses'=>'GroupController@operate']);
    Route::post('Group/store', ['as'=>'group.store', 'uses'=>'GroupController@groupStore']);
});
//系统设置
Route::group(['prefix'=>'/Sys', 'middleware'=>'isAuth'], function(){
    //站点设置
    Route::get('site', ['uses'=>'SysController@site']);
    Route::post('siteStore', ['as'=>'site.store', 'uses'=>'SysController@siteStore']);
    Route::get('env', ['uses'=>'SysController@sysenv']);
    Route::post('env/store', ['as'=>'env.store', 'uses'=>'SysController@envStore']);
    Route::get('menu', ['as'=>'menu', 'uses'=>'SysController@menu']);
    Route::get('Menu/info', ['as'=>'menu.add', 'uses'=>'SysController@addMenu']);
    Route::post('Menu/store', ['as'=>'menu.store', 'uses'=>'SysController@menuStore']);
    Route::post('Menu/del', ['as'=>'menu.del', 'uses'=>'SysController@delMenu']);
});
//统计信息
Route::group(['prefix'=>'/Statistics'], function(){
    Route::get('v1/area', ['as'=>'stat.area', 'uses'=>'StatisticsController@area']);
});
//上传图片
Route::group(['middleware'=>'isAuth'], function(){
    Route::post('upload', ['as'=>'upload', 'uses'=>'UploadController@upload']);
});
//个人资料
Route::group(['prefix'=>'/Personal','middleware'=>'isAuth'], function(){
    Route::get('profile', ['as'=>'profile', 'uses'=>'PersonalController@profile']);
});
//PDF导出
Route::group(['prefix'=>'/Export','middleware'=>'isAuth'], function(){
    Route::get('v1/classify/{cid}', ['as'=>'Export.classify', 'uses'=>'ExportController@classify']);
});