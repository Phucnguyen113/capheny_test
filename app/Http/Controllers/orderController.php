<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class orderController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkInput');
    }
    public function index(Request $request){
        if(!p_author('view','tbl_order')){
            return view('error.403');
        }
        $paramWhere=[]; // param to search
        $list_order=DB::table('tbl_order')->orderByDesc('order_id')
        ->join('tbl_province','tbl_province.id','=','tbl_order.province')
        ->join('tbl_district','tbl_district.id','=','tbl_order.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_order.ward');

        //get list province 
        $list_province=DB::table('tbl_province')->get();
        $list_district=[];
        if($request->has('province') && $request->province !== null && $request->province!=='0'){
            $list_district=DB::table('tbl_district')->where('_province_id',$request->province)->get();
            $paramWhere[]=['tbl_order.province','=',$request->province];
        }
        $list_ward=[];
        if($request->has('district') && $request->district !== null && $request->district!=='0'){
            $list_ward=DB::table('tbl_ward')->where('_district_id',$request->district)->get();
            $paramWhere[]=['tbl_order.district','=',$request->district];
        }
        if($request->has('ward') && $request->ward !== null && $request->ward!=='0'){
            $paramWhere[]=['tbl_order.ward','=',$request->ward];
        }
        if($request->order_address!==null){
            $paramWhere[]=['tbl_order.order_address','Like','%'.$request->order_address.'%'];
        }
        if($request->order_status!==null && is_int($request->order_status)){
            $paramWhere[]=['tbl_order.order_status','=',$request->order_address];
        }
        // process to get data 
      
        if($request->has('user_id') && !empty($request->user_id) ){
            $list_order=$list_order->whereIn('tbl_order.user_id',$request->user_id);
        }
        if( $request->order_name!==null){
            $paramWhere[]=['tbl_order.order_name','LIKE','%'.$request->order_name.'%'];
        }
        if( $request->order_phone!==null){
            $paramWhere[]=['tbl_order.order_phone','LIKE','%'.$request->order_phone.'%'];
        }
        // create_at order process
        if($request->create_at_from!==null && $request->create_at_to!==null){
            $list_order=$list_order->whereBetween('create_at',[$request->create_at_from,$request->create_at_to]);
        }else if($request->create_at_from==null && $request->create_at_to!==null ){
            $list_order=$list_order->whereBetween('create_at',['',$request->create_at_to]);
        }else if($request->create_at_from!==null && $request->create_at_to==null){
            $list_order=$list_order->whereBetween('create_at',[$request->create_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        // update_at order process
        if($request->update_at_from!==null && $request->update_at_to!==null){
            $list_order=$list_order->whereBetween('update_at',[$request->update_at_from,$request->update_at_to]);
        }else if($request->update_at_from==null && $request->update_at_to!==null){
            $list_order=$list_order->whereBetween('update_at',['',$request->update_at_to]);
        }else if($request->update_at_from!==null && $request->update_at_to==null){
            $list_order=$list_order->whereBetween('update_at',[$request->update_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        $list_order=$list_order->where($paramWhere)
        ->select(
            [
                'tbl_province._name as province_',
                'tbl_district._name as district_',
                'tbl_ward._name as ward_',
                'tbl_order.*',
                
            ],
        )->distinct(['tbl_order.order_id'])->paginate(20,['*'],'orderPage');
        $title='Capheny - Danh sách đơn hàng';
        return view('admin.order.index',compact('list_province','list_district','list_ward','list_order','title'));

    }
    public function add_form(){
        if(!p_author('add','tbl_order')){
            return view('error.403');
        }
        $list_province=DB::table('tbl_province')->get(['id','_name']);
        $title='Capheny - Thêm đơn hàng';
        return view('admin.order.add',compact('list_province','title'));
    }
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
        $token=Str::random(50);
        $data_user['token']=$token;
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
                'list_product'=>$list_product,
                'token'=>$token
                ]))
            ->delay(now()->addSeconds(1));
        event(new \App\Events\pusherOrder($order_id));
        p_history(0,'đã thêm đơn hàng mới #'.$order_id,p_user()['user_id']);
        return response()->json(['success'=>'insert success']);
    }
    public function edit_form($id){
        if(!p_author('edit','tbl_order')){
            return view('error.403');
        }
        $order=DB::table('tbl_order')->where('order_id',$id)->first();
        //get order detail 
        $order_detail=DB::table('tbl_order_detail')->join('tbl_product','tbl_product.product_id','=','tbl_order_detail.product_id')
        ->join('tbl_size','tbl_size.size_id','=','tbl_order_detail.product_size_id')
        ->join('tbl_color','tbl_color.color_id','=','tbl_order_detail.product_color_id')->where('order_id',$id)
        ->get(['tbl_product.product_name','tbl_order_detail.*','tbl_size.size','tbl_color.color']);
        $list_province=DB::table('tbl_province')->get(['id','_name']);
        
        $list_user=DB::table('tbl_user')->where('user_id',$order->user_id)->first();
        
        $list_district=DB::table('tbl_district')->where('_province_id',$order->province)->get(['id','_name']);
        $list_ward=DB::table('tbl_ward')->where('_district_id',$order->district)->get(['id','_name']);
        $title='Capheny - Cập nhật đơn hàng';
        return view('admin.order.edit',compact('title','list_province','list_user','list_district','list_ward','order_detail','order'));
    }

    public function edit($id,Request $request){
       
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
                'order_status'      => 'bail|required|numeric|between:0,4'
            ],
            [
                'required'        => ':attribute không được trống',
                'email'           => ':attribute không đúng định dạng',
                'regex'           => ':attribute không đúng định dạng',
                'not_in'          => ':attribute không được trống',
                'numeric'         => ':attribute phải là số',
                'order_address.between' => ':attribute từ :min đến :max ký tự',
                'order_status.between'=>':attribute không hợp lệ'
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
                'order_status'      => 'Trạng thái'
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
            ])->get()->toArray();
            
            $total_amount=0;
            foreach ($amount_product as $amounts => $amount) {
                $total_amount+=$amount->product_amount;
            }
            
            if($total_amount < $request->product_amount[$productss]){
                $list_product_old_amount=DB::table('tbl_order_detail')->where([
                    ['order_id','=',$id],
                    ['product_id','=',$request->product_id[$productss]],
                    ['product_color_id','=',$request->product_color[$productss]],
                    ['product_size_id','=',$request->product_size[$productss]]
                ])->get(['product_amount']);
                $count_product_amount=0;
                foreach ($list_product_old_amount as $product_old_amounts => $product_old_amount) {
                    $count_product_amount+=$product_old_amount->product_amount;
                }
                // nếu tổng số lượng trong kho + sản phẩm trả về kho ko > số lượng mua thì return false
                if( ( $total_amount + $count_product_amount ) < $request->product_amount[$productss] ){
                    $name_error=DB::table('tbl_product')->where('product_id',$product1)->first(['product_name']);
                    return response()->json(['error'=>['amount'=>'Số lượng sản phẩm '.$name_error->product_name." còn lại không đủ"]]);
                }
                
            }
        }
        
        $id_user_not_login=0;
        $update_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
       
        if($request->user_id=='0'){
            $check_user_notlogin_already=DB::table('tbl_user_not_login')->where([
                ['user_email','=',$request->order_email],
                ['user_phone','=',$request->order_phone],
            ])->first();
                $data_user_not_login=
                [
                    'user_email'     => $request->order_email,
                    'user_full_name' => $request->order_name,
                    'user_phone'     => $request->order_phone,
                    'district'       => $request->district,
                    'ward'           => $request->ward,
                    'province'       => $request->province,
                    'user_address'   => $request->order_address,
                    'update_at'      => $update_at
                ];
                
            if(empty($check_user_notlogin_already)){
                // case user_not_login is already
                
                $id_user_not_login=DB::table('tbl_user_not_login')->insertGetId($data_user_not_login);
            }else{
                DB::table('tbl_user_not_login')->where('user_id',$check_user_notlogin_already->user_id)
                ->update($data_user_not_login);
                $id_user_not_login=$check_user_notlogin_already->user_id;
            }
            
        }
      
        //update data user in tbl_order
        $data_user=$request->only(['user_id','order_email','order_phone','order_name','province','district','ward','order_address']);
        $data_user['update_at']=$update_at;
        $data_user['user_not_login_id']=$id_user_not_login;
        $data_user['order_status']=$request->order_status; // status= đã tiếp nhận
        DB::table('tbl_order')->where('order_id',$id)->update($data_user);
        
        //return amount into store
        $list_product_old=DB::table('tbl_order_detail')->where('order_id',$id)->get(['order_detail_id','product_id','product_size_id','product_color_id','product_amount']);
        // foreach array list product old 
       
        foreach ($list_product_old as $products_old => $product_old) {
            //get all record store_product 
            $record_store_product=DB::table('tbl_store_product')->where([
                ['product_id','=',$product_old->product_id],
                ['product_size','=',$product_old->product_size_id],
                ['product_color','=',$product_old->product_color_id],
            ])->get();
            //foreach record_store_product
            foreach ($record_store_product as $stores => $store) {
                if($product_old->product_amount<=0) break;
                //if store->_amount - store_product->product_amount < $product_old->product_amount
                if( ($store->amount_ - $store->product_amount) >= $product_old->product_amount ){
                    DB::table('tbl_store_product')->where('id',$store->id)->update(['product_amount'=> ( $store->product_amount+$product_old->product_amount ) ]);
                    break;
                }else{
                    if($product_old->product_amount<=0) break;
                    // process return amount, store->_amount - store_product->product_amount > $product_old->product_amount
                    $amount_temp=$product_old->product_amount-($store->amount_-$store->product_amount);
                    DB::table('tbl_store_product')->where('id',$store->id)->update(
                        ['product_amount'=>$store->product_amount+($store->amount_-$store->product_amount)]);
                    $product_old->product_amount=$amount_temp;
                }
            }
        }
        
        //delete list _product old in order_detail
        DB::table('tbl_order_detail')->where('order_id',$id)->delete();
        // status >=1 mới tính toán số lượng
        if($request->order_status >=1){
            // process - product_amount in store_product
            foreach ($request->product_id as $products => $product1) {
                $request_amount=$request->product_amount[$products];
                $amount_product=DB::table('tbl_store_product')->where([
                    ['product_id','=',$product1],
                    ['product_size','=',$request->product_size[$products]],
                    ['product_color','=',$request->product_color[$products]],
                    ['product_amount','>',0]
                ])->get();
            
                foreach ($amount_product as $amounts => $amount) {
                    
                if( $request_amount > $amount->product_amount){
                        // if request amount > product amount at store
                        DB::table('tbl_store_product')->where([
                            ['id','=',$amount->id]
                        ])->update(['product_amount'=>0]);
                        $request_amount=$request_amount - $amount->product_amount;
                    
                }else{
                    
                        DB::table('tbl_store_product')->where([
                            ['id','=',$amount->id]
                        ])->update(['product_amount'=>$amount->product_amount - $request_amount]);
                        break;
                }
                }
            }
        }
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
                'order_id'        => $id,
                'product_id'      => $product,
                'product_size_id' => $request->product_size[$products],
                'product_color_id'=> $request->product_color[$products],
                'product_amount'  => $request->product_amount[$products],
                'product_price'   => $arr_price[$products],
                'create_at'       => $update_at
            ]);
       }
       p_history(1,'đã cập nhật đơn hàng #'.$id,p_user()['user_id']);
       return response()->json(['success'=>'insert success']);
    }
    public function delete($id){
        if(!p_author('delete','tbl_order')){
            return view('error.403');
        }
        $check_already=DB::table('tbl_order')->where('order_id',$id)->first();
        if(empty($check_already)){
            return redirect()->back();
        }
        $list_product_of_order=DB::table('tbl_order_detail')->where('order_id',$id)
        ->get(['product_id','product_color_id','product_size_id','product_amount'])->toArray();

        // reset amount in store
        foreach ($list_product_of_order as $products => $product) {
            $list_record_store=DB::table('tbl_store_product')
            ->where(
                [
                    ['product_id','=',$product->product_id],
                    ['product_size','=',$product->product_size_id],
                    ['product_color','=',$product->product_color_id],
                ]
            )->get();
            
            // lặp từng record để add amount vào lại
            foreach ($list_record_store as $records => $record) {
                $amount_tempt=$record->product_amount;
                // nếu record có số lượng nhập trừ số lượng còn lại > số lượng trong đơn hàng
                if( ($record->amount_ - $record->product_amount) >= $product->product_amount){
                    // add trực tiếp vào record
                    DB::table('tbl_store_product')->where('id',$record->id)->update(
                        ['product_amount'=>$record->product_amount+$product->product_amount]
                    );
                    break;
                }else{
                    if($product->product_amount==0) break;
                    // nếu record có số lượng nhập trừ số lượng còn lại < số lượng trong đơn hàng
                    $amount_tempt=( $record->amount_ - $record->product_amount ) ;
                    DB::table('tbl_store_product')->where('id',$record->id)->update(
                        ['product_amount'=>$amount_tempt]
                    );
                    $product->product_amount=$product->product_amount-$amount_tempt;
                }
            }
        }
        //delete order_detail
        DB::table('tbl_order_detail')->where('order_id',$id)->delete();
        DB::table('tbl_order')->where('order_id',$id)->delete();
        p_history(2,'đã xóa đơn hàng  #'.$id,p_user()['user_id']);
        return redirect()->back()->with('success',true);
    }

    public function detail($id){
        if(!p_author('view','tbl_order')){
            return view('error.403');
        }
        $checkNull=DB::table('tbl_order')->where('order_id',$id)->first();
        if(empty($checkNull)) return redirect()->back();
        //list province 
        $list_province=DB::table('tbl_province')->get(['id','_name']);
        //info customer & order
        $user=DB::table('tbl_order')->where('order_id',$id)
        ->join('tbl_province','tbl_province.id','=','tbl_order.province')
        ->join('tbl_district','tbl_district.id','=','tbl_order.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_order.ward')
        ->first([
            'tbl_order.order_id',
            'tbl_order.order_name',
            'tbl_order.order_email',
            'tbl_order.order_phone',
            'tbl_order.user_id',
            'tbl_order.user_not_login_id',
            'tbl_order.order_address',
            'tbl_ward._name as ward',
            'tbl_province._name as province',
            'tbl_district._name as district'
        ]);
        //info order
        $order=DB::table('tbl_order')->where('order_id',$id)->first(['order_status','create_at','update_at']);
        // list product
        $list_product_detail=DB::table('tbl_order_detail')
        ->join('tbl_product','tbl_product.product_id','=','tbl_order_detail.product_id')
        ->join('tbl_size','tbl_size.size_id','=','tbl_order_detail.product_size_id')
        ->join('tbl_color','tbl_color.color_id','=','tbl_order_detail.product_color_id')
        ->join('tbl_order','tbl_order.order_id','=','tbl_order_detail.order_id')
        ->where('tbl_order_detail.order_id',$id)->get(
            [
                'tbl_product.product_id',
                'tbl_product.product_name as product_name',
                'tbl_color.color as color',
                'tbl_color.color_id as color_id',
                'tbl_size.size as size',
                'tbl_size.size_id as size_id',
                'tbl_order_detail.product_amount as product_amount',
                'tbl_order_detail.product_price as product_price',
                'tbl_order.create_at as create_at' 
            ]
        );
        $order->total_product=count($list_product_detail); // total product
        $order->total_price=0;
        
        foreach ($list_product_detail as $products => $product) {
            //total price
            $order->total_price+=($product->product_price * $product->product_amount);
            //check price discount if isset for each product
            $discount=DB::table('tbl_product_discount')->where([
                ['product_id','=',$product->product_id],
                ['discount_from_date','<=',$product->create_at],
                ['discount_end_date','>=',$product->create_at],
            ])->orderByDesc('discount_id')->first(['discount_type','discount_amount']);
            if(!empty($discount)){
                $product->discount=true;
                
            }else{
                $product->discount=false;
            }
        }
        
        return view('admin.order.detail',compact('user','list_product_detail','list_province','order'));
    }

    public function delete_product_order_detail(Request $request,$order_id,$product_id,$size_id,$color_id){
        $check_amount_product_of_order=DB::table('tbl_order_detail')->where('order_id',$order_id)->get()->toArray();
        if(count($check_amount_product_of_order)<=1) return response()->json(['error'=>['amount'=>'Phải có ít nhất 1 sản phẩm trong đơn hàng']]);
        $amount_product_restore=DB::table('tbl_order_detail')->where([
            ['order_id','=',$order_id],
            ['product_id','=',$product_id],
            ['product_size_id','=',$size_id],
            ['product_color_id','=',$color_id],
        ])->first(['product_amount']);
        if(empty($amount_product_restore)) return response()->json(['error'=>['error_sv'=>'Lỗi server']]);
        $list_record_store=DB::table('tbl_store_product')
            ->where(
                [
                    ['product_id','=',$product_id],
                    ['product_size','=',$size_id],
                    ['product_color','=',$color_id],
                ]

            )->get();
            
        // lặp từng record để add amount vào lại
        foreach ($list_record_store as $records => $record) {
            $amount_tempt=$record->product_amount;
            // nếu record có số lượng nhập trừ số lượng còn lại > số lượng trong đơn hàng
            if( ($record->amount_ - $record->product_amount) >= $amount_product_restore->product_amount){
                // add trực tiếp vào record
                DB::table('tbl_store_product')->where('id',$record->id)->update(
                    ['product_amount'=>$record->product_amount+$amount_product_restore->product_amount]
                );
                break;
            }else{
                if($amount_product_restore->product_amount==0) break;
                // nếu record có số lượng nhập trừ số lượng còn lại < số lượng trong đơn hàng
                $amount_tempt=( $record->amount_ - $record->product_amount ) ;
                DB::table('tbl_store_product')->where('id',$record->id)->update(
                    ['product_amount'=>$amount_tempt]
                );
                $amount_product_restore->product_amount=$amount_product_restore->product_amount-$amount_tempt;
            }
        }
        DB::table('tbl_order_detail')->where([
            ['order_id','=',$order_id],
            ['product_id','=',$product_id],
            ['product_size_id','=',$size_id],
            ['product_color_id','=',$color_id],
        ])->delete();
        return response()->json(['success'=>'success']);
    }
   
    public function view_order($token){
        $order=DB::table('tbl_order')
        ->join('tbl_province','tbl_province.id','=','tbl_order.province')
        ->join('tbl_district','tbl_district.id','=','tbl_order.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_order.ward')
        ->where('token',$token)->first(['tbl_order.*','tbl_province._name as province_','tbl_district._name as district_','tbl_district._prefix','tbl_ward._name as ward_','tbl_ward._prefix as _prefix_ward']);
        $order_detail=DB::table('tbl_order_detail')
        ->join('tbl_product','tbl_product.product_id','=','tbl_order_detail.product_id')
        ->where('order_id',$order->order_id)->get(['tbl_order_detail.*','tbl_product.product_name','tbl_product.product_image']);
        $total_price=0;
        foreach ($order_detail as $key => $value) {
            $value->product_image=json_decode($value->product_image,true)[0];
            $total_price+=$value->product_amount*$value->product_price;
            $size=DB::table('tbl_size')->where('size_id',$value->product_size_id)->first();
            $value->size=$size->size;
            $color=DB::table('tbl_color')->where('color_id',$value->product_color_id)->first();
            $value->color=$color->color;

        }
        
        return view('admin.order.view_order',compact('order','order_detail','total_price'));
    }
}
