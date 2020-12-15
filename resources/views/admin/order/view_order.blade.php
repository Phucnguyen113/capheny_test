<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<style> 
/* * {margin: 0; padding: 0} */
    body {
        background-color: #f0f0f0;
    }
    .view-container {
        width: 70%;
        height: auto;
        background-color: white;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
   
    .info-order h3{
        margin-top: 30px;
        font-size: 40px;
        color: rgb(3, 58, 1);
        text-shadow: 2px 1px darkgray;
    }
    .info-order li{
        margin: 10px;
        font-size: 18px;
    }
    .back {
        float: left;
        margin: 10px 0px 20px 10px;
    }
    
    .hr {
        margin-top: 20px;
    }
</style>
<body>
    <div class="view-container">
        <table class="table table-bordered">
            
            <div class="main-heading">
                <h1 class="card-title" style="text-align:center">THÔNG TIN ĐƠN HÀNG</h1>
            </div>
            <thead class="thead-light">
              <tr>
                <th scope="col">STT</th>
                <th scope="col">Hình ảnh</th>
                <th scope="col">Tên sản phẩm</th>
                <th scope="col">Đơn giá</th>
                <th scope="col">Số lượng</th>
                
                <th scope="col">Thành tiền</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order_detail as $detail_items => $detail_item)
                <tr>
                    <th scope="row">1</th>
                    <td><img src="{{asset('images/product')}}/{{$detail_item->product_image}}" width="50px" height="50px"></td>
                    <td>{{$detail_item->product_name}} <br>Size : {{$detail_item->size}} <br>Màu: <div style="display:inline-block;background-color: #{{$detail_item->color}};width:15px;height:15px;"></div></td>
                    <td>{{number_format($detail_item->product_price)}} VNĐ</td>
                    <td>{{number_format($detail_item->product_amount)}}</td>
                    
                    <td>{{number_format($detail_item->product_price* $detail_item->product_amount)}} VNĐ</td>
                </tr>
              @endforeach
                <tr>
                    <th colspan="5">Phí vận chuyển</th>
                    <td>30.000 VNĐ</td>
                </tr>
              <tr>
                  <th colspan="5">Tổng</th>
                  
                  
                  <td>{{number_format($total_price+30000)}} VNĐ</td>
              </tr>
            </tbody>
          </table>
        <hr class="hr">
        <div class="info-order">
            <ul>
            <h3> Thông tin giao hàng </h3>
            <li>Tên khách hàng: {{$order->order_name}}</li>
            <li>Địa chỉ: {{$order->order_address}},{{$order->_prefix_ward}} {{$order->ward_}},{{$order->_prefix}} : {{$order->district_}},{{$order->province_}}</li>
            <li>Số điện thoại: 0987257590</li>
            </ul>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>