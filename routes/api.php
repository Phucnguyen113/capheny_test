<?php

use App\Http\Controllers\ApiCategoryController;
use App\Http\Controllers\ApiColorController;
use App\Http\Controllers\ApiOrderController;
use App\Http\Controllers\ApiProductController;
use App\Http\Controllers\ApiSizeController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\districtController;
use App\Http\Controllers\jwtAuthController;
use App\Http\Controllers\mailController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\productController;
use App\Http\Controllers\uiSettingController;
use App\Http\Controllers\userController;
use App\Http\Controllers\wardController;
use App\Http\Controllers\permissionController;
use App\Http\Controllers\select2Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('district/get/{id}',[districtController::class,'get_district']);
Route::post('ward/get/{id}',[wardController::class,'get_ward']);
//get size & color product
Route::post('product/get_size_color/{id}',[productController::class,'get_size_color_api']);
// get size & color & price product
Route::post('product/get_size_color_price/{id}',[productController::class,'get_price_size_color_api']);
//delete product_color
Route::post('product/color/delete/{id_product}/{id_color}',[productController::class,'delete_color_product']);
//delete product_size
Route::post('product/size/delete/{id_product}/{id_size}',[productController::class,'delete_size_product']);
//delete category product
Route::post('product/category/delete/{id_product}/{id_category}',[productController::class,'delete_category_product']);

//get infoUser
Route::post('infouser/{id}',[userController::class,'user']);
// delete product order detail
Route::post('delete/product/order/{order}/{product}/{size}/{color}',[orderController::class,'delete_product_order_detail']);
// uisetting 
Route::post('ui_setting',[uiSettingController::class,'set_ui']);
// get action in page add permission
Route::post('get_action/{table}',[permissionController::class,'get_action']);
//active api user
Route::post('user/active',[userController::class,'active_api']);
//active api cateogry
Route::post('category/active',[categoryController::class,'active_api']);
//active api product
Route::post('product/active',[productController::class,'active_api']);
//edit status order
Route::post('order/status',[orderController::class,'edit_status_api']);
//send mail order
Route::post('send/mail/{order_id}',[mailController::class,'sendmail']);

Route::group(['middleware'=>'p_cors'],function(){
    //category API
    Route::post('tree_cate',[ApiCategoryController::class,'list_category']);
    // product API
    Route::get('list_product',[ApiProductController::class,'list_product']);
    //category API
    Route::get('column_cate/{id}',[ApiCategoryController::class,'category_column']);
    // detail product API
    Route::get('detail/product',[ApiProductController::class,'detail']);
    //get product_new
    Route::get('product_new',[ApiProductController::class,'list_product_new_api']);
    // insert order API
    Route::post('order/create',[ApiOrderController::class,'add']);
    // list color
    Route::get('color/list',[ApiColorController::class,'list_color_api']);
    // list size
    Route::get('size/list',[ApiSizeController::class,'list_size_api']);
    //Check amount product detail
    Route::post('check_amount_attr_color',[ApiProductController::class,'check_amount_attr_color']);
});

// select2 remote data
Route::post('get_list_user',[select2Controller::class,'get_list_user']);
Route::post('get_list_product',[select2Controller::class,'get_list_product']);
Route::post('get_list_color',[select2Controller::class,'get_list_color']);

// dashboard
Route::post('dashboard/order',[dashboardController::class,'order']);
Route::post('dashboard/store_product',[dashboardController::class,'store_product']);

Route::post('auth/register', [jwtAuthController::class,'register']);
Route::post('auth/login', [jwtAuthController::class,'login']);
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('user-info', [jwtAuthController::class,'getUserInfo']);
});

