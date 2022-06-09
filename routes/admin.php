<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 后台公共路由部分
|
*/
Route::any('test', function () {
    return 'This is a request from any HTTP verb';
});
Route::group(['namespace' => 'Admin'], function () {
    //登录、注销
    Route::get('login', 'LoginController@showLoginForm')->name('admin.loginForm');
    Route::post('login', 'LoginController@login')->name('admin.login');
    Route::get('logout', 'LoginController@logout')->name('admin.logout');
    Route::post('test', 'IndexController@test')->name('admin.test');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 后台需要授权的路由 admins
|
*/
Route::group(['namespace' => 'Admin', 'middleware' => 'auth'], function () {
    Route::post('upload', 'UploadController@store')->name('admin.upload');
    Route::get('file', 'UploadController@index')->name('admin.file');
    Route::post('del_file', 'UploadController@DelFile')->name('admin.del_file');
    //后台布局
    Route::get('/', 'IndexController@layout')->name('admin.layout');
    //后台首页
    Route::get('/index', 'IndexController@index')->name('admin.index');
    Route::get('/agent_index', 'IndexController@agent_index')->name('admin.agent_index');
    //主页代理图表
    Route::get('agent_line_chart', 'IndexController@agent_line_chart')->name('admin.agent_line_chart');
    Route::get('agent_pie_chart', 'IndexController@agent_pie_chart')->name('admin.agent_pie_chart');
    //当前管理员设置
    Route::get('set/index', 'SetController@index')->name('admin.set.index');
    Route::post('set/setinfo', 'SetController@setinfo')->name('admin.set.setinfo');
    Route::get('set/password', 'SetController@password')->name('admin.set.password');
    Route::post('set/setpassword', 'SetController@setpassword')->name('admin.set.setpassword');
});

//系统管理
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'permission:system.manage']], function () {
    //数据表格接口
    Route::get('data', 'IndexController@data')->name('admin.data')->middleware('permission:system.role|system.user|system.permission');
    Route::get('line_chart', 'IndexController@line_chart')->name('admin.line_chart');
    Route::get('pie_chart', 'IndexController@pie_chart')->name('admin.pie_chart');
    //管理员管理
    Route::group(['middleware' => ['permission:system.user']], function () {
        Route::get('user', 'AdminUserController@index')->name('admin.user');
        //添加
        Route::get('user/create', 'AdminUserController@create')->name('admin.user.create')->middleware('permission:system.user.create');
        Route::post('user/store', 'AdminUserController@store')->name('admin.user.store')->middleware('permission:system.user.create');
        //编辑
        Route::get('user/{id}/edit', 'AdminUserController@edit')->name('admin.user.edit')->middleware('permission:system.user.edit');
        Route::put('user/{id}/update', 'AdminUserController@update')->name('admin.user.update')->middleware('permission:system.user.edit');
        //删除
        Route::delete('user/destroy', 'AdminUserController@destroy')->name('admin.user.destroy')->middleware('permission:system.user.destroy');
        //分配角色
        Route::get('user/{id}/role', 'AdminUserController@role')->name('admin.user.role')->middleware('permission:system.user.role');
        Route::put('user/{id}/assignRole', 'AdminUserController@assignRole')->name('admin.user.assignRole')->middleware('permission:system.user.role');
        //分配权限
        Route::get('user/{id}/permission', 'AdminUserController@permission')->name('admin.user.permission')->middleware('permission:system.user.permission');
        Route::put('user/{id}/assignPermission', 'AdminUserController@assignPermission')->name('admin.user.assignPermission')->middleware('permission:system.user.permission');
    });

    //代理管理
    Route::group(['middleware' => ['permission:system.agentuser']], function () {
        Route::get('agentuser', 'AgentUserController@index')->name('admin.agentuser');
        Route::get('agentuser/data', 'AgentUserController@data')->name('admin.agentuser.data');
        //添加
        Route::get('agentuser/create', 'AgentUserController@create')->name('admin.agentuser.create')->middleware('permission:system.agentuser.create');
        Route::post('agentuser/store', 'AgentUserController@store')->name('admin.agentuser.store')->middleware('permission:system.agentuser.create');
        //编辑
        Route::get('agentuser/{id}/edit', 'AgentUserController@edit')->name('admin.agentuser.edit')->middleware('permission:system.agentuser.edit');
        Route::put('agentuser/{id}/update', 'AgentUserController@update')->name('admin.agentuser.update')->middleware('permission:system.agentuser.edit');
        //删除
        Route::delete('agentuser/destroy', 'AgentUserController@destroy')->name('admin.agentuser.destroy')->middleware('permission:system.agentuser.destroy');
    });
    //角色管理
    Route::group(['middleware' => 'permission:system.role'], function () {
        Route::get('role', 'RoleController@index')->name('admin.role');
        //添加
        Route::get('role/create', 'RoleController@create')->name('admin.role.create')->middleware('permission:system.role.create');
        Route::post('role/store', 'RoleController@store')->name('admin.role.store')->middleware('permission:system.role.create');
        //编辑
        Route::get('role/{id}/edit', 'RoleController@edit')->name('admin.role.edit')->middleware('permission:system.role.edit');
        Route::put('role/{id}/update', 'RoleController@update')->name('admin.role.update')->middleware('permission:system.role.edit');
        //删除
        Route::delete('role/destroy', 'RoleController@destroy')->name('admin.role.destroy')->middleware('permission:system.role.destroy');
        //分配权限
        Route::get('role/{id}/permission', 'RoleController@permission')->name('admin.role.permission')->middleware('permission:system.role.permission');
        Route::put('role/{id}/assignPermission', 'RoleController@assignPermission')->name('admin.role.assignPermission')->middleware('permission:system.role.permission');
    });
    //权限管理
    Route::group(['middleware' => 'permission:system.permission'], function () {
        Route::get('permission', 'PermissionController@index')->name('admin.permission');
        //添加
        Route::get('permission/create', 'PermissionController@create')->name('admin.permission.create')->middleware('permission:system.permission.create');
        Route::post('permission/store', 'PermissionController@store')->name('admin.permission.store')->middleware('permission:system.permission.create');
        //编辑
        Route::get('permission/{id}/edit', 'PermissionController@edit')->name('admin.permission.edit')->middleware('permission:system.permission.edit');
        Route::put('permission/{id}/update', 'PermissionController@update')->name('admin.permission.update')->middleware('permission:system.permission.edit');
        //删除
        Route::delete('permission/destroy', 'PermissionController@destroy')->name('admin.permission.destroy')->middleware('permission:system.permission.destroy');
    });
    //操作日志
    Route::group(['middleware' => ['permission:system.operation']], function () {
        Route::get('operation', 'OperationController@index')->name('admin.operation');
        Route::get('operation/data', 'OperationController@data')->name('admin.operation.data');
        Route::get('operation/{id}/show', 'OperationController@show')->name('admin.operation.show');
        //删除
        Route::delete('operation/destroy', 'OperationController@destroy')->name('admin.operation.destroy')->middleware('permission:system.operation.destroy');
    });
});

