<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class productController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkInput');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!p_author('view','tbl_product')){
            return view('error.403');
        }
        //data filter
        $list_color_search=DB::table('tbl_color')->orderByDesc('color_id')->get();
        $list_size_search=DB::table('tbl_size')->orderByDesc('size_id')->get();
        // search case 
      
        // data table
        $list_product=DB::table('tbl_product')->select(['tbl_product.*'])->orderByDesc('product_id');
        $binding=[];
        if($request->has('discount_type') && $request->discount_type!==null){
            $list_product->join('tbl_product_discount','tbl_product_discount.product_id','=','tbl_product.product_id');

            $binding[]=['tbl_product_discount.discount_type','=',$request->discount_type];
            if(  $request->discount_from_date!==null && $request->discount_end_date !==null ){
                $binding[]=['tbl_product_discount.discount_from_date','>=',$request->discount_from_date];
                $binding[]=['tbl_product_discount.discount_end_date','<=',$request->discount_end_date];
            }else if( $request->discount_from_date==null && $request->discount_end_date !==null){
                $binding[]=['tbl_product_discount.discount_end_date','<=',$request->discount_end_date];
            }else if( $request->discount_from_date!==null && $request->discount_end_date ==null){
                $binding[]=['tbl_product_discount.discount_from_date','>=',$request->discount_from_date];
            }
           
        }
         // color process
         if($request->has('color') && !empty($request->color)){
            $list_product->join('tbl_product_color','tbl_product_color.product_id','=','tbl_product.product_id');
            $list_product=$list_product->whereIn('tbl_product_color.color_id',$request->color);
        }
         // size process
        if($request->has('size') && !empty($request->size)){
            $list_product->join('tbl_product_size','tbl_product_size.product_id','=','tbl_product.product_id');
            $list_product=$list_product->whereIn('tbl_product_size.size_id',$request->size);
        }
         // category process
         if($request->has('category') && $request->category!==null){
            $list_product->join('tbl_category_product','tbl_category_product.product_id','=','tbl_product.product_id');
            $request->category=explode(',',trim($request->category,','));
            $list_product=$list_product->whereIn('tbl_category_product.category_id',$request->category);
        }
         // name_product
         if($request->has('product_name') && $request->product_name !==null){
            $binding[]=  ['tbl_product.product_name','LIKE',"%".$request->product_name.'%'];
        }
        
        $list_product=$list_product->where($binding);
        // create_at product process
        if($request->create_at_from!==null && $request->create_at_to!==null){
            $list_product=$list_product->whereBetween('create_at',[$request->create_at_from,$request->create_at_to]);
        }else if($request->create_at_from==null && $request->create_at_to!==null ){
            $list_product=$list_product->whereBetween('create_at',['',$request->create_at_to]);
        }else if($request->create_at_from!==null && $request->create_at_to==null){
            $list_product=$list_product->whereBetween('create_at',[$request->create_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        // update_at product process
        if($request->update_at_from!==null && $request->update_at_to!==null){
            $list_product=$list_product->whereBetween('update_at',[$request->update_at_from,$request->update_at_to]);
        }else if($request->update_at_from==null && $request->update_at_to!==null){
            $list_product=$list_product->whereBetween('update_at',['',$request->update_at_to]);
        }else if($request->update_at_from!==null && $request->update_at_to==null){
            $list_product=$list_product->whereBetween('update_at',[$request->update_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        // end process filter

        $list_product=$list_product->distinct('tbl_product.product_id')->paginate(20);
       
        foreach ($list_product as $key => $value) {
            $currentDate=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
            $discount=DB::table('tbl_product_discount')->where([
                ['product_id',$value->product_id],
                ['discount_from_date','<=',$currentDate],
                ['discount_end_date','>=',$currentDate],
            ])->orderByDesc('discount_id')->first();
            // check product has discount in today 
            if(!empty($discount)){
                // product has discount in today
                $list_product[$key]->discount='Có';
            }else{
                // product hasn't discount in today
                $list_product[$key]->discount='Không';
                
            }

            //get color and size product
            $list_color=DB::table('tbl_color')->select(['tbl_color.color'])
            ->join('tbL_product_color','tbl_color.color_id','=','tbl_product_color.color_id')
            ->where('tbl_product_color.product_id',$value->product_id)->get()->toArray();
            if(!empty($list_color)){
                foreach ($list_color as $colors => $color) {
                    $list_product[$key]->colors[]=$color->color;
                }
            }else{
                $list_product[$key]->colors=[];
            }

            $list_size=DB::table('tbl_size')->select(['size'])->join('tbl_product_size','tbl_product_size.size_id','=','tbl_size.size_id')->where('tbl_product_size.product_id',$value->product_id)->get()->toArray();
            if(!empty($list_size)){
                foreach ($list_size as $sizes => $size) {
                    $list_product[$key]->sizes[]=$size->size;
                }
            }else{
                $list_product[$key]->sizes=[];
            }
            //get list category
            $list_category=DB::table('tbl_category_product')->join('tbl_category','tbl_category.category_id','=','tbl_category_product.category_id')
            ->where('tbl_category_product.product_id',$value->product_id)->get(['tbl_category.category_id','tbl_category.category_name'])->toArray();
            if(!empty($list_category)){
                foreach ($list_category as $categories => $category) {
                    $list_product[$key]->category[]=$category;
                }
            }else{
                $list_product[$key]->category=[];
            }
        }
        
        return view('admin.product.index',['list_product'=>$list_product,'color_search'=>$list_color_search,'size_search'=>$list_size_search]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!p_author('add','tbl_product')){
            return view('error.403');
        }
        $list_cate=DB::table('tbl_category')->get();
        $list_cate=$this->get_category_tree($list_cate);
        $color=DB::table('tbl_color')->orderByDesc('color_id')->get();
        $size=DB::table('tbl_size')->orderByDesc('size_id')->get();
        return view('admin.product.add',['list_cate'=>$list_cate,'color'=>$color,'size'=>$size]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $validated= Validator::make($request->all(),
            [
                'product_name'       => 'required|bail',
                'product_slug'       => 'bail|required|unique:tbl_product,product_slug',
                'description'        => 'required',
                'discount_type'      => 'numeric|bail|required',
                'discount_amount'    => 'bail|required_unless:discount_type,0|numeric',
                'discount_from_date' => 'bail|required_unless:discount_type,0|date|after_or_equal:today',
                'discount_end_date'  => 'bail|required_unless:discount_type,0|date|after_or_equal:discount_from_date',
                'active'             => 'boolean|bail|required',
                'image'              => 'required',
                'category'           => 'required',
                'color'              => 'required',
                'size'               => 'required',
                'product_price'      => 'bail|required|numeric|max:10000000'
            ],
            [
                'required'=>':attribute không được trống ',
                'unique'=>':attribute đã tồn tại',
                'boolean'=>':attribute phải là kiểu boolean',
                'required_unless'=>':attribute không được trống khi có :other ',
                'numeric'=>':attribute phải là số',
                'discount_end_date.after_or_equal'=>':attribute phải sau :date',
                'discount_from_date.after_or_equal'=>':attribute phải từ ngày hôm nay trở đi ',
                'date'=>':attribute không đúng định dạng ngày tháng năm',
                'max'=>':attribute tối đa là :max'
            ],
            [
                'product_name'       => 'Tên sản phẩm',
                'product_slug'       => 'Đường dẫn sản phẩm',
                'description'        => 'Mô tả',
                'discount_type'      => 'Giảm giá',
                'discount_amount'    => 'Số tiền giảm giá',
                'discount_from_date' => 'Ngày bắt đầu giảm giá',
                'discount_end_date'  => 'Ngày kết thúc giảm giá',
                'Active'             => 'Active',
                'category'           => 'Danh mục',
                'size'               => 'Size',
                'color'              => 'Color',
                'image'              => 'Ảnh',
                'category'           => 'Danh mục',
                'product_price'      => 'Giá sản phẩm'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        // validate hour and minute discount_date
        if($request->discount_type!=='0'){
            if(strtotime($request->discount_end_date)==strtotime($request->discount_from_date)) return response()->json(['error'=>['discount_end_date'=>'Giờ ngày kết thúc khuyến mãi phải lớn hơn giờ ngày bắt đầu']]);
        }
        // validate image one more time
        for ($i=0; $i <count($request->image) ; $i++) { 
            $image=['image'=>$request->image[$i]];
            $validatedImage=Validator::make($image,
                [
                    'image'=>'image'
                ],
                [
                    'image'=>' File '.$request->image[$i]->getClientOriginalName().' phải có dạng jpg,jpeg,png,svg,gif'
                ]
            );
            if($validatedImage->fails()) return response()->json(['error'=>$validatedImage->getMessageBag()]);
        }
        // validateImage success to upload image
        $image_json=[];
        for ($i=0; $i < count($request->image); $i++) { 
            $image=$request->image[$i];
            $newNameImg=$request->product_slug.'_'.$i.'_'.date('Y_m_d').'.'.$image->getClientOriginalExtension();
            $image_json[]=$newNameImg;
            $image->move('images/product',$newNameImg);
            DB::table('tbl_image')->insert([ 'image_path'=>$newNameImg,'type'=>1]);
        }
        //process date
        $end_date=$request->discount_end_date;  
        $from_date=$request->discount_from_date;
        //create_at
        $create_at=Carbon::now('Asia/Ho_Chi_minh')->toDateTimeString();
        // insert data to tbl_product
        $idProduct=DB::table('tbl_product')->insertGetId(array_merge($request->except(['image','_token','category','discount_type','discount_amount','discount_end_date','discount_from_date','color','size']),['product_image'=>json_encode($image_json)],['create_at'=>$create_at],['user_create'=>p_user()['user_id']]));
        if($request->discount_type!=='0'){  
            //insert discount product 
            DB::table('tbl_product_discount')->insert(['product_id'=>$idProduct,'discount_type'=>$request->discount_type,'discount_amount'=>$request->discount_amount,'discount_end_date'=>$end_date,'discount_from_date'=>$from_date]);
        }

        // insert data to tbl_category_product
        if(is_array($request->category)){
            $category=explode(',',$request->category[0]);
            for ($i=0; $i < count($category); $i++) { 
                DB::table('tbl_category_product')->insert(['category_id'=>$category[$i],'product_id'=>$idProduct]);
            }
        }
        // insert product size,color
        for ($i=0; $i < count($request->color); $i++) { 
            DB::table('tbl_product_color')->insert(['product_id'=>$idProduct,'color_id'=>$request->color[$i]]);
        }
        for ($i=0; $i < count($request->size); $i++) { 
            DB::table('tbl_product_size')->insert(['product_id'=>$idProduct,'size_id'=>$request->size[$i]]);
        }
        return response()->json(['success'=>'Insert product success']);    

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!p_author('add','tbl_product')){
            return view('error.403');
        }
        try{
            $list_cate=DB::table('tbl_category')->get();
            $list_cate=$this->get_category_tree($list_cate);
            $list_color=DB::table('tbl_color')->orderByDesc('color_id')->get();
            $list_size=DB::table('tbl_size')->orderByDesc('size_id')->get();
            //get product detail
            $product=DB::table('tbl_product')->where('product_id',$id)->first();
            // get color product
            $color=DB::table('tbl_color')->select(['tbl_color.color','tbl_color.color_id'])->join('tbL_product_color','tbl_product_color.color_id','=','tbl_color.color_id')->where('product_id',$product->product_id)->get()->toArray();
            $product->color=$color;
            //get size product
            $size=DB::table('tbl_size')->select(['tbl_size.size','tbl_size.size_id'])->join('tbl_product_size','tbl_product_size.size_id','=','tbl_size.size_id')->where('product_id',$product->product_id)->get()->toArray();
            $product->size=$size;
            // get discount type product
            $currentDate=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
            $discount=DB::table('tbl_product_discount')->where([
                ['product_id',$product->product_id],
                ['discount_from_date','<=',$currentDate],
                ['discount_end_date','>=',$currentDate],
            ])->orderByDesc('discount_id')->first();
            // check product has discount in today 
            if(!empty($discount)){
                // product has discount in today
                $product->discount_type=$discount->discount_type;
                $product->discount_amount=$discount->discount_amount;
                $product->discount_from_date=$discount->discount_from_date;
                $product->discount_end_date=$discount->discount_end_date;
            }else{
                // product hasn't discount in today
                $product->discount_type=0;
                $product->discount_amount='';
                $product->discount_from_date='';
                $product->discount_end_date='';
            }
            return view('admin.product.edit',['list_cate'=>$list_cate,'color'=>$list_color,'size'=>$list_size,'product'=>$product]);
        }catch(\Exception $e){
            return redirect('admin/product');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated= Validator::make($request->all(),
            [
                'product_name'=>'required|bail',
                'product_slug'=>[
                    'bail',
                    'required',
                    Rule::unique('tbl_product')->ignore($id,'product_id')
                ],
                'description'=>'required',
                'active'=>'boolean|bail|required',
                'category'=>'required',
                'discount_type'=>'numeric|bail|required',
                'discount_amount'=>'bail|required_unless:discount_type,0|numeric',
                'discount_from_date'=>'bail|required_unless:discount_type,0|date|after_or_equal:today',
                'discount_end_date'=>'bail|required_unless:discount_type,0|date|after_or_equal:discount_from_date',
                'product_price' => 'bail|required|numeric|max:1000000' 
            ],
            [
                'required'=>':attribute không được trống ',
                'unique'=>':attribute đã tồn tại',
                'boolean'=>':attribute phải là kiểu boolean',
                'required_unless'=>':attribute không được trống khi có :other ',
                'numeric'=>':attribute phải là số',
                'discount_end_date.after_or_equal'=>':attribute phải sau :date',
                'discount_from_date.after_or_equal'=>':attribute phải từ ngày hôm nay trở đi ',
                'date'=>':attribute không đúng định dạng ngày tháng năm',
                'max'=> ':attribute tối đa là :max'
            ],
            [
                'product_name'=>'Tên sản phẩm',
                'product_slug'=>'Đường dẫn sản phẩm',
                'description'=>'Mô tả',
                'Active'=>'Active',
                'category'=>'Danh mục',
                'category'=>'Danh mục',
                'discount_type'=>'Giảm giá',
                'discount_amount'=>'Số tiền giảm giá',
                'discount_from_date'=>'Ngày bắt đầu giảm giá',
                'discount_end_date'=>'Ngày kết thúc giảm giá',
                'product_price' => 'Giá sản phẩm'
            ]
        );
        
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
       
        if($request->hasFile('image')){
             // validate image    
            for ($i=0; $i <count($request->image); $i++) { 
                $image=['image'=>$request->image[$i]];
                $validatedImage= Validator::make($image,
                    [
                        'image'=>'bail|sometimes|nullable|image'
                    ],
                    [
                        'image.image'=>'File '.$request->image[$i]->getClientOriginalName().' phải có dạng jpg,jpeg,png,svg,gif'
                    ]
                );
                if($validatedImage->fails()) return response()->json(['error'=>$validatedImage->getMessageBag()]);

            }
           // if validate array image success, start upload multi file
            $image_json=[];
            for ($i=0; $i <count($request->image) ; $i++) { 
                $image=$request->image[$i];
                $newNameImg=$request->product_slug.'_'.$i.'_'.date('Y_m_d').'.'.$image->getClientOriginalExtension();
                $image_json[]=$newNameImg;
                $image->move('images/product',$newNameImg);
                DB::table('tbl_image')->insert([ 'image_path'=>$newNameImg,'type'=>1]);
            }
        }

        // get current time update
        $update_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        //update product table
        if($request->hasFile('image')){
            DB::table('tbl_product')->where('product_id',$id)->update(array_merge($request->except(['category','color','size','_token','image','_method','discount_type','discount_amount','discount_from_date','discount_end_date']),['update_at'=>$update_at],['product_image'=>json_encode($image_json)],['user_edit'=>p_user()['user_id']]));
        }else{
            DB::table('tbl_product')->where('product_id',$id)->update(array_merge($request->except(['category','color','size','_token','_method','discount_type','discount_amount','discount_from_date','discount_end_date']),['update_at'=>$update_at],['user_edit'=>p_user()['user_id']]));
        }
        // insert new discount if user add new discount
        if($request->discount_type!=='0'){
            //process date
            $end_date=$request->discount_end_date;  
            $from_date=$request->discount_from_date;
            DB::table('tbl_product_discount')->insert(['product_id'=>$id,'discount_type'=>$request->discount_type,'discount_amount'=>$request->discount_amount,'discount_end_date'=>$end_date,'discount_from_date'=>$from_date]);
        }
        //update product category
        DB::table('tbl_category_product')->where('product_id',$id)->delete();
        if(is_array($request->category)){
            $category=explode(',',$request->category[0]);
            for ($i=0; $i < count($category); $i++) { 
                DB::table('tbl_category_product')->insert(['category_id'=>$category[$i],'product_id'=>$id]);
            }
        }
        // update product size
        // DB::table('tbl_product_size')->where('product_id',$id)->delete();
        // for ($i=0; $i <count($request->size) ; $i++) { 
        //     DB::table('tbl_product_size')->insert(['product_id'=>$id,'size_id'=>$request->size[$i]]);
        // }
        //update product color
        // DB::table('tbl_product_color')->where('product_id',$id)->delete();
        // for ($i=0; $i <count($request->color) ; $i++) { 
        //     DB::table('tbl_product_color')->insert(['product_id'=>$id,'color_id'=>$request->color[$i]]);
        // }
        return response()->json(['success'=>'Update product success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!p_author('delete','tbl_product')){
            return view('error.403');
        }
        // xóa sản phẩm thì cũng xóa size và color của sản phẩm trong các bản quan hệ
        $check_isset_store=DB::table('tbl_store_product')->where('product_id',$id)->first();
        if(!empty($check_isset_store)){
            return redirect()->back()->withErrors(['isset'=>'Sản phẩm đã nhập hàngs']);
        }
        try{
            $img_name=DB::table('tbl_product')->where('product_id',$id)->first(['product_image']);
            $img_name= json_decode($img_name->product_image,true);
           for ($i=0; $i <count($img_name) ; $i++) { 
                unlink(public_path('images/product/'.$img_name[$i]));
           }
            DB::table('tbl_product')->where('product_id',$id)->delete();
            return redirect()->back()->with('success','success');
        }catch(\Exception $e){
            return redirect()->back()->withErrors(['isset'=>'Sản phẩm đã nhập hàngs']);
        }
    }
    
    public function get_category_tree($data,$parent_id=0,$level=0){
        $category_tree=[];
        foreach ($data as $key => $value) {
           if($value->category_parent_id== $parent_id){
                $value->level=$level;
                array_push($category_tree,$value);
                unset($data[$key]);
                $child= $this->get_category_tree($data,$value->category_id,$level+1);
                $category_tree=array_merge($category_tree,$child);
           }
        }
        return $category_tree;
    }
    public function get_category_product_detail($id){
        //get category product
        $list_id_category=DB::table('tbl_category')->select(['tbl_category.category_id'])->join('tbl_category_product','tbl_category.category_id','=','tbl_category_product.category_id')->where('tbl_category_product.product_id',$id)->get()->toArray();
        $category=[];
        for ($i=0; $i <count($list_id_category) ; $i++) { 
            $category[]=$list_id_category[$i]->category_id;
        }
        return response()->json($category);
    }

    public function get_size_color_api($id){
        $size=DB::table('tbl_product_size')->join('tbl_size','tbl_size.size_id','=','tbl_product_size.size_id')->where('tbl_product_size.product_id',$id)->get(['tbl_size.size_id','tbl_size.size']);
        $color=DB::table('tbl_product_color')->join('tbl_color','tbl_color.color_id','=','tbl_product_color.color_id')->where('tbl_product_color.product_id',$id)->get(['tbl_color.color_id','tbl_color.color']);
         $data=['color'=>$color,'size'=>$size];
        return response()->json(['data'=>$data]);
    }
    public function get_price_size_color_api($id){
        $today=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        $size=DB::table('tbl_product_size')->join('tbl_size','tbl_size.size_id','=','tbl_product_size.size_id')->where('tbl_product_size.product_id',$id)->get(['tbl_size.size_id','tbl_size.size']);
        $color=DB::table('tbl_product_color')->join('tbl_color','tbl_color.color_id','=','tbl_product_color.color_id')->where('tbl_product_color.product_id',$id)->get(['tbl_color.color_id','tbl_color.color']);
        $price=DB::table('tbl_product')->where('product_id',$id)->first(['product_price']);
        
        $discount=DB::table('tbl_product_discount')
        ->where([
            ['product_id','=',$id],
            ['discount_from_date','<=',$today],
            ['discount_end_date','>=',$today]
        ])->orderByDesc('discount_id')->first(['discount_type','discount_amount']);
        if(!empty($discount)){
            if($discount->discount_type==1){
                $price->product_price=$price->product_price-$discount->discount_amount;
            }else{
                $price->product_price-=$price->product_price*$discount->discount_amount/100;
            }
        }
        $data=['color'=>$color,'size'=>$size,'price'=>$price];
        return response()->json(['data'=>$data]);
    }
    public function detail($id,Request $request){
        if(!p_author('view','tbl_product')){
            return view('error.403');
        }
        $check_isset=DB::table('tbl_product')->where('product_id',$id)->first();
        if(empty($check_isset)) return redirect()->back();
        try {
            // list store 
            $list_store=DB::table('tbl_store')->get();
            $product=DB::table('tbl_product')->where('product_id',$id)->first();
            $product->product_image=json_decode($product->product_image);
            // list color and size
            $list_color=DB::table('tbl_color')->join('tbl_product_color','tbl_product_color.color_id','=','tbl_color.color_id')
            ->where('tbl_product_color.product_id',$product->product_id)->get(['tbl_color.color','tbl_color.color_id'])->toArray();
            $list_size=DB::table('tbl_size')->join('tbl_product_size','tbl_product_size.size_id','=','tbl_size.size_id')
            ->where('tbl_product_size.product_id',$product->product_id)->get(['tbl_size.size','tbl_size.size_id'])->toArray();
            $product->list_color=$list_color;
            $product->list_size=$list_size;

            // detail discount
            $list_discount=DB::table('tbl_product_discount')->where([
                ['product_id','=',$id],
                ['discount_type','>',0]
            ])->get(['discount_type','discount_amount','discount_from_date','discount_end_date']);
            //discount  using
            $now=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
            $discount_using=DB::table('tbl_product_discount')->where([
                ['product_id','=',$id],
                ['discount_from_date','<',$now],
                ['discount_end_date','>',$now]
            ])->orderByDesc('discount_id')->first();
            // detail amount
                // $detail_amount=DB::table('tbl_product')
                // ->join('tbl_product_size','tbl_product_size.product_id','=','tbl_product.product_id')
                // ->join('tbl_size','tbl_size.size_id','=','tbl_product_size.size_id')
                // ->join('tbl_product_color','tbl_product_color.product_id','=','tbl_product.product_id')
                // ->join('tbl_color','tbl_color.color_id','=','tbl_product_color.color_id')
                // ->join('tbl_store_product',function($join){
                //     $join->on('tbl_store_product.product_id','=','tbl_product.product_id')
                //          ->on('tbl_store_product.product_size','=','tbl_size.size_id')
                //          ->on('tbl_store_product.product_color','=','tbl_color.color_id');
                // })
                // ->select(
                //     [   
                //         'tbl_product.product_name as product_name',
                //         'tbl_color.color as color',
                //         'tbl_size.size as size',
                //         'tbl_store_product.product_amount as product_amount',
                //         'tbl_size.size_id as size_id',
                //         'tbl_color.color_id as color_id'
                //     ]
                // )
                // ->where( 'tbl_product.product_id','=',$product->product_id)->paginate(2);
                
                // $data_detail_amount=[];
                // $lenght=count($detail_amount);
                // for ($i=0; $i <$lenght ; $i++) {
                //     if(!isset($detail_amount[$i])) continue;
                //     $data_detail_amount[$i]=$detail_amount[$i];
                //     unset($detail_amount[$i]);
                //     for ($j=0; $j <$lenght ; $j++) {
                //         if(!isset($detail_amount[$j])) continue;
                //         if($data_detail_amount[$i]->color_id == $detail_amount[$j]->color_id && $data_detail_amount[$i]->size_id == $detail_amount[$j]->size_id){
                //             $data_detail_amount[$i]->product_amount+=$detail_amount[$j]->product_amount;
                //             unset($detail_amount[$j]);
                //         }
                //    }
                // }

            // detail_amount
            $param_amount_where=[['product_id',$product->product_id]];
            if($request->store!==null && $request->store!=='0'){
                $param_amount_where[]=['tbl_store_product.store_id','=',$request->store];
            }
            $list_size_color_amount_product=DB::table('tbl_store_product')
            ->join('tbl_size','tbl_size.size_id','=','tbl_store_product.product_size')
            ->join('tbl_color','tbl_color.color_id','=','tbl_store_product.product_color')
            ->where($param_amount_where)
            ->select(['product_color','product_size','tbl_size.size as size','tbl_color.color as color','tbl_size.size_id','tbl_color.color_id'])
            ->distinct(['product_color','product_size'])->paginate(20);
           
            foreach ($list_size_color_amount_product as $products => $product_amount) {
                $sql='select SUM(product_amount) as total from tbl_store_product where product_size=? AND product_color=? AND product_id=?';
                $param=[$product_amount->size_id,$product_amount->color_id,$product->product_id];
                if($request->store!==null && $request->store!=='0'){
                    $sql.=' AND store_id=?';
                    array_push($param,$request->store);
                }
                $data_detail_amount=DB::select($sql,$param);
                // $data_detailamount=[ [total=>59]];
                $list_size_color_amount_product[$products]->product_amount=$data_detail_amount[0]->total;
              
            }
            
            // list categroy for product
            $list_category=DB::table('tbl_category')->join('tbl_category_product','tbl_category_product.category_id','=','tbl_category.category_id')
            ->where('tbl_category_product.product_id',$product->product_id)->get();
        
            return view('admin.product.detail',compact('product','list_discount','discount_using','list_size_color_amount_product','list_store','list_category'));

        } catch (\Throwable $th) {
           return redirect()->back();
        }
    }

    public function delete_color_product($product_id,$color_id){
        $data=DB::table('tbl_store_product')->where([
            ['product_id','=',$product_id],
            ['product_color','=',$color_id],
        ])->get(['product_id'])->toArray();
        if(count($data)>0) return response()->json(['error'=>['amount'=>'Tồn tại dữ liệu r']]);
        $list_color_product=DB::table('tbl_product_color')->where('product_id',$product_id)->get(['product_id'])->toArray();
        if(count($list_color_product) <=1) return response()->json(['error'=>['notnull'=>'Color của sản phẩm phải tồn tại']]);
        DB::table('tbl_product_color')->where(
            [
                ['product_id','=',$product_id],
                ['color_id','=',$color_id],
            ]
        )->delete();
        return response()->json(['success'=>'success']);
    }
    public function delete_size_product($product_id,$size_id){
        $data=DB::table('tbl_store_product')->where([
            ['product_id','=',$product_id],
            ['product_size','=',$size_id],
        ])->get(['product_id'])->toArray();
        if(count($data)>0) return response()->json(['error'=>['amount'=>'Tồn tại dữ liệu r']]);
        $list_size_product=DB::table('tbl_product_size')->where('product_id',$product_id)->get(['product_id'])->toArray();

        if(count($list_size_product)<=1) return response()->json(['error'=>['notnull'=>'Size của sản phẩm phải tồn tại']]);
        DB::table('tbl_product_size')->where(
            [
                ['product_id','=',$product_id],
                ['size_id','=',$size_id],
            ]
        )->delete();
        return response()->json(['success'=>'success']);
    }

    public function delete_category_product($product_id,$cate_id){
        $list_color_product=DB::table('tbl_category_product')->where('product_id',$product_id)->get(['product_id'])->toArray();
        if(count($list_color_product) <=1) return response()->json(['error'=>['notnull'=>'Color của sản phẩm phải tồn tại']]);
        DB::table('tbl_category_product')->where(
            [
                ['product_id','=',$product_id],
                ['category_id','=',$cate_id],
            ]
        )->delete();
        return response()->json(['success'=>'success']);
    }
    public function add_new_color_product_form($product_id){
        if(!p_author('edit','tbl_product')){
            die('Bạn đéo đủ quyền truy cập');
        }
        $product_isset=DB::table('tbl_product')->where('product_id',$product_id)->get(['product_id'])->toArray();
        if(count($product_isset)!==1) return redirect()->back();
        // get color old for product
        $list_color_old=DB::table('tbl_product_color')->join('tbl_color','tbl_color.color_id','=','tbl_product_color.color_id')->where('tbl_product_color.product_id',$product_id)->get(['tbl_product_color.color_id'])->toArray();
        $list_id_color_old=[];
        foreach ($list_color_old as $colors => $color) {
            $list_id_color_old[]=$color->color_id;
        }
        // get list color to add 
        $list_color=DB::table('tbl_color')->whereNotIn('color_id',$list_id_color_old)->get();
        // redirect if list color to add = null
        if(count($list_color)<1){
           return redirect()->back()->withErrors(['redirect'=>'Sản phẩm đã có đủ hết tất cả màu']);
        }
        return view('admin.product.addcolor',compact('list_color'));
    }
    public function add_new_color_product($product_id,Request $request){
      
        $validated=Validator::make($request->all(),
            [
                'color'=>'required'
            ],
            [
                'required'=> 'Chưa chọn :attribute cần thêm'
            ],
            [
                'color'=>'màu'
            ]
        );
        if($validated->fails()) return redirect()->back()->withErrors($validated->errors());
        foreach ($request->color as $colors => $color) {
            DB::table('tbl_product_color')->insert([
                    'product_id'=>$product_id,
                    'color_id'=>$color
            ]);
        }
        return redirect('admin/product/'.$product_id.'/detail');
    }

    public function add_new_size_product_form($product_id){
        if(!p_author('edit','tbl_product')){
            return view('error.403');
        }
        $product_isset=DB::table('tbl_product')->where('product_id',$product_id)->get(['product_id'])->toArray();
        if(count($product_isset)!==1) return redirect()->back();
        // get size old for product
        $list_size_old=DB::table('tbl_product_size')->join('tbl_size','tbl_size.size_id','=','tbl_product_size.size_id')->where('tbl_product_size.product_id',$product_id)->get(['tbl_product_size.size_id'])->toArray();
        $list_id_size_old=[];
        foreach ($list_size_old as $sizes => $size) {
            $list_id_size_old[]=$size->size_id;
        }
        // get list size to add 
        $list_size=DB::table('tbl_size')->whereNotIn('size_id',$list_id_size_old)->get();
        // redirect if list color to add = null
        if(count($list_size)<1){
           return redirect()->back()->withErrors(['redirect'=>'Sản phẩm đã có đủ hết tất cả kích cỡ']);
        }
        return view('admin.product.addsize',compact('list_size'));
    }
    public function add_new_size_product($product_id,Request $request){
      
        $validated=Validator::make($request->all(),
            [
                'size'=>'required'
            ],
            [
                'required'=> 'Chưa chọn :attribute cần thêm'
            ],
            [
                'size'=>'kích cỡ'
            ]
        );
        if($validated->fails()) return redirect()->back()->withErrors($validated->errors());
        foreach ($request->size as $sizes => $size) {
            DB::table('tbl_product_size')->insert([
                    'product_id'=>$product_id,
                    'size_id'=>$size
            ]);
        }
        return redirect('admin/product/'.$product_id.'/detail');
    }
    public function active_api(Request $request){
        try {
            $check_product=DB::table('tbl_product')->where('product_id',$request->id)->first();
            if(empty($check_product)) return response()->json(['error'=>'error']);
            if($check_product->active==1){
                $active=0;
            }else{
                $active=1;
            }
            DB::table('tbl_product')->where('product_id',$request->id)->update(['active'=>$active]);
            return response()->json(['success'=>$active]);
        } catch (\Throwable $th) {
            return response()->json(['error'=>'error']);
        }
    }
}
