<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    protected $users;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        Mail::send('teamplatemail', [
            'order_id' => $this->data['id'],
            'order_name' => $this->data['name'],
            'order_phone'=>$this->data['phone'],
            'list_product'=>$this->data['list_product'],
            'create_at'=>$this->data['create_at'],
            'province'=>$this->data['province'],
            'district'=>$this->data['district'],
            'ward'=>$this->data['ward'],
            'address'=>$this->data['address'],
            'order_token'=>$this->data['token']
            
        ], function ($msg) {
            $msg->to($this->data['email'], 'Xác nhận đơn hàng')->subject('Xác nhận đơn hàng');
        });
            
        
    }
}
