<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class pusherOrder implements ShouldBroadcast
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
        $order=DB::table('tbl_order')->join('tbl_province','tbl_province.id','=','tbl_order.province')
        ->join('tbl_district','tbl_district.id','=','tbl_order.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_order.ward')->select(
            [
                'tbl_province._name as province_',
                'tbl_district._name as district_',
                'tbl_ward._name as ward_',
                'tbl_order.*',
                
            ],
        )->distinct(['tbl_order.order_id'])
        ->where('order_id',$message)->first();
        $html=' <tr>';
        $html.='<td>'.$order->order_name.'</td>';
        $html.='<td class="p_setting order_email"';
        if(!p_ui_setting('order','order_email')) $html.='  style="display: none;"';
        $html.='>'.$order->order_email.'</td>';
        $html.='<td class="p_setting phone"';
        if(!p_ui_setting('order','phone')) $html.=' style="display: none;"';
        $html.=' >'.$order->order_phone.'</td>';
        $html.='<td class="p_setting province"';
        if(!p_ui_setting('order','province')) $html.=' style="display: none;"';
        $html.=' >'.$order->province_.'</td>';
        $html.='<td class="p_setting district"';
        if(!p_ui_setting('order','district')) $html.='  style="display: none;"';
        $html.='>'.$order->district_.'</td>';
        $html.='<td class="p_setting ward"';
        if(!p_ui_setting('order','ward')) $html.='  style="display: none;"';
        $html.='>'.$order->ward_.'</td>';
        $html.=' <td class="p_setting address"';
        if(!p_ui_setting('order','address')) $html.='  style="display: none;"';
        $html.='>'.$order->order_address.'</td>';
        $html.='<td class="p_setting status"';
        if(!p_ui_setting('order','status'))  $html.='style="display: none;"';
        $html.='>';
        $html.=' <div class="btn-group" role="group" aria-label="Button group with nested dropdown">';
        $html.='<button id="btnGroupDrop{{$order->order_id}}" type="button" class="btn btn-secondary " >';
        if($order->order_status==0){
            $html.='Chờ xử lý';
        }else if($order->order_status==1){
            $html.='Đã tiếp nhận';
        }else if($order->order_status==2){
            $html.='Đang giao hàng';
        }else if($order->order_status==3){
            $html.='Đã nhận hàng';
        }else{
            $html.='Trả hàng về';
        }
        $html.='</button>';
        $html.='</div>';
        $html.='</div>';
        $html.='</td>';
        $html.='<td class="p_setting create_at"';
        if(!p_ui_setting('order','create_at')) $html.='  style="display: none;"';
        $html.='>'.$order->create_at.'</td>';
        $html.='<td class="p_setting update_at"';
        if(!p_ui_setting('order','update_at')) $html.=' style="display: none;"';
        $html.='>'.$order->update_at.'</td>';
        $html.='<td class="p_setting detail"';
        if(!p_ui_setting('order','detail')) $html.=' style="display: none;"';
        $html.='><a href="'.url('admin/order/').'/'.$order->order_id.'/detail" class="btn btn-info">Chi tiết</a></td>';
        $html.='<td class="p_setting action"';
        if(!p_ui_setting('order','action')) $html.=' style="display: none;"';
        $html.='>';
        if(p_author('edit','tbl_order')){
            $html.='<a href="'.url('admin/order').'/'.$order->order_id.'/edit" class="btn btn-primary"><div class="fa fa-edit"></div></a>';
        }
        if(p_author('delete','tbl_order')){
            $html.='<form action="'.url('admin/order').'/'.$order->order_id.'/delete" method="POST" id="form_'.$order->order_id.'" style="display:inline-block" >';
            $html.=csrf_field();
            $html.=p_method('PUT');
            $html.='<button class="btn btn-danger" onclick="check_delete('.$order->order_id.')"><div class="fa fa-trash-alt"></div></button>';
            $html.=' </form>';
        }
        $html.='</td> </tr>';
        $this->message=$html;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['order-add'];
    }
}