//设置管理
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'permission:config.manage']], function () {
    //基础设置
    Route::group(['middleware' => 'permission:config.site'], function () {
        Route::get('site', 'SiteController@index')->name('admin.site');
        Route::get('site/optimize', 'SiteController@optimize')->name('admin.site.optimize')->middleware('permission:config.site.optimize');
        Route::get('site/datecache', 'SiteController@datecache')->name('admin.site.datecache')->middleware('permission:config.site.datecache');
        //清除缓存
        Route::put('site/clearcache', 'SiteController@clearcache')->name('admin.site.clearcache')->middleware('permission:config.site.clearcache');
        //更新
        Route::put('site', 'SiteController@update')->name('admin.site.update')->middleware('permission:config.site.update');
    });

    //支付渠道配置
    Route::group(['middleware' => ['permission:config.paychannel']], function () {
        Route::get('paychannel', 'PaychannelController@index')->name('admin.paychannel');
        Route::get('paychannel/data', 'PaychannelController@data')->name('admin.paychannel.data');
        //添加
        Route::get('paychannel/create', 'PaychannelController@create')->name('admin.paychannel.create')->middleware('permission:config.paychannel.create');
        Route::post('paychannel/store', 'PaychannelController@store')->name('admin.paychannel.store')->middleware('permission:config.paychannel.create');
        //编辑
        Route::get('paychannel/{id}/edit', 'PaychannelController@edit')->name('admin.paychannel.edit')->middleware('permission:config.paychannel.edit');
        Route::put('paychannel/{id}/update', 'PaychannelController@update')->name('admin.paychannel.update')->middleware('permission:config.paychannel.edit');
        //删除
        Route::delete('paychannel/destroy', 'PaychannelController@destroy')->name('admin.paychannel.destroy')->middleware('permission:config.paychannel.destroy');
    });

    //登陆日志
    Route::group(['middleware' => ['permission:config.paychannel']], function () {
        Route::get('paychannel', 'PaychannelController@index')->name('admin.paychannel');
        Route::get('paychannel/data', 'PaychannelController@data')->name('admin.paychannel.data');
        //添加
        Route::get('paychannel/create', 'PaychannelController@create')->name('admin.paychannel.create')->middleware('permission:config.paychannel.create');
        Route::post('paychannel/store', 'PaychannelController@store')->name('admin.paychannel.store')->middleware('permission:config.paychannel.create');
        //编辑
        Route::get('paychannel/{id}/edit', 'PaychannelController@edit')->name('admin.paychannel.edit')->middleware('permission:config.paychannel.edit');
        Route::put('paychannel/{id}/update', 'PaychannelController@update')->name('admin.paychannel.update')->middleware('permission:config.paychannel.edit');
        //删除
        Route::delete('paychannel/destroy', 'PaychannelController@destroy')->name('admin.paychannel.destroy')->middleware('permission:config.paychannel.destroy');
    });

});


