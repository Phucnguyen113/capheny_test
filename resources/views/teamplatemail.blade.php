<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng {{$order_id}}</title>
</head>
<body style="max-width:1400px;margin:0 auto">
    <h2 class="card-title" style="font-size:36px">
        Xác nhận đơn hàng #{{$order_id}} {{$create_at}}
    </h2>
    <h2>Thông tin giao hàng</h2>
    <div style="margin-bottom:20px">
        {{$order_name}} <br>
        {{$address}} {{$ward}} {{$district}} {{$province}} <br>
        Tel: {{$order_phone}}
    </div>
    <div>
        <b>Phí vận chuyển :</b> 30,000 đ
    </div>
    <div style="background-color:#f0f2f0;padding-top:10px;padding-bottom:10px;padding-left:10px;padding-right:10px;font-weight:700">Thông tin đơn hàng</div>
    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $total=0;
            @endphp
            @foreach($list_product as $products => $product)
                @php 
                    $total+=$product->product_amount*$product->product_price
                @endphp
                <tr>
                    <td>{{$product->product_name}}-{{$product->size}}- <div style="display:inline-block;width:15px;height:15px;background-color:#{{$product->color}}"></div>
                        <img src="{{ $message->embed(public_path(). '/images/product/'.$product->image) }}" alt="" style="width:50px;height:50px;">
                    </td>
                    <td>{{number_format($product->product_price)}}</td>
                    <td>{{$product->product_amount}}</td>
                    <td>{{number_format($product->product_amount*$product->product_price)}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;">
                    <span>Thành tiền : <b>{{number_format($total)}} đ</b></span><br>
                    <span>Chi phí vận chuyển : <b>30,000 đ</b></span><br>
                    <span>Tổng : <b>{{number_format($total+30000)}} đ</b></span><br>
                </td>
            </tr>
        </tfoot>
    </table>
    <div style="text-align: center;">
            <a href="{{url('product')}}/{{$order_id}}" style="display:block;background-color:#4b8c6c;font-weight:700;font-size:13px;color:#fff;text-align:center;padding-top:10px;padding-bottom:10px;text-decoration:none"> Xem chi tiết đơn hàng</a>
    </div>
</body>
</html>