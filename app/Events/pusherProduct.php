<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class pusherProduct implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $message;
    public function __construct($message)
    {   
        $product=DB::table('tbl_product')->where('product_id',$message)->first();
        $html='';
        $currentDate=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
            $discount=DB::table('tbl_product_discount')->where([
                ['product_id',$product->product_id],
                ['discount_from_date','<=',$currentDate],
                ['discount_end_date','>=',$currentDate],
            ])->orderByDesc('discount_id')->first();
            // check product has discount in today 
            if(!empty($discount)){
                // product has discount in today
                $product->discount='Có';
            }else{
                // product hasn't discount in today
                $product->discount='Không';
                
            }

            //get color and size product
            $list_color=DB::table('tbl_color')->select(['tbl_color.color'])
            ->join('tbl_product_color','tbl_color.color_id','=','tbl_product_color.color_id')
            ->where('tbl_product_color.product_id',$product->product_id)->get()->toArray();
            if(!empty($list_color)){
                foreach ($list_color as $colors => $color) {
                    $product->colors[]=$color->color;
                }
            }else{
                $product->colors=[];
            }

            $list_size=DB::table('tbl_size')->select(['size'])->join('tbl_product_size','tbl_product_size.size_id','=','tbl_size.size_id')->where('tbl_product_size.product_id',$product->product_id)->get()->toArray();
            if(!empty($list_size)){
                foreach ($list_size as $sizes => $size) {
                    $product->sizes[]=$size->size;
                }
            }else{
                $product->sizes=[];
            }
            //get list category
            $list_category=DB::table('tbl_category_product')->join('tbl_category','tbl_category.category_id','=','tbl_category_product.category_id')
            ->where('tbl_category_product.product_id',$product->product_id)->get(['tbl_category.category_id','tbl_category.category_name'])->toArray();
            if(!empty($list_category)){
                foreach ($list_category as $categories => $category) {
                    $product->category[]=$category;
                }
            }else{
                $product->category=[];
            }
            $html.='<tr>';
                        
            $html.='<td >'.$product->product_name.'</td>';
            $html.='<td class="p_setting description"';
            if(!p_ui_setting('product','description')) $html.='style="display: none;"' ;
            $html.='>'.Str::limit($product->description,50).'</td>';
            $html.='<td  class="p_setting list_cate"';
            if(!p_ui_setting('product','list_cate')) 'style="display: none;"';
            $html.='>';
                    
            $categoryText='';
            
            foreach($product->category as $categories => $category){  
                $categoryText.=$category->category_name.',';
            }
            if(p_author('edit','tbl_product')){
                $html.='<a href="url("admin/product")/'.$product->product_id.'/edit" class="href">'.Str::limit(rtrim($categoryText,','),30).   '</a>';
            
            }else{
                $html.=Str::limit(rtrim($categoryText,','),30);  
            }
                         
                   
            $html.='</td>';
            $html.='<td class="p_setting color"'; 
            if(!p_ui_setting('product','color')) $html.='style="display: none;"';
            $html.='>';

                    foreach ($product->colors as $colors => $color){
                        $html.='<div style="width:15px;height:15px;display:inline-block;background-color:#'.$color.'"></div>';
                    }
            $html.='</td>';
            $html.='<td class="p_setting size"'; 
            if(!p_ui_setting('product','size')) $html.='style="display: none;"';
            $html.='>';
            foreach ($product->sizes as $sizes => $size){
                $html.='<p style="display:inline-block;margin:0; margin-right:5px;">'.$size.'</p>';
            }        
            $html.='</td>';
            $html.='<td class="p_setting price"';
            if(!p_ui_setting('product','price')) $html.='style="display: none;"'; 
            $html.=' style="text-align:right">'.number_format($product->product_price).' VND</td>';
            $html.='<td class="p_setting discount"';
            if(!p_ui_setting('product','discount')) $html.='style="display: none;">'.$product->discount.'</td>';
                
            $html.='<td class="p_setting active"';
            if(!p_ui_setting('product','active')) $html.='style="display: none;"';
            $html.='>';
            if(p_author('active','tbl_product')){
                if($product->active==1)  $html.='<a href="#" class="p_product_active" data-id="'.$product->product_id.'"><i class="fa fa-check" style="color:green" aria-hidden="true"></i></a>';
                    
                else $html.='<a href="#" class="p_product_active" data-id="'.$product->product_id.'"><i class="fas fa-times" style="color:#b81f44"></i></a>';   
            }            
            else{
                if($product->active==1){
                    $html.='<i class="fa fa-check" style="color:green" aria-hidden="true"></i>';
                }else{
                    $html.='<i class="fas fa-times" style="color:#b81f44"></i>';
                }
            }
            $html.='</td>';
            $html.='<td class="p_setting detail"'; 
            if(!p_ui_setting('product','detail')) $html.='style="display: none;"';
            $html.='>';
            $html.='<a href="'.url('admin/product').'/'.$product->product_id.'/detail" class="btn btn-info">Chi tiết</a>';
                   
            $html.='</td>';
            $html.='<td class="p_setting action"';
            if(!p_ui_setting('product','action')) $html.='style="display: none;" ';
            $html.='>';
            if(p_author('edit','tbl_product'))  $html.='<a href="'.url("admin/product").'/'.$product->product_id.'/edit" class="btn btn-primary"><i class="fa fa-edit"></i></a>';
                       
                    
            if(p_author('delete','tbl_product')){
                $html.='<form action="'.url('admin/product').'/'.$product->product_id.'" style="display:inline-block;margin:0" method="post">';
                $html.=csrf_field();
                $html.=p_method('DELETE');
                $html.='<button type="submit" class="btn btn-danger"><i class="fa fa-trash-alt"></i></button> </form>';
               
            }
            $html.='</td>';
            $html.='</tr>';
        $this->message=$html;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['product-add'];
    }
}