//会员相关
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'permission:member.manage']], function () {
    //数据表格接口
    //Route::get('data','IndexController@data')->name('admin.data')->middleware('permission:system.role|system.user|system.permission|member.user');
    //Route::get('line_chart','IndexController@line_chart')->name('admin.line_chart');
    //会员管理
    Route::group(['middleware' => ['permission:member.user']], function () {
        Route::get('member', 'MemberController@index')->name('admin.member');
        Route::get('member/data', 'MemberController@data')->name('admin.member.data');
        Route::get('member/{id}/{username}/bankcard', 'MemberController@bankcard')->name('admin.member.bankcard');
        Route::get('member/bankcard_data', 'MemberController@bankcard_data')->name('admin.member.bankcard_data');
        //添加
        Route::get('member/create', 'MemberController@create')->name('admin.member.create')->middleware('permission:member.user.create');
        Route::post('member/store', 'MemberController@store')->name('admin.member.store')->middleware('permission:member.user.create');
        //编辑
        Route::get('member/{id}/edit', 'MemberController@edit')->name('admin.member.edit')->middleware('permission:member.user.edit');
        Route::put('member/{id}/update', 'MemberController@update')->name('admin.member.update')->middleware('permission:member.user.edit');
        //删除.禁用
        Route::delete('member/destroy', 'MemberController@destroy')->name('admin.member.destroy')->middleware('permission:member.user.destroy');
        Route::put('member/isuse', 'MemberController@isuse')->name('admin.member.isuse')->middleware('permission:member.user.isuse');

        //添加用户银行卡
         Route::get('member/bankcardcreate', 'MemberController@bankcardCreate')->name('admin.member.bankcardcreate')->middleware('permission:member.user.create');
         Route::post('member/bankcardstore', 'MemberController@bankcardStore')->name('admin.member.bankcardstore')->middleware('permission:member.user.create');
        //编辑用户银行卡
         Route::get('member/{id}/bankcardedit', 'MemberController@bankcardEdit')->name('admin.member.bankcardedit')->middleware('permission:member.user.edit');
         Route::put('member/{id}/bankcardupdate', 'MemberController@bankcardUpdate')->name('admin.member.bankcardupdate')->middleware('permission:member.user.edit');
        //删除.用户银行卡
        Route::delete('member/bankcarddestroy', 'MemberController@bankcardDestroy')->name('admin.member.bankcarddestroy')->middleware('permission:member.user.isuse');
        //Route::put('member/bankcardisuse', 'MemberController@isuse')->name('admin.member.isuse')->middleware('permission:member.user.isuse');

    });
    //注单订单管理
    Route::group(['middleware' => ['permission:member.betorders']], function () {
        Route::get('betorders', 'BetordersController@index')->name('admin.betorders');
        Route::get('betorders/data', 'BetordersController@data')->name('admin.betorders.data');
        //编辑
        Route::put('betorders/upuser', 'BetordersController@upuser')->name('admin.betorders.upuser')->middleware('permission:member.betorders.upuser');
	Route::get('betorders/{id}/edit', 'BetordersController@edit')->name('admin.betorders.edit')->middleware('permission:member.betorders.upuser');
        Route::put('betorders/{id}/update', 'BetordersController@update')->name('admin.betorders.update')->middleware('permission:member.betorders.upuser');
    });


    //会员账变记录
    Route::group(['middleware' => ['permission:member.tradelogs']], function () {
        //会员账变记录列表
        Route::get('tradelogs', 'TradelogsController@index')->name('admin.tradelogs');
        Route::get('tradelogs/data', 'TradelogsController@data')->name('admin.tradelogs.data');
    });
});


