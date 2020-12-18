<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDO;

class storeController extends Controller
{
    function index(Request $request){
        if(!p_author('view','tbl_store')){
            return view('error.403');
        }
        $param_search=[];
        $list_store=DB::table('tbl_store')->orderByDesc('store_id')
        ->join('tbl_province','tbl_province.id','=','tbl_store.province')
        ->join('tbl_district','tbl_district.id','=','tbl_store.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_store.ward')
        ->select(['tbl_store.store_id','tbl_store.store_name','tbl_store.create_at','tbl_store.update_at','tbl_store.store_address','tbl_province._name as province','tbl_district._name as district','tbl_ward._name as ward']);
        // create_at product process
        if($request->create_at_from!==null && $request->create_at_to!==null){
            $list_store=$list_store->whereBetween('create_at',[$request->create_at_from,$request->create_at_to]);
        }else if($request->create_at_from==null && $request->create_at_to!==null ){
            $list_store=$list_store->whereBetween('create_at',['',$request->create_at_to]);
        }else if($request->create_at_from!==null && $request->create_at_to==null){
            $list_store=$list_store->whereBetween('create_at',[$request->create_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        // update_at product process
        if($request->update_at_from!==null && $request->update_at_to!==null){
            $list_store=$list_store->whereBetween('update_at',[$request->update_at_from,$request->update_at_to]);
        }else if($request->update_at_from==null && $request->update_at_to!==null){
            $list_store=$list_store->whereBetween('update_at',['',$request->update_at_to]);
        }else if($request->update_at_from!==null && $request->update_at_to==null){
            $list_store=$list_store->whereBetween('update_at',[$request->update_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }

        if($request->store_name!==null) $param_search[]=['tbl_store.store_name','LIKE','%'.$request->store_name.'%'];
        $list_district=[];
        if($request->province!==null && $request->province!=='0'){
            $param_search[]=['tbl_store.province','=',$request->province];
            $list_district=DB::table('tbl_district')->where('_province_id',$request->province)->get();
        }
        $list_ward=[];
        if($request->district!==null && $request->district!=='0'){
            $param_search[]=['tbl_store.district','=',$request->district];
            $list_ward=DB::table('tbl_ward')->where('_district_id',$request->district)->get();
        }
        if($request->ward!==null && $request->ward!=='0') $param_search[]=['tbl_store.ward','=',$request->ward];

        $list_store=$list_store->where($param_search)->distinct(['tbl_store.store_id'])->paginate(20);
        $list_province=DB::table('tbl_province')->get();
        $title='Capheny - Danh sách cửa hàng';
       
        return view('admin.store.index',compact('list_store','list_province','list_district','list_ward','title'));
    }

    function create(){
        if(!p_author('add','tbl_store')){
            return view('error.403');
        }
        $list_province=DB::table('tbl_province')->get();
        $title='Capheny - Thêm cửa hàng';
        return view('admin.store.add',compact('list_province','title'));
    }
    function store(Request $request){
        $validated=Validator::make($request->all(),
        [
            'store_name'       => 'required|bail',
            'province'         => 'required|bail|not_in:0',
            'district'         => 'required|bail|not_in:0',
            'ward'             => 'required|bail|not_in:0',
            'store_address'    => 'required|bail|'
        ],
        [
            'required' => ':attribute không được trống',
            'not_in'   => 'Chưa chọn:attribute '
        ],
        [
            'store_name'    => 'Tên cửa hàng',
            'province'      => 'Thành phố, tỉnh',
            'district'      => 'Quận, huyện',
            'ward'          => 'Khu vực',
            'store_address' => 'Địa chỉ'
        ]);
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $create_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString(); 
        $id=DB::table('tbl_store')->insertGetId(array_merge($request->except(['_token']),['create_at'=>$create_at],['create_by'=>p_user()['user_id']]));
        p_history(0,'đã thêm cửa hàng mới  #'.$id,p_user()['user_id']);
        return response()->json(['success'=>'sucess']);
    }
    public function form_add_product(){
        if(!p_author('add_product','tbl_store')){
            return view('error.403');
        }
        $list_store=DB::table('tbl_store')->orderByDesc('store_id')->get();
        $list_product=DB::table('tbl_product')->orderByDesc('product_id')->get();
        $title='Capheny - Nhập sản phẩm về cửa hàng ';
        return view('admin.store.addproduct',compact('list_store','list_product','title'));
    }
    public function add_product(Request $request){
        
        $validated=Validator::make($request->all(),
            [
                'product' => 'bail|required',
                'store'      => 'bail|required',
                'product_amount.*'=> 'bail|sometimes|nullable|numeric|min:0',
                'product_amount' => 'bail|sometimes|nullable|array'

            ],
            [
                'required'=>':attribute không được trống',
                'numeric' => ':attribute phải là số',
                'min'=> ':atribute nhỏ nhất là :min'
            ],
            [
                'product' =>'Sản phẩm',
                'store' => 'Cửa hàng',
                'product_amount.*' =>'Số lượng'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $create_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        $size=DB::table('tbl_product_size')->join('tbl_size','tbl_size.size_id','=','tbl_product_size.size_id')->where('tbl_product_size.product_id',$request->product)->get(['tbl_size.size_id','tbl_size.size']);
        $color=DB::table('tbl_product_color')->join('tbl_color','tbl_color.color_id','=','tbl_product_color.color_id')->where('tbl_product_color.product_id',$request->product)->get(['tbl_color.color_id','tbl_color.color']);
        //store
        foreach ($request->store as $stores => $store) {
            $product_amount=0;
            // product
            foreach ($color as $key => $value) {
                // size
                foreach ($size as $key1 => $value1) {
                    if($request->product_amount[$product_amount]!==null){
                        DB::table('tbl_store_product')->insert(
                            [
                                'product_id'     => $request->product,
                                'store_id'       => $store,
                                'product_size'   => $value1->size_id,
                                'product_color'  => $value->color_id,
                                'product_amount' => ($request->product_amount[$product_amount])?$request->product_amount[$product_amount]:0,
                                'amount_'        => ($request->product_amount[$product_amount])?$request->product_amount[$product_amount]:0,
                                'create_at'      => $create_at,
                               
                            ]
                        );
                    }
                    
                    $product_amount++;
                }
            }
        }// end
       
        return response()->json(['success'=>'successs']);
    }
    //detail store
    public function detail($id){
        if(!p_author('view','tbl_store')){
            return view('error.403');
        }
        $data=DB::table('tbl_store')->join('tbl_province','tbl_province.id','=','tbl_store.province')
        ->join('tbl_district','tbl_district.id','=','tbl_store.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_store.ward')
        ->select(['tbl_store.store_id','tbl_store.store_name','tbl_store.create_at','tbl_store.update_at','tbl_store.store_address','tbl_province._name as province','tbl_district._name as district','tbl_ward._name as ward'])
        ->where('tbl_store.store_id',$id)->first();
        // list product
        $list_product=DB::table('tbl_store_product')->where('store_id',$id)
        ->join('tbl_size','tbl_size.size_id','=','tbl_store_product.product_size')
        ->join('tbl_color','tbl_color.color_id','=','tbl_store_product.product_color')
        ->join('tbl_product','tbl_product.product_id','=','tbl_store_product.product_id')
        ->orderByDesc('tbl_product.product_id')
        ->select(['tbl_store_product.id','tbl_store_product.amount_','tbl_size.size as size','tbl_color.color as color','tbl_store_product.product_amount','tbl_product.product_name as product_name','tbl_store_product.create_at as create_at'])
        ->paginate(20);
        $data->list_product=$list_product;

        // mappingg amount
        $list_product_distinct=DB::table('tbl_store_product')
        ->join('tbl_product','tbl_product.product_id','=','tbl_store_product.product_id')
        ->where('store_id',$id)->distinct(['tbl_store_product.product_id'])->paginate(20,['tbl_store_product.product_id'],'por');
        
        foreach ($list_product_distinct as $products => $product) {
            $product_collection=DB::table('tbl_product')->where('product_id',$product->product_id)->first();
            if(!empty($product_collection)){
                $list_product_distinct[$products]->product_name=$product_collection->product_name;
            }else{
                $list_product_distinct[$products]->product_name='N/A';
            }
            $list_size_product=DB::table('tbl_product_size')->join('tbl_size','tbl_size.size_id','=','tbl_product_size.size_id')
            ->select(['tbl_size.size','tbl_size.size_id as size_id'])->where('tbl_product_size.product_id',$product->product_id)->get();

            $list_color_product=DB::table('tbl_product_color')->join('tbl_color','tbl_color.color_id','=','tbl_product_color.color_id')
            ->select(['tbl_color.color','tbl_color.color_id as color_id'])->where('tbl_product_color.product_id',$product->product_id)->get();
          
            foreach ($list_size_product as $sizes => $size) {
                foreach ($list_color_product as $colors => $color) {
                    $product_amount=DB::select('select SUM(product_amount) as total_product from tbl_store_product where store_id=? AND product_id=? AND product_size=? AND product_color=?',[$id,$product->product_id,$size->size_id,$color->color_id]);
                    $list_product_distinct[$products]->detail[]=['color'=>$color->color,'size'=>$size->size,'amount'=>$product_amount[0]->total_product];
                }
            }
        }
      
        $title='Capheny - Chi tiết cửa hàng';
        return view('admin.store.detail',compact('data','list_product_distinct','title'));
    }
    public function delete_product_from_store($id){
        $check=DB::table('tbl_store_product')->where('id',$id)->first();
        if($check->product_amount !== $check->amount_) return redirect()->back()->withErrors(['amount'=>'Đã có sản phẩm được bán']);
        DB::table('tbl_store_product')->where('id',$id)->delete();
        return redirect()->back();
    }
    public function edit_product_from_store_form($id){
        $data=DB::table('tbl_store_product')
        ->join('tbl_size','tbl_size.size_id','=','tbl_store_product.product_size')
        ->join('tbl_color','tbl_color.color_id','=','tbl_store_product.product_color')
        ->where('id',$id)->first(['tbl_store_product.id as id','tbl_store_product.product_amount','tbl_color.color as color','tbl_size.size as size']);
        $title='Capheny - Chỉnh sửa sản phẩm ở cửa hàng';
        return view('admin.store.editproduct',compact('data','title'));
    }
    public function edit_product_from_store(Request $request,$id){
        $validated= Validator::make($request->all(),
            ['product_amount'=>'bail|sometimes|nullable|numeric|min:0'],
            [
                'numeric'=>':attribute phải là số',
                'min'=>':attribute nhỏ nhất là :min'
            ],
            [
                'product_amount'=>' Số lượng sản phẩm'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        if($request->product_amount==null) $request->product_amount=0;
        $update_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();

        DB::table('tbl_store_product')->where('id',$id)->update(
            [
                'product_amount'=> $request->product_amount,
                'amount_'       => $request->product_amount,
                'update_at'     => $update_at
            ]
        );
        return response()->json(['success'=>'success']);
    }

    public function edit_store_form($id){
        if(!p_author('edit','tbl_store')){
           return view('error.403');
        }
        $data=DB::table('tbl_store')->where('store_id',$id)->first();
        $list_province=DB::table('tbl_province')->get();
        $list_district=DB::table('tbl_district')->where('_province_id',$data->province)->get();
        $list_ward=DB::table('tbl_ward')->where('_district_id',$data->district)->get();
        $title='Capheny - Cập nhật cửa hàng';
        return view('admin.store.edit',compact('list_province','data','list_province','list_district','list_ward','title'));
    }
    public function edit_store(Request $request,$id){
        $validated=Validator::make($request->all(),
        [
            'store_name'       => 'required|bail',
            'province'         => 'required|bail|not_in:0',
            'district'         => 'required|bail|not_in:0',
            'ward'             => 'required|bail|not_in:0',
            'store_address'    => 'required|bail|'
        ],
        [
            'required' => ':attribute không được trống',
            'not_in'   => 'Chưa chọn:attribute '
        ],
        [
            'store_name'    => 'Tên cửa hàng',
            'province'      => 'Thành phố, tỉnh',
            'district'      => 'Quận, huyện',
            'ward'          => 'Khu vực',
            'store_address' => 'Địa chỉ'
        ]);
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $update_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString(); 
        DB::table('tbl_store')->where('store_id',$id)->update(array_merge($request->except(['_token']),['update_at'=>$update_at],['update_by'=>p_user()['user_id']]));
        p_history(1,'đã cập nhật cửa hàng  #'.$id,p_user()['user_id']);
        return response()->json(['success'=>'sucess']);
    }
    public function delete_store($id){
        if(!p_author('delete','tbl_store')){
            return view('error.403');
        }
        $list_product_of_store=DB::table('tbl_store_product')->where('store_id',$id)->get()->toArray();
        if(count($list_product_of_store)>0){
            return redirect()->back()->withErrors(['error'=>'Cửa hàng đã có sản phẩm, không thể xóa']);
        }
        DB::table('tbl_store')->where('store_id',$id)->delete();
        p_history(2,'đã xóa cửa hàng  #'.$id,p_user()['user_id']);
        return redirect()->back()->with('success','success');
    }
}
