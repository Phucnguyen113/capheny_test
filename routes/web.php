<?php

use App\Http\Controllers\ApiCategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\colorController;
use App\Http\Controllers\commentController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\mailController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\permissionController;
use App\Http\Controllers\productController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\sizeController;
use App\Http\Controllers\storeController;
use App\Http\Controllers\userController;
use App\Http\Controllers\jwtAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['prefix'=>'admin'],function(){
    Route::group(['middleware'=>'p_auth'],function(){
        Route::get('dashboard',[dashboardController::class,'index']);
        Route::resource('category',categoryController::class);
        Route::resource('product',productController::class);
        Route::resource('size',sizeController::class);
        Route::resource('color',colorController::class);
        Route::resource('product',productController::class);
        //detail product
        Route::get('product/{id}/detail',[productController::class,'detail']);
        Route::get('product/{id}/addcolor',[productController::class,'add_new_color_product_form']);
        Route::post('product/{id}/addcolor',[productController::class,'add_new_color_product']);
        Route::get('product/{id}/addsize',[productController::class,'add_new_size_product_form']);
        Route::post('product/{id}/addsize',[productController::class,'add_new_size_product']);
        //store
        Route::get('store',[storeController::class,'index']);
        Route::get('store/create',[storeController::class,'create']);
        Route::post('store/create',[storeController::class,'store']);
        Route::get('store/addproduct',[storeController::class,'form_add_product']);
        Route::post('store/addproduct',[storeController::class,'add_product']);
        Route::get('store/{id}/detail',[storeController::class,'detail']);
        Route::post('store/delete/addproduct/{id}',[storeController::class,'delete_product_from_store']);
        Route::get('store/editproduct/{id}',[storeController::class,'edit_product_from_store_form']);
        Route::post('store/editproduct/{id}',[storeController::class,'edit_product_from_store']);
        Route::get('store/{id}/edit',[storeController::class,'edit_store_form']);
        Route::post('store/{id}/edit',[storeController::class,'edit_store']);
        Route::post('store/delete/{id}',[storeController::class,'delete_store']);
        //user
        Route::get('user',[userController::class,'index']);
        Route::get('user/create',[userController::class,'add_form']);
        Route::post('user/create',[userController::class,'add']);
        Route::get('user/{id}/edit',[userController::class,'edit_form']);
        Route::post('user/{id}/edit',[userController::class,'edit']);
        Route::post('user/{id}/delete',[userController::class,'delete']);
        Route::get('user/addrole',[roleController::class,'add_role_for_user_form']);
        Route::post('user/addrole',[roleController::class,'add_role']);
        Route::get('user/{id}/editrole',[roleController::class,'edit_role_for_user_form'] );
        Route::post('user/{id}/editrole', [roleController::class,'edit_role']);
        Route::get('user/addpermission',[permissionController::class,'add_permission_for_user_form']);
        Route::post('user/addpermission',[permissionController::class,'add_permission']);
        Route::get('user/{id}/editpermission',[permissionController::class,'edit_permission_for_user_form']);
        Route::post('user/{id}/editpermission',[permissionController::class,'edit_permission']);
        // order 
        Route::get('order/create',[orderController::class,'add_form']);
        Route::post('order/create',[orderController::class,'add']);
        Route::get('order/{id}/edit',[orderController::class,'edit_form']);
        Route::post('order/{id}/edit',[orderController::class,'edit']);
        Route::get('order',[orderController::class,'index']);
        Route::put('order/{id}/delete',[orderController::class,'delete']);
        Route::get('order/{id}/detail',[orderController::class,'detail']);
        //comment
        Route::get('comment',[commentController::class,'index']);
        Route::get('comment/create',[commentController::class,'add_form']);
        Route::post('comment/create',[commentController::class,'add']);
        Route::get('comment/{id}/edit',[commentController::class,'edit_form']);
        Route::post('comment/{id}/edit',[commentController::class,'edit']);
        Route::post('comment/{id}/delete',[commentController::class,'delete_comment']);
        //role
        Route::get('role', [roleController::class,'index']);
        Route::post('role/{id}/delete', [roleController::class,'delete_role']);
        Route::get('role/{id}/edit',[roleController::class,'edit_name_role_form']);
        Route::post('role/{id}/edit',[roleController::class,'edit_name_role']);
        Route::get('role/create', [roleController::class,'add_form']);
        Route::post('role/create', [roleController::class,'add']);
        //permission
        Route::get('permission', [permissionController::class,'index']);
        // Route::get('permission/create',[permissionController::class,'add_form']);
        // Route::post('permission/create',[permissionController::class,'add']);
        // Route::get('permission/{id}/edit',[permissionController::class,'edit_form']);
        // Route::post('permission/{id}/edit',[permissionController::class,'edit']);
        // Route::post('permission/{id}/delete',[permissionController::class,'delete']);
    });
    Route::get('auth',[AuthController::class,'login_form']);
    Route::post('auth',[AuthController::class,'login']);
    Route::get('auth/logout',[AuthController::class,'logout']);
    Route::get('auth/check',[AuthController::class,'check']);
});
//api get category_tree select
Route::post('category/tree_category/select/{id}',[categoryController::class,'get_tree_category']);
// get category product detail
Route::post('product/get_category/{id}',[productController::class,'get_category_product_detail']);

Route::get('send/{id}',[mailController::class,'sendmail']);