//游戏相关
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'permission:content.manage']], function () {
    //游戏管理
    Route::group(['middleware' => ['permission:content.game']], function () {
        Route::get('game', 'GameController@index')->name('admin.game');
        Route::get('game/data', 'GameController@data')->name('admin.game.data');
        //添加
        Route::get('game/create', 'GameController@create')->name('admin.game.create')->middleware('permission:content.game.create');
        Route::post('game/store', 'GameController@store')->name('admin.game.store')->middleware('permission:content.game.create');
        //编辑
        Route::get('game/{id}/edit', 'GameController@edit')->name('admin.game.edit')->middleware('permission:content.game.edit');
        Route::put('game/{id}/update', 'GameController@update')->name('admin.game.update')->middleware('permission:content.game.edit');
        //删除
        Route::delete('game/destroy', 'GameController@destroy')->name('admin.game.destroy')->middleware('permission:content.game.destroy');
        Route::put('game/isuse', 'GameController@isuse')->name('admin.game.isuse')->middleware('permission:content.game.isuse');
      //玩法
       // Route::get('games_type', 'GameController@gamesType')->name('admin.games_type');
        Route::get('game/{id}/game_type', 'GameController@gameType')->name('admin.game.game_type')->middleware('permission:content.game.edit');
        Route::get('game/game_type_data', 'GameController@gameTypeData')->name('admin.game.game_type_data');
        //设置玩法投资限额，赔率
        Route::put('game/edit_type', 'GameController@editType')->name('admin.game.edittype');
        //设置游戏玩法开启/关闭
        Route::put('game/type_isuse', 'GameController@typeIsuse')->name('admin.game.type_isuse')->middleware('permission:content.game.isuse');
        //添加玩法
        Route::get('game/createtype', 'GameController@createType')->name('admin.game.createtype')->middleware('permission:content.game.createtype');
        Route::post('game/storetype', 'GameController@storeType')->name('admin.game.storetype')->middleware('permission:content.game.createtype');
        //编辑玩法
        /*Route::get('game/{id}/edittype', 'GameController@editType')->name('admin.game.edittype')->middleware('permission:content.game.edittype');
        Route::put('game/{id}/updatetype', 'GameController@updateType')->name('admin.game.updatetype')->middleware('permission:content.game.edittype');*/
    });

    //开奖管理
    Route::group(['middleware' => ['permission:content.lottery']], function () {
        Route::get('lottery', 'LotteryController@index')->name('admin.lottery');
        Route::post('lottery/smallclasslist', 'LotteryController@smallclasslist')->name('admin.lottery.smallclasslist');
        Route::get('lottery/data', 'LotteryController@data')->name('admin.lottery.data');
        //添加
        Route::get('lottery/create', 'LotteryController@create')->name('admin.lottery.create')->middleware('permission:content.lottery.create');
        Route::post('lottery/store', 'LotteryController@store')->name('admin.lottery.store')->middleware('permission:content.lottery.create');
        //编辑
        Route::get('lottery/{id}/edit', 'LotteryController@edit')->name('admin.lottery.edit')->middleware('permission:content.lottery.edit');
        Route::put('lottery/{id}/update', 'LotteryController@update')->name('admin.lottery.update')->middleware('permission:content.lottery.edit');
        //删除
        Route::delete('lottery/destroy', 'LotteryController@destroy')->name('admin.lottery.destroy')->middleware('permission:content.lottery.destroy');
    });


});

