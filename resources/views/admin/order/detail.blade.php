@extends('layouts.admin')
@section('body')
    <div class="col md-2 mb-2" style="padding:unset">
        <a href="{{url()->previous()}}" class="btn btn-warning" style="color:white;display:inline-block!important"> Quay lại</a>
        <div class="card-title" style="text-align:center;font-size:36px">Chi tiết đơn hàng</div>
    </div>
    <div class="card main-card ">
        <div class="card-body">
            <div class="card-title">Chi tiết đơn hàng</div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Trạng thái</th>
                            <th style="text-align: right;">Tổng tiền</th>
                            <th style="text-align: right;">Tổng sản phẩm</th>
                            <th>Ngày tạo</th>
                            <th>Ngày sửa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> 
                                @if($order->order_status==0) 
                                        Chờ xử lý
                                    @elseif($order->order_status==1)
                                        Đã tiếp nhận
                                    @elseif($order->order_status==2)
                                        Đang giao hàng
                                    @elseif($order->order_status==3)
                                        Đã nhận hàng
                                    @else 
                                        Trả hàng về
                                @endif
                            </td>
                            <td style="text-align: right;">{{number_format($order->total_price)}} VNĐ</td>
                            <td style="text-align: right;">{{$order->total_product}}</td>
                            <td>{{$order->create_at}}</td>
                            <td>{{$order->update_at}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card main-card mt-3">
       
        <div class="card-body">
            <div class="card-title">Thông tin người mua</div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Thành phố/Tỉnh</th>
                            <th>Quận/Huyện</th>
                            <th>Khu vực</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$user->order_name}}</td>
                            <td>
                                
                                @if($user->user_id!==0)
                                    <a href="{{url('admin/user')}}/{{$user->user_id}}/detail">{{$user->order_email}}</a>
                                @else
                                    {{$user->order_email}}
                                @endif
                            </td>
                            <td>{{$user->order_phone}}</td>
                            <td>{{$user->province}}</td>
                            <td>{{$user->district}}</td>
                            <td>{{$user->ward}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="card main-card mt-3">
        <div class="card-body">
            <div class="card-title">Danh sách sản phẩm</div>
            <a href="{{url('admin/order')}}/{{$user->order_id}}/edit" class="btn btn-success mb-2">Thêm sản phẩm</a>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Màu</th>
                            <th>Kích thước</th>
                            <th style="text-align: right;">Số lượng</th>
                            <th style="text-align: right;">Giá</th>
                            <th style="text-align: right;">Tổng tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_product_detail as $products => $product)
                            <tr id="product_{{$product->product_id}}_{{$product->size_id}}_{{$product->color_id}}">
                                <td><a href="{{url('admin/product')}}/{{$product->product_id}}/detail">{{$product->product_name}}</td>
                                <td><div style="width:15px;height:15px;background-color:#{{$product->color}}"></div></td>
                                <td>{{$product->size}}</td>
                                <td style="text-align: right;">{{$product->product_amount}}</td>
                                <td style="text-align: right;">
                                    @if($product->discount)
                                        {{number_format($product->product_price)}} VNĐ (Khuyến mãi)
                                    @else
                                        {{number_format($product->product_price)}}VNĐ
                                    @endif
                                </td>
                                <td style="text-align: right;">{{number_format($product->product_amount* $product->product_price)}} VNĐ</td>
                                <td>
                                    <button type="button" class="btn btn-danger" onclick="delete_product_detail({{$user->order_id}},{{$product->product_id}},{{$product->size_id}},{{$product->color_id}})"><div class="fa fa-trash-alt"></div></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>

            </div>
        </div>
    </div>
  

@endsection
@section('css')
    <style>
        .select2-selection__rendered {
            line-height: 35px !important;
        }
        .select2-container .select2-selection--single {
            height: 38px !important;
        }
        .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
@endsection
@section('js')
<script>
    function delete_product_detail(order_id,product_id,size_id,color_id){
        $.ajax({
            type: "post",
            url: "{{url('api/delete/product/order')}}/"+order_id+"/"+product_id+"/"+size_id+"/"+color_id,
            data: {},
            dataType: "json",
            success: function (response) {
                if(!$.isEmptyObject(response.error)){
                    console.log(response);
                    $.each(response.error,function(index,item){
                        if(index=='amount'){
                            Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Phải có ít nhất 1 sản phẩm trong đơn hàng',
                            })
                        }else if(index=='error_sv'){
                            Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Xin hãy thử tải lại trang',
                            })
                        }
                    })
                }else{
                    Swal.fire({
                            icon: 'success',
                            title: 'Xóa thành công',
                            text: 'Sản phẩm đã xóa',
                    }).then(()=>{
                        $(`#product_${product_id}_${size_id}_${color_id}`).remove();
                    })
                    
                }
            }
        });
    }
</script>
@endsection