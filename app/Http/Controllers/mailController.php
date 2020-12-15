<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class mailController extends Controller
{
    public function sendmail($order_id)
    {   
        // try {
            $info_order=DB::table('tbl_order')->where('order_id',$order_id)
            ->join('tbl_province','tbl_province.id','=','tbl_order.province')
            ->join('tbl_district','tbl_district.id','=','tbl_order.district')
            ->join('tbl_ward','tbl_ward.id','=','tbl_order.ward')
            ->first(['tbl_order.*','tbl_province._name as province_name','tbl_district._name as district_name','tbl_ward._name as ward_name']);
            
            $list_product=DB::table('tbl_order_detail')->where('order_id',$order_id)
            ->join('tbl_product','tbl_product.product_id','=','tbl_order_detail.product_id')
            ->join('tbl_size','tbl_size.size_id','=','tbl_order_detail.product_size_id')
            ->join('tbl_color','tbl_color.color_id','=','tbl_order_detail.product_color_id')
            ->get(
                ['tbl_order_detail.*','tbl_size.size','tbl_color.color','tbl_product.product_name','tbl_product.product_image']
            );
            foreach ($list_product as $images => $image) {
                $image=json_decode($image->product_image,true);
                $list_product[$images]->image=$image[0];
            }
            dispatch(new SendEmail([
                'id'=>$info_order->order_id,
                'name'=>$info_order->order_name,
                'email'=>$info_order->order_email,
                'phone'=>$info_order->order_phone,
                'province'=>$info_order->province_name,
                'district'=>$info_order->district_name,
                'ward'=>$info_order->ward_name,
                'address'=>$info_order->order_address,
                'create_at'=>$info_order->create_at,
                'token' =>$info_order->token,
                'list_product'=>$list_product]))->delay(now()->addSeconds(1));
                 
            return response()->json(['success'=>'success']);
        // } catch (\Throwable $th) {
        //    return response()->json(['error'=>'error']);
        // }
        
    }
}