//视频相关
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'permission:videos.manage']], function () {
    //视频管理
    Route::group(['middleware' => ['permission:videos.video']], function () {
        Route::get('video', 'VideoController@index')->name('admin.video');
        Route::get('video/data', 'VideoController@data')->name('admin.video.data');
        Route::post('video/smallclasslist', 'VideoController@smallclasslist')->name('admin.video.smallclasslist');

        //添加
        Route::get('video/create', 'VideoController@create')->name('admin.video.create')->middleware('permission:videos.video.create');
        Route::post('video/store', 'VideoController@store')->name('admin.video.store')->middleware('permission:videos.video.create');
        //编辑
        Route::get('video/{id}/edit', 'VideoController@edit')->name('admin.video.edit')->middleware('permission:videos.video.edit');
        Route::put('video/{id}/update', 'VideoController@update')->name('admin.video.update')->middleware('permission:videos.video.edit');
        //删除
        Route::delete('video/destroy', 'VideoController@destroy')->name('admin.video.destroy')->middleware('permission:videos.video.destroy');
    });

    //标签管理
    Route::group(['middleware' => ['permission:videos.label']], function () {
        Route::get('label', 'LabelController@index')->name('admin.label');
        Route::get('label/data', 'LabelController@data')->name('admin.label.data');
        //添加
        Route::get('label/create', 'LabelController@create')->name('admin.label.create')->middleware('permission:videos.label.create');
        Route::post('label/store', 'LabelController@store')->name('admin.label.store')->middleware('permission:videos.label.create');
        //编辑
        Route::get('label/{id}/edit', 'LabelController@edit')->name('admin.label.edit')->middleware('permission:videos.label.edit');
        Route::put('label/{id}/update', 'LabelController@update')->name('admin.label.update')->middleware('permission:videos.label.edit');
        //删除
        Route::delete('label/destroy', 'LabelController@destroy')->name('admin.label.destroy')->middleware('permission:videos.label.destroy');
    });

    //视频大类管理
    Route::group(['middleware' => ['permission:videos.videobigclass']], function () {
        Route::get('videobigclass', 'VideobigclassController@index')->name('admin.videobigclass');
        Route::get('videobigclass/data', 'VideobigclassController@data')->name('admin.videobigclass.data');
        //添加
        Route::get('videobigclass/create', 'VideobigclassController@create')->name('admin.videobigclass.create')->middleware('permission:videos.videobigclass.create');
        Route::post('videobigclass/store', 'VideobigclassController@store')->name('admin.videobigclass.store')->middleware('permission:videos.videobigclass.create');
        //编辑
        Route::get('videobigclass/{id}/edit', 'VideobigclassController@edit')->name('admin.videobigclass.edit')->middleware('permission:videos.videobigclass.edit');
        Route::put('videobigclass/{id}/update', 'VideobigclassController@update')->name('admin.videobigclass.update')->middleware('permission:videos.videobigclass.edit');
        //删除
        Route::delete('videobigclass/destroy', 'VideobigclassController@destroy')->name('admin.videobigclass.destroy')->middleware('permission:videos.videobigclass.destroy');
    });

    //视频小类管理
    Route::group(['middleware' => ['permission:videos.videosmallclass']], function () {
        Route::get('videosmallclass', 'VideosmallclassController@index')->name('admin.videosmallclass');
        Route::get('videosmallclass/data', 'VideosmallclassController@data')->name('admin.videosmallclass.data');
        //添加
        Route::get('videosmallclass/create', 'VideosmallclassController@create')->name('admin.videosmallclass.create')->middleware('permission:videos.videosmallclass.create');
        Route::post('videosmallclass/store', 'VideosmallclassController@store')->name('admin.videosmallclass.store')->middleware('permission:videos.videosmallclass.create');
        //编辑
        Route::get('videosmallclass/{id}/edit', 'VideosmallclassController@edit')->name('admin.videosmallclass.edit')->middleware('permission:videos.videosmallclass.edit');
        Route::put('videosmallclass/{id}/update', 'VideosmallclassController@update')->name('admin.videosmallclass.update')->middleware('permission:videos.videosmallclass.edit');
        //删除
        Route::delete('videosmallclass/destroy', 'VideosmallclassController@destroy')->name('admin.videosmallclass.destroy')->middleware('permission:videos.videosmallclass.destroy');
    });

    //视频导航分类管理
    Route::group(['middleware' => ['permission:videos.navigationsmallclass']], function () {
        Route::get('navigationsmallclass', 'NavigationsmallclassController@index')->name('admin.navigationsmallclass');
        Route::get('navigationsmallclass/data', 'NavigationsmallclassController@data')->name('admin.navigationsmallclass.data');
        //添加
        Route::get('navigationsmallclass/create', 'NavigationsmallclassController@create')->name('admin.navigationsmallclass.create')->middleware('permission:videos.navigationsmallclass.create');
        Route::post('navigationsmallclass/store', 'NavigationsmallclassController@store')->name('admin.navigationsmallclass.store')->middleware('permission:videos.navigationsmallclass.create');
        //编辑
        Route::get('navigationsmallclass/{id}/edit', 'NavigationsmallclassController@edit')->name('admin.navigationsmallclass.edit')->middleware('permission:videos.navigationsmallclass.edit');
        Route::put('navigationsmallclass/{id}/update', 'NavigationsmallclassController@update')->name('admin.navigationsmallclass.update')->middleware('permission:videos.navigationsmallclass.edit');
        //删除
        Route::delete('navigationsmallclass/destroy', 'NavigationsmallclassController@destroy')->name('admin.navigationsmallclass.destroy')->middleware('permission:videos.navigationsmallclass.destroy');
    });


    //演员管理
    Route::group(['middleware' => ['permission:videos.actors']], function () {
        Route::get('actors', 'ActorsController@index')->name('admin.actors');
        Route::get('actors/data', 'ActorsController@data')->name('admin.actors.data');
        //添加
        Route::get('actors/create', 'ActorsController@create')->name('admin.actors.create')->middleware('permission:videos.actors.create');
        Route::post('actors/store', 'ActorsController@store')->name('admin.actors.store')->middleware('permission:videos.actors.create');
        //编辑
        Route::get('actors/{id}/edit', 'ActorsController@edit')->name('admin.actors.edit')->middleware('permission:videos.actors.edit');
        Route::put('actors/{id}/update', 'ActorsController@update')->name('admin.actors.update')->middleware('permission:videos.actors.edit');
        //删除
        Route::delete('actors/destroy', 'ActorsController@destroy')->name('admin.actors.destroy')->middleware('permission:videos.actors.destroy');
    });
});

