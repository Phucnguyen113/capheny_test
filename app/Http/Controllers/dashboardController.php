<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function index(){
        $total_order=DB::table('tbl_order')->get()->toArray();
        $total_order=count($total_order);
        $total_product=DB::table('tbl_product')->get()->toArray();
        $total_product=count($total_product);
        $total_user=DB::table('tbl_user')->get()->toArray();
        $total_user=count($total_user);
        $total_user_not_login=DB::table('tbl_user_not_login')->get()->toArray();
        $total_user_not_login=count($total_user_not_login);
        $total_store=DB::table('tbl_store')->get()->toArray();
        $total_store=count($total_store);
        $order_collection=DB::table('tbl_order')->where('order_status',3)->get();
        $total_price=0;
        foreach ($order_collection as $key => $value) {
            $order_detail_colletion=DB::table('tbl_order_detail')->where('order_id',$value->order_id)->get();
            foreach ($order_detail_colletion as $order_details => $order_detail) {
                $total_price+=$order_detail->product_amount*$order_detail->product_price;
            }
        }
        $total_cate=DB::table('tbl_category')->get()->toArray();
        $total_cate=count($total_cate);
        $title='Capheny - Dashboard';
        return view('admin.dashboard.index',compact('title','total_cate','total_store','total_order','total_product','total_user','total_user_not_login','total_price'));
    }
    public function order(){
        $order_status_0=DB::table('tbl_order')->where('order_status',0)->get()->toArray();
        $total_order_status_0=count($order_status_0);
        $order_status_1=DB::table('tbl_order')->where('order_status',1)->get()->toArray();
        $total_order_status_1=count($order_status_1);
        $order_status_2=DB::table('tbl_order')->where('order_status',2)->get()->toArray();
        $total_order_status_2=count($order_status_2);
        $order_status_3=DB::table('tbl_order')->where('order_status',3)->get()->toArray();
        $total_order_status_3=count($order_status_3);
        $order_status_4=DB::table('tbl_order')->where('order_status',4)->get()->toArray();
        $total_order_status_4=count($order_status_4);
        return response()->json(['data'=>[
            'status_0'=>$total_order_status_0,
            'status_1'=>$total_order_status_1,
            'status_2'=>$total_order_status_2,
            'status_3'=>$total_order_status_3,
            'status_4'=>$total_order_status_4,
            ]
        ]);
    }
    public function store_product(){
        $list_store_collection=DB::table('tbl_store')->get();
        $total_store=[];
        $total_product=[];
        foreach ($list_store_collection as $key => $value) {
            $list_product_collection=DB::table('tbl_store_product')->where('store_id',$value->store_id)->distinct()->get();
            $total_product[]=count($list_product_collection);
            $total_store[]=$value->store_name;
        }
        return response()->json(['data'=>compact('total_store','total_product')]);
    }
}
