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
//项目选择
Route::group(['prefix'=>'/Project','middleware'=>'isAuth'], function(){
    Route::get('create', ['as'=>'project.create', 'uses'=>'ProjectController@create']);
    Route::get('edit', ['as'=>'project.edit', 'uses'=>'ProjectController@edit']);
    Route::post('store', ['as'=>'project.store', 'uses'=>'ProjectController@store']);
    Route::post('toggle', ['as'=>'project.toggle', 'uses'=>'ProjectController@toggle']);
});
//登录相关
Route::group(['namespace'=>'Auth'], function(){
    Route::any('Login/index', ['as'=>'login.index', 'uses'=>'LoginController@index']);
    Route::get('Register/index', ['as'=>'Register.index', 'uses'=>'RegisterController@index']);
    Route::post('Register/store', ['as'=>'Register.store', 'uses'=>'RegisterController@store']);
    Route::any('Register/check', ['as'=>'Register.check', 'uses'=>'RegisterController@check']);
    Route::get('Login/logout', 'LoginController@logout');
});
//Api调试
Route::group(['prefix'=>'/Debug'], function(){
    Route::get('/', ['as'=>'Debug', 'uses'=>'DebugController@index']);
    Route::post('domain', ['as'=>'Debug.domain', 'uses'=>'DebugController@domain']);
    Route::post('isBind', ['as'=>'Debug.isBind', 'middleware'=>'isAuth', 'uses'=>'DebugController@isBind']);
    Route::post('store', ['as'=>'Debug.store', 'middleware'=>'isAuth', 'uses'=>'DebugController@store']);
    Route::post('del', ['as'=>'Debug.del', 'middleware'=>'isAuth', 'uses'=>'DebugController@del']);
    Route::post('test', ['as'=>'Debug.test', 'uses'=>'DebugController@test']);
});
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
});
//接口调试
Route::any('test', ['as'=>'Api.test', 'uses'=>'DebugController@test']);
//接口分类信息
Route::group(['prefix'=>'/Category', 'middleware'=>'isAuth'], function(){
    Route::get('index', ['as'=>'Category.index', 'uses'=>'CategoryController@index']);
    Route::get('list', ['uses'=>'CategoryController@getCategoryList']);
    Route::get('detail', ['uses'=>'CategoryController@getDetail']);
    Route::get('detailSub', ['uses'=>'CategoryController@getDetailSub']);
    Route::get('info', ['uses'=>'CategoryController@infoCategory']);
    Route::get('infoSub', ['as'=>'Category.infoSub', 'uses'=>'CategoryController@infoSubCategory']);
    Route::post('store', ['as'=>'Category.store', 'uses'=>'CategoryController@categoryStore']);
    Route::post('del', ['as'=>'Category.del', 'uses'=>'CategoryController@delClassify']);
    Route::get('v1/subClassify/{cid}', ['uses'=>'CategoryController@subClassify']);
});
//公司密钥
Route::group(['prefix'=>'/Company', 'middleware'=>'isAuth'], function(){
    Route::any('index', ['as'=>'secret.index', 'uses'=>'CompanyController@index']);
    Route::get('ajaxList', ['as'=>'secret.ajaxList', 'uses'=>'CompanyController@ajaxList']);
    Route::get('secret/info', ['as'=>'secret.info', 'uses'=>'CompanyController@secretInfo']);
    Route::post('secret/store', ['as'=>'secret.store', 'uses'=>'CompanyController@store']);
    Route::post('secret/operate', ['as'=>'secret.operate', 'uses'=>'CompanyController@operate']);
});
//用户和组
Route::group(['middleware'=>'isAuth'], function(){
    Route::get('User/index', ['as'=>'user.index', 'uses'=>'UserController@index']);
    Route::get('ajaxUser', ['as'=>'ajaxUser', 'uses'=>'UserController@ajaxUser']);
    Route::get('User/info', ['uses'=>'UserController@userInfo']);
    Route::get('User/detail', ['uses'=>'UserController@detail']);
    Route::post('User/store', ['as'=>'user.store', 'uses'=>'UserController@userStore']);
    Route::post('User/check', ['as'=>'user.check', 'uses'=>'UserController@checkUser']);
    Route::get('Group/index', ['as'=>'group.index', 'uses'=>'GroupController@index']);
    Route::get('ajaxGroup', ['as'=>'ajaxGroup', 'uses'=>'GroupController@ajaxGroup']);
    Route::get('Group/info', ['as'=>'group.info', 'uses'=>'GroupController@groupInfo']);
    Route::get('Group/featureAuth', ['as'=>'group.featureAuth', 'uses'=>'GroupController@featureAuth']);
    Route::get('Group/dataAuth', ['as'=>'group.dataAuth', 'uses'=>'GroupController@dataAuth']);
    Route::get('ajaxDataRange', ['as'=>'group.ajaxDataRange', 'uses'=>'GroupController@ajaxDataRange']);
    Route::post('Group/operate', ['as'=>'group.operate', 'uses'=>'GroupController@operate']);
    Route::post('Group/store', ['as'=>'group.store', 'uses'=>'GroupController@groupStore']);
    Route::post('Group/featureStore', ['as'=>'group.featureStore', 'uses'=>'GroupController@featureStore']);
    Route::post('Group/dataStore', ['as'=>'group.dataStore', 'uses'=>'GroupController@dataStore']);
});
//消息通知
Route::group(['prefix'=>'/Message', 'middleware'=>'isAuth'], function(){
    Route::get('index', ['as'=>'message.index', 'uses'=>'MessageController@index']);
    Route::get('info', ['as'=>'message.info', 'uses'=>'MessageController@MessageInfo']);
    Route::post('store', ['as'=>'message.store', 'uses'=>'MessageController@MessageStore']);
    Route::get('detail', ['as'=>'message.detail', 'uses'=>'MessageController@detail']);
    Route::post('del', ['as'=>'message.del', 'uses'=>'MessageController@del']);
});
//帮助中心
Route::group(['prefix'=>'/Help', 'middleware'=>'isAuth'], function(){
    Route::get('index', ['as'=>'help.index', 'uses'=>'HelpController@index']);
    Route::get('info', ['as'=>'help.info', 'uses'=>'HelpController@helpInfo']);
    Route::post('store', ['as'=>'help.store', 'uses'=>'HelpController@helpStore']);
    Route::post('del', ['as'=>'help.del', 'uses'=>'HelpController@del']);
    Route::get('ajaxHelp', ['as'=>'ajaxHelp', 'uses'=>'HelpController@ajaxHelp']);
});
//系统设置
Route::group(['prefix'=>'/Sys', 'middleware'=>'isAuth'], function(){
    //站点设置
    Route::get('site', ['uses'=>'SysController@site']);
    Route::get('project', ['uses'=>'SysController@project']);
    Route::post('siteStore', ['as'=>'site.store', 'uses'=>'SysController@siteStore']);
    Route::get('env', ['uses'=>'SysController@sysenv']);
    Route::post('env/store', ['as'=>'env.store', 'uses'=>'SysController@envStore']);
    Route::get('log', ['uses'=>'SysController@log']);
    Route::get('log/detail', ['uses'=>'SysController@detailLog']);
    Route::get('ajaxLog', ['as'=>'ajaxLog', 'uses'=>'SysController@ajaxLog']);
    Route::get('update', ['as'=>'sys.update', 'uses'=>'SysController@update']);
});
//统计信息
Route::group(['prefix'=>'/Statistics'], function(){
    Route::get('v1/area', ['as'=>'stat.area', 'uses'=>'StatisticsController@area']);
});
//上传图片
Route::group(['prefix'=>'/upload', 'middleware'=>'isAuth'], function(){
    Route::post('/', ['as'=>'upload', 'uses'=>'UploadController@upload']);
    Route::post('avatar', ['as'=>'upload.avatar', 'uses'=>'UploadController@avatar']);
});
//安装向导
Route::group(['prefix'=>'/Install'], function(){
    Route::get('/', ['as'=>'install.index', 'uses'=>'InstallController@index']);
    Route::post('info', ['as'=>'install.info', 'uses'=>'InstallController@info']);
    Route::post('update', ['as'=>'install.update', 'uses'=>'InstallController@update']);
});
//word导出
Route::group(['prefix'=>'/Export','middleware'=>'isAuth'], function(){
    Route::get('v1/subClassify/{cid}', ['as'=>'Export.subClassify', 'uses'=>'ExportController@subClassify']);
});
//mock测试
Route::group(['prefix'=>'/Mock'], function(){
    Route::get('{name}', ['as'=>'mock.index', 'uses'=>'MockController@index'])->where('name', '[a-zA-Z0-9\/\-\_]+');
});