//资金管理
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'permission:funds.manage']], function () {

    //提现管理
    Route::group(['middleware' => ['permission:funds.withdrawal']], function () {
        Route::get('withdrawal', 'WithdrawalController@index')->name('admin.withdrawal');
        Route::get('withdrawal/data', 'WithdrawalController@data')->name('admin.withdrawal.data');
        Route::put('withdrawal/agreepay', 'WithdrawalController@agreepay')->name('admin.withdrawal.agreepay')->middleware('permission:funds.withdrawal.agreepay');
        Route::put('withdrawal/refusepay', 'WithdrawalController@refusepay')->name('admin.withdrawal.refusepay')->middleware('permission:funds.withdrawal.refusepay');
        Route::get('withdrawal/{id}/edit', 'WithdrawalController@edit')->name('admin.withdrawal.edit')->middleware('permission:funds.withdrawal.refusepay');
        Route::put('withdrawal/{id}/update', 'WithdrawalController@update')->name('admin.withdrawal.update')->middleware('permission:funds.withdrawal.refusepay');
    });

    //手动充值管理
    Route::group(['middleware' => ['permission:funds.orders']], function () {
        Route::get('orders', 'OrdersController@index')->name('admin.orders');
        Route::get('orders/data', 'OrdersController@data')->name('admin.orders.data');
        //加钱扣钱
        Route::get('orders/create', 'OrdersController@create')->name('admin.orders.create')->middleware('permission:funds.orders.create');
        Route::post('orders/store', 'OrdersController@store')->name('admin.orders.store')->middleware('permission:funds.orders.create');
        Route::post('orders/getuser', 'OrdersController@getuser')->name('admin.orders.getuser');

    });

});



