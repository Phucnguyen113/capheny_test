<?php

use App\Http\Controllers\categoryController;
use App\Http\Controllers\districtController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\productController;
use App\Http\Controllers\uiSettingController;
use App\Http\Controllers\userController;
use App\Http\Controllers\wardController;
use App\Http\Controllers\permissionController;
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