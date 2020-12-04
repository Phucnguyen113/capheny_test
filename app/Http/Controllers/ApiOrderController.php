<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiOrderController extends Controller
{
    public function add(Request $request){
       
        $validated=Validator::make($request->all(),
            [
                'user_id'           => 'bail|required|numeric',
                'order_email'       => 'bail|required|email',
                'order_name'        => 'bail|required',
                'order_phone'       => 'bail|required|regex:/^0[0-9]{9,10}$/',
                'province'          => 'bail|required|not_in:0',
                'district'          => 'bail|required|not_in:0',
                'ward'              => 'bail|required|not_in:0',
                'order_address'     => 'bail|required|between:5,50',
                'product_id'        => 'bail|required|array',
                'product_id.*'      => 'bail|numeric',
                'product_size'      => 'bail|required|array',
                'product_size.*'    => 'bail|required|numeric',
                'product_color'     => 'bail|required|array',
                'product_color.*'   => 'bail|required|numeric',
                'product_amount'    => 'bail|required|array',
                'product_amount.*'  => 'bail|required|numeric',
            ],
            [
                'required'        => ':attribute không được trống',
                'email'           => ':attribute không đúng định dạng',
                'regex'           => ':attribute không đúng định dạng',
                'not_in'          => ':attribute không được trống',
                'numeric'         => ':attribute phải là số',
                'between'         => ':attribute từ :min đến :max ký tự',
            ], 
            [
                'user_id'           => 'Người dùng',
                'user_not_login_id' => 'Người dùng không đăng nhập',
                'order_email'       => 'Email',
                'order_name'        => 'Tên người mua',
                'order_phone'       => 'Điện thoại',
                'province'          => 'Thành phố/Tỉnh',
                'district'          => 'Quận/Huyện',
                'ward'              => 'Khu vực',
                'order_address'     => 'Địa chỉ',
                'product_id'        => 'Sản phẩm',
                'product_id.*'      => 'Sản phẩm',
                'product_size'      => 'Kích cỡ sản phẩm',
                'product_size.*'    => 'Kích cỡ sản phẩm',
                'product_color'     => 'Màu sản phẩm',
                'product_color.*'   => 'Màu sản phẩm',
                'product_amount'    => 'Số lượng sản phẩm',
                'product_amount.*'  => 'Số lượng sản phẩm',
               
            ]
        );

        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        //validate size & color & product is true
        foreach ($request->product_id as $productss => $product1) {
            $check_product_is_onready=DB::table('tbl_product')->where('product_id',$product1)->first();
            if(empty($check_product_is_onready)){
                return response()->json([
                    'error'=>[
                        'product_500'=>'Sản phẩm ko tồn tại'
                    ]]);
            }
            $check_size_is_onready=DB::table('tbl_product_size')
            ->where([
                ['product_id','=',$product1],
                ['size_id','=',$request->product_size[$productss]]
            ])->first();
            if(empty($check_size_is_onready)){
                return response()->json([
                    'error'=>['size_500'=>'Kích thước của sản phẩm sai ở đâu đó']
                ]);
            }
            $check_color_is_onready=DB::table('tbl_product_color')
            ->where([
                ['product_id','=',$product1],
                ['color_id','=',$request->product_color[$productss]]
            ])->first();
            if(empty($check_color_is_onready)){
               return response()->json([
                   'error'=>['color_500'=>'Màu của sản phẩm sai ở đâu đó']
               ]);
            }
            // check amount_product 
            $amount_product=DB::table('tbl_store_product')->where([
                ['product_id','=',$product1],
                ['product_size','=',$request->product_size[$productss]],
                ['product_color','=',$request->product_color[$productss]],
                ['product_amount','>',0]
            ])->get();
            $total_amount=0;
            foreach ($amount_product as $amounts => $amount) {
                $total_amount+=$amount->product_amount;
            }
            $request_amount=$request->product_amount[$productss];
            if($total_amount<$request_amount){
                $name_error=DB::table('tbl_product')->where('product_id',$product1)->first(['product_name']);
                return response()->json(['error'=>['amount'=>'Số lượng sản phẩm '.$name_error->product_name." còn lại không đủ"]]);
            }
        }   

        // - amount product
        foreach ($request->product_id as $productss => $product1) {
            $amount_product=DB::table('tbl_store_product')->where([
                ['product_id','=',$product1],
                ['product_size','=',$request->product_size[$productss]],
                ['product_color','=',$request->product_color[$productss]],
                ['product_amount','>',0]
            ])->get();
            $request_amount=$request->product_amount[$productss];
           
            foreach ($amount_product as $amounts => $amount) {
               
               if( $request_amount > $amount->product_amount){
                   if($request_amount==0) break;
                    // if request amount > product amount at store
                    DB::table('tbl_store_product')->where('id',$amount->id)->update(['product_amount'=>0]);
                    $request_amount=$request_amount - $amount->product_amount;
               }else{
                    DB::table('tbl_store_product')->where([
                        ['id','=',$amount->id]
                    ])->update(['product_amount'=>$amount->product_amount - $request_amount]);
                    break;
               }
            }
        }
        
        $id_user_not_login=0;
        $create_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        if($request->user_id=='0'){
            // case didn't picked user list
            $data_user_not_login=
            [
                'user_email'     => $request->order_email,
                'user_full_name' => $request->order_name,
                'user_phone'     => $request->order_phone,
                'district'       => $request->district,
                'ward'           => $request->ward,
                'province'       => $request->province,
                'user_address'   => $request->order_address,
                'create_at'      => $create_at
            ];
         
            $id_user_not_login=DB::table('tbl_user_not_login')->insertGetId($data_user_not_login);
        }
        //insert data user in tbl_order
        $data_user=$request->only(['user_id','order_email','order_phone','order_name','province','district','ward','order_address']);
        $data_user['create_at']=$create_at;
        $data_user['user_not_login_id']=$id_user_not_login;
        $data_user['order_status']=1; // status= đã tiếp nhận
        $order_id=DB::table('tbl_order')->insertGetId($data_user);
        //get price for product
        $today=Carbon::now('Asia/Ho_CHi_Minh')->toDateTimeString();
        $arr_price=[];
        foreach ($request->product_id as $products => $product) {
            $price=DB::table('tbl_product')->where('product_id',$product)->first(['product_price']);
            $discount=DB::table('tbl_product_discount')->where([
                ['product_id',$product],
                ['discount_from_date','<=',$today],
                ['discount_end_date','>=',$today],
            ])->orderByDesc('discount_id')->first();
            if(!empty($discount)){
                if($discount->discount_type==1){
                    $price->product_price-=$discount->discount_amount;
                }else{
                    $price->product_price-=($price->product_price*$discount->discount_amount)/100;
                }
            }
            $arr_price[]=$price->product_price;
        }
        //insert data product in tbl_order_detail
        foreach ($request->product_id as $products => $product) {
            DB::table('tbl_order_detail')->insert([
                'order_id'        => $order_id,
                'product_id'      => $product,
                'product_size_id' => $request->product_size[$products],
                'product_color_id'=> $request->product_color[$products],
                'product_amount'  => $request->product_amount[$products],
                'product_price'   => $arr_price[$products],
                'create_at'       =>$create_at
            ]);
       }
       // send mail
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
                'list_product'=>$list_product]))->delay(now()->addSeconds(1));
       return response()->json(['success'=>'insert success']);
    }
}
