<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiProductController extends Controller
{
    public function list_product(Request $request){
        
        $list_product=DB::table('tbl_product')->select(['tbl_product.*'])->orderByDesc('product_id');
        $binding=[];
        $binding[]=['tbl_product.active','=',1];
        if($request->has('discount_type') && $request->discount_type!==null){
            $list_product->join('tbl_product_discount','tbl_product_discount.product_id','=','tbl_product.product_id');
            $binding[]=['tbl_product_discount.discount_type','=',$request->discount_type];
            $binding[]=['tbl_product_discount.discount_from_date','<=',Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()];
            $binding[]=['tbl_product_discount.discount_end_date','>=',Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()];
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
            $list_product=$list_product->whereIn('tbl_category_product.category_id',$request->category);
        }
       
        
        $list_product=$list_product->where($binding);
        
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
                $list_product[$key]->discount=true;
                if($discount->discount_type==1){
                    $list_product[$key]->price_discount=$value->product_price-$discount->discount_amount;   
                }elseif($discount->discount_type==2){
                    $list_product[$key]->price_discount=$value->product_price-($value->product_price*$discount->discount_amount/100); 
                }
            }else{
                $list_product[$key]->discount=false;
                $list_product[$key]->price_discount=$value->product_price;
            }

            //get color and size product
            $list_color=DB::table('tbl_color')->select(['tbl_color.color','tbl_color.color_id'])
            ->join('tbl_product_color','tbl_color.color_id','=','tbl_product_color.color_id')
            ->where('tbl_product_color.product_id',$value->product_id)->get()->toArray();
            if(!empty($list_color)){
                foreach ($list_color as $colors => $color) {
                    $list_product[$key]->colors[]=['color'=>$color->color,'color_id'=>$color->color_id];
                }
            }else{
                $list_product[$key]->colors=[];
            }

            $list_size=DB::table('tbl_size')->select(['tbl_size.size','tbl_size.size_id'])->join('tbl_product_size','tbl_product_size.size_id','=','tbl_size.size_id')->where('tbl_product_size.product_id',$value->product_id)->get()->toArray();
            if(!empty($list_size)){
                foreach ($list_size as $sizes => $size) {
                    $list_product[$key]->sizes[]=['size'=>$size->size,'size_id'=>$size->size_id];
                }
            }else{
                $list_product[$key]->sizes=[];
            }
            
             // process price filter
            if($request->has('price') && $request->price!==null ){
                if($request->price==0){
                    if($value->price_discount>200000){
                        unset($list_product[$key]);
                    }
                }else if($request->price==1){
                    if($value->price_discount<200000 || $value->price_discount>400000){
                        unset($list_product[$key]);
                    }
                }else if($request->price==2){
                    if($value->price_discount<400000 || $value->price_discount>600000){
                        unset($list_product[$key]);
                    }
                }
            }
        }
        return response()->json(['data'=>$list_product]);
    }

    public function detail(Request $request){
        $detail=DB::table('tbl_product')->where('product_id',$request->product_id)->first();
        if(empty($detail))return response()->json(['error'=>['Không tìm thấy sản phẩm']]);
        // get discount 
        $discount_collection=DB::table('tbl_product_discount')->where('product_id',$request->product_id)->orderByDesc('discount_id')->first();
        if(!empty($discount_collection)){
            
            if($discount_collection->discount_type==1){
                $final_price=$detail->product_price-$discount_collection->discount_amount;
            }else if($discount_collection->discount_type==2){
                $final_price=$detail->product_price-($detail->product_price*$discount_collection->discount_amount/100);
            }
            $detail->final_price=$final_price;
            $detail->discount=true;
        }else{
            // không có discount
            $detail->discount=false;
            $detail->final_price=$detail->product_price;
        }
        //get size
        $list_size=DB::table('tbl_product_size')
        ->join('tbl_size','tbl_size.size_id','=','tbl_product_size.size_id')
        ->where('tbl_product_size.product_id',$request->product_id)->get(['tbl_size.size_id','tbl_size.size'])->toArray();
        $detail->size=$list_size;
        //get color
        $list_color=DB::table('tbl_product_color')
        ->join('tbl_color','tbl_color.color_id','=','tbl_product_color.color_id')
        ->where('tbl_product_color.product_id',$request->product_id)->get(['tbl_color.color_id','tbl_color.color'])->toArray();
        $detail->color=$list_color;
        return response()->json(['data'=>$detail]);
    }
    public function list_product_new_api(Request $request){
        $list_product_new_collection=DB::table('tbl_product')->orderByDesc('product_id')->limit(6)->get();
        foreach ($list_product_new_collection as $produtcs => $product) {
            //get size
            $list_size=DB::table('tbl_product_size')
            ->join('tbl_size','tbl_size.size_id','=','tbl_product_size.size_id')
            ->where('tbl_product_size.product_id',$product->product_id)->get(['tbl_size.size_id','tbl_size.size'])->toArray();
            $list_product_new_collection[$produtcs]->size=$list_size;
            //get color
            $list_color=DB::table('tbl_product_color')
            ->join('tbl_color','tbl_color.color_id','=','tbl_product_color.color_id')
            ->where('tbl_product_color.product_id',$product->product_id)->get(['tbl_color.color_id','tbl_color.color'])->toArray();
            $list_product_new_collection[$produtcs]->color=$list_color;
            // get discount 
            $discount_collection=DB::table('tbl_product_discount')->where('product_id',$request->product_id)->orderByDesc('discount_id')->first();
            if(!empty($discount_collection)){
                
                if($discount_collection->discount_type==1){
                    $final_price=$product->product_price-$discount_collection->discount_amount;
                }else if($discount_collection->discount_type==2){
                    $final_price=$product->product_price-($product->product_price*$discount_collection->discount_amount/100);
                }
                $list_product_new_collection[$produtcs]->final_price=$final_price;
                $list_product_new_collection[$produtcs]->discount=true;
            }else{
                // không có discount
                $list_product_new_collection[$produtcs]->discount=false;
                $list_product_new_collection[$produtcs]->final_price=$product->product_price;
            }
        }
        return response()->json(['data'=>$list_product_new_collection]);
    }
}