//统计报表
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'permission:reports.manage']], function () {
    //用户报表
    Route::group(['middleware' => ['permission:reports.usercount']], function () {
        Route::get('usercount', 'UsercountController@index')->name('admin.usercount');
        Route::get('usercount/data', 'UsercountController@data')->name('admin.usercount.data');
    });
    //平台报表
    Route::group(['middleware' => ['permission:reports.totalcount']], function () {
        Route::get('totalcount', 'TotalcountController@index')->name('admin.totalcount');
        Route::get('totalcount/data', 'TotalcountController@data')->name('admin.totalcount.data');
    });
    //代理报表
    Route::group(['middleware' => ['permission:reports.agentcount']], function () {
        Route::get('agentcount', 'AgentcountController@index')->name('admin.agentcount');
        Route::get('agentcount/data', 'AgentcountController@data')->name('admin.agentcount.data');
        Route::get('agentcount/{id}/view_down', 'AgentcountController@viewDown')->name('admin.agentcount.view_down');
        Route::get('agentcount/view_down_data', 'AgentcountController@viewDownData')->name('admin.agentcount.view_down_data');
    });
});

//日志管理
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'permission:logs.manage']], function () {

    Route::group(['middleware' => ['permission:logs.userlogs']], function () {
        Route::get('userlogs', 'UserlogsController@index')->name('admin.userlogs');
        Route::get('userlogs/data', 'UserlogsController@data')->name('admin.userlogs.data');
    });

    Route::group(['middleware' => ['permission:logs.adminlogs']], function () {
        Route::get('adminlogs', 'AdminlogsController@index')->name('admin.adminlogs');
        Route::get('adminlogs/data', 'AdminlogsController@data')->name('admin.adminlogs.data');
    });
});


//内容管理
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'permission:info.manage']], function () {
    //站内信息
    Route::group(['middleware' => ['permission:info.news']], function () {
        Route::get('news', 'NewsController@index')->name('admin.news');
        Route::get('news/data', 'NewsController@data')->name('admin.news.data');
        //添加
        Route::get('news/create', 'NewsController@create')->name('admin.news.create')->middleware('permission:info.news.create');
        Route::post('news/store', 'NewsController@store')->name('admin.news.store')->middleware('permission:info.news.create');
        //编辑
        Route::get('news/{id}/edit', 'NewsController@edit')->name('admin.news.edit')->middleware('permission:info.news.edit');
        Route::put('news/{id}/update', 'NewsController@update')->name('admin.news.update')->middleware('permission:info.news.edit');
        //删除
        Route::delete('news/destroy', 'NewsController@destroy')->name('admin.news.destroy')->middleware('permission:info.news.destroy');



    });

});




