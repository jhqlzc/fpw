<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

//微信接口路由
Route::rule('weixin/callback', 'weixin/WeChatCallBack/index', 'GET|POST');

//发送验证码
Route::post('api/:version/sendsms/register', 'api/:version.Sms/registerSms');
//注册用户并获取token
Route::post('api/:version/register', 'api/:version.Register/register');
//微信授权绑定手机号
Route::post('api/:version/wxregister', 'api/:version.Register/wxRegister');
//登陆获取token
Route::post('api/:version/token/login', 'api/:version.Token/getLoginToken');
//微信获取token
Route::get('api/:version/token/weixin', 'api/:version.Token/getWeiXinToken');

//上传商品图片
Route::post('api/:version/goods/upload_img', 'api/:version.Goods/uploadImg');
//删除商品图片
Route::get('api/:version/del_tmp_pic/:name', 'api/:version.Goods/deleteTmpPic');
//自动删除垃圾商品图片
Route::get('api/:version/auto_del_tmp_pic', 'api/:version.Goods/autoDelTmpPic');
//上传商品属性
Route::post('api/:version/goods/upload_info', 'api/:version.Goods/addGoods');
//查询用户商品
Route::get('api/:version/goods/by_user', 'api/:version.Goods/getAllGoodsByUser');
//查询所有商品
Route::get('api/:version/goods/all', 'api/:version.Goods/getAllGoods');
//查询某一商品详细信息
Route::get('api/:version/goods/:id', 'api/:version.Goods/getOneDetail', [], ['id'=>'\d+']);
//按商品名称搜索商品
Route::get('api/:version/goods/by_name', 'api/:version.Goods/searchGoodsByName');
//编辑商品
Route::get('api/:version/goods/edit/:id', 'api/:version.Goods/editGoods');
//删除商品图片
Route::get('api/:version/goods/del_pic/:id/:pic_type', 'api/:version.Goods/delPic');
//更新商品图片
Route::post('api/:version/goods/update_img/:id', 'api/:version.Goods/updateImg');
//更新商品属性
Route::post('api/:version/goods/update_info/:id', 'api/:version.Goods/updateGoodsInfo');
//删除整条商品
Route::get('api/:version/goods/del_goods/:id', 'api/:version.Goods/delGoods');

//设置开拍提醒
Route::get('api/:version/remind', 'api/:version.goodsRemind/setRemind');

//添加用户地址
Route::post('api/:version/address/add', 'api/:version.Address/createAddress');
//删除用户地址
Route::get('api/:version/address/delete/:id', 'api/:version.Address/deleteAddress');
//设置默认地址
Route::get('api/:version/address/default/:id', 'api/:version.Address/setDefault');
//查询所有地址
Route::get('api/:version/address/all', 'api/:version.Address/getAllAddressByUser');


//竞价
Route::get('api/:version/bid/:id', 'api/:version.Bid/bid');
//报名
Route::post('api/:version/enroll', 'api/:version.AuctionEnroll/enroll');

//支付保证金
Route::post('api/:version/wx_enroll_pay', 'api/:version.WxPay/getEnrollPreOrder');
//支付尾款
Route::post('api/:version/wx_final_pay', 'api/:version.WxPay/getFinalPreOrder');

//微信支付回调
Route::post('api/:version/wxpay/notify', 'api/:version.WxPay/receiveNotify');


//用户信息
Route::get('api/:version/user', 'api/:version.User/getUserInfo');





