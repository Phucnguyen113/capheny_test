@extends('layouts.admin')
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
<script src="{{asset('p_js/order.add.addproduct.js')}}"></script>
@endsection
@section('body')
    <a href="{{url()->previous()}}" class="btn btn-warning  mb-2" style="color:white">Quay lại</a>
    <div class="card">
        <div class="card-body">
            <div class="card-title" style="text-align:center;font-size:36px">Chỉnh sửa đơn hàng</div>
            <form action="" onsubmit="return add_order()" method="post">
                @csrf 
               <div class="form-row">
                   <div class="card-title">
                       Thông tin người mua
                   </div>
                   <div class="col-md-12 form-group">
                        <label for="user_id">Người dùng <span style="color:Red"> *</span></label>
                        <select name="user_id" id="user_id" class="form-control" style="width:100%">
                            <option value="0">Chọn người dùng</option>
                            @foreach($list_user as $users => $user)
                                <option @if($user->user_id==$order->user_id) selected @endif value="{{$user->user_id}}">{{$user->user_first_name}} {{$user->user_last_name}}</option>
                            @endforeach
                        </select>
                        <script>
                                $('#user_id').select2()
                                $('#user_id').change(function(){
                                    if($(this).val()!=='0'){
                                            $.ajax({
                                            type: "post",
                                            url: "{{url('api/infouser')}}/"+$(this).val(),
                                            data: {},
                                            dataType: "json",
                                            success: function (response) {
                                                if(!$.isEmptyObject(response.error)){
                                                    console.log(response.error);
                                                }else{
                                                    console.log(response.user);
                                                    $('#order_name').val(response.user.user_first_name+" "+response.user.user_last_name);
                                                    $('#order_email').val(response.user.user_email);
                                                    $('#order_phone').val(response.user.user_phone);
                                                    $('#district').html('<option value="0">Chọn quận/huyện</option>');
                                                    $('#ward').html('<option value="0">Chọn khu vực</option>');
                                                    var check_district=true;
                                                    $.each(response.list_district,function(index,item){
                                                        var symple="";
                                                        if(item.id==response.user.district && check_district){
                                                            symple="selected" 
                                                            check_district=false
                                                        } 
                                                        var html='<option '+symple+' value="'+item.id+'">'+item._name+'</option>'
                                                        $('#district').append(html)
                                                    })
                                                    var check_ward=true;
                                                    $.each(response.list_ward,function(index,item){
                                                        var symple="";
                                                        if(item.id==response.user.ward && check_ward){
                                                            symple="selected"
                                                            check_ward=false
                                                        } 
                                                        var html='<option '+symple+' value="'+item.id+'">'+item._name+'</option>'
                                                        $('#ward').append(html)
                                                    })
                                                    $('#province').val(response.user.province);
                                                }
                                            }
                                        });
                                    }
                                
                                })
                        </script>
                        <div id="user_id_error" style="color:red" class="p_error"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="order_email">Email <span style="color:red"> *</span></label>
                        <input type="text" value="{{$order->order_email}}" name="order_email" id="order_email" class="form-control">
                        <div id="order_email_error" style="color:red" class="p_error"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="order_phone">Điện thoại <span style="color:red"> *</span></label>
                        <input type="text" value="{{$order->order_phone}}" name="order_phone" id="order_phone" class="form-control">
                        <div id="order_phone_error" style="color:Red" class="p_error"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="order_name">Tên <span style="color:Red"> *</span></label>
                        <input type="text" value="{{$order->order_name}}" name="order_name" id="order_name" class="form-control">
                        <div id="order_name_error" style="color:red" class="p_error"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="province">Thành phố/Tỉnh <span style="color:Red"> *</span></label>
                        <select name="province" id="province" class="form-control">
                            <option value="0">Chọn thành phố/tỉnh</option>
                            @foreach($list_province as $provinces => $province)
                            <option @if($province->id== $order->province) selected @endif value="{{$province->id}}">{{$province->_name}}</option>
                            @endforeach
                        </select>
                         <!-- script get district -->
                         <script>
                            $('#province').change(function(){
                                if($(this).val()==0){
                                    $('#district').html('<option value="0">Chọn quận, huyện</option>');
                                    $('#ward').html('<option value="0">Chọn khu vực</option>');
                                }else{
                                    $.ajax({
                                    type: "post",
                                    url: "{{url('api/district/get')}}/"+$(this).val(),
                                    data: {},
                                    dataType: "json",
                                    success: function (response) {
                                        if(!$.isEmptyObject(response.error)){
                                            $('#district').html('<option value="0">Chọn quận, huyện</option>');
                                            $('#ward').html('<option value="0">Chọn khu vực</option>');
                                        }else{
                                            $('#district').html('<option value="0">Chọn quận, huyện</option>');
                                            $('#ward').html('<option value="0">Chọn khu vực</option>');
                                            $.each(response.data,function(index,item){
                                                var district='<option value="'+item.id+'">'+item._name+'</option>'
                                                $('#district').append(district);
                                            })
                                        }
                                    }
                                });
                                }
                            })
                        </script>
                        <div id="province_error" style="color:red" class="p_error"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="district">Quận/huyện <span style="color:Red"> *</span></label>
                        <select name="district" id="district" class="form-control">
                            <option value="0">Chọn quận/huyện</option>
                            @foreach($list_district as $districts => $district)
                                <option @if($district->id==$order->district) selected @endif value="{{$district->id}}">{{$district->_name}}</option>
                            @endforeach
                            
                        </select>
                         <!-- script get ward -->
                         <script>
                            $('#district').change(function(){
                                if($(this).val()==0){
                                    $('#ward').html('<option value="0">Chọn khu vực </option>');
                                }else{
                                    $.ajax({
                                    type: "post",
                                    url: "{{url('api/ward/get')}}/"+$(this).val(),
                                    data: {},
                                    dataType: "json",
                                    success: function (response) {
                                        if(!$.isEmptyObject(response.error)){

                                        }else{
                                            $('#ward').html('<option value="0">Chọn khu vực </option>');
                                            $.each(response.data,function(index,item){
                                                var ward='<option value="'+item.id+'">'+item._name+'</option>'
                                                $('#ward').append(ward);
                                            })
                                        }
                                    }
                                });
                                }
                                
                            })
                        </script>
                        <div id="district_error" style="color:Red" class="p_error"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="ward">Khu vực <span style="color:Red"> *</span></label>
                        <select name="ward" id="ward" class="form-control">
                            <option value="0">Chọn khu vực</option>
                            @foreach($list_ward as $wards => $ward)
                                <option @if($ward->id==$order->ward) selected @endif value="{{$ward->id}}">{{$ward->_name}}</option>
                            @endforeach
                        </select>
                        <div id="ward_error" style="color:red" class="p_error"></div>
                    </div>
               </div>
               <div class="form-row form-group">
                   <div class="col-md-12">
                       <label for="">Địa chỉ <span style="color:Red"> *</span></label>
                       <input type="text" value="{{$order->order_address}}" name="order_address" id="order_address" class="form-control">
                       <div id="order_address_error" class="p_error" style="color:Red"></div>
                   </div>
               </div>
                <!-- end user --> 
                <div class="card-title">
                    Sản phẩm
                </div>
                <div class="form-row ">
                    <div class="col-md-6 form-group">
                            <label for="product_id">Sản phẩm <span style="color:Red"> *</span></label>
                            <select name="product_id" id="product_id" class="form-control" style="width:100%">
                                <option value="0">Chọn sản phẩm</option>
                                @foreach($list_product as $products => $product)
                                    <option value="{{$product->product_id}}">{{$product->product_name}}</option>
                                @endforeach
                            </select>
                            <div id="product_id_form_error" style="color:red" class="p_error"></div>
                            <!-- script process form product -->
                            <script>
                                var p_price=0; // giá product
                                $('#product_id').select2()
                                $('#product_id').change(function(index,item){
                                    if($(this).val()!=='0'){
                                        $.ajax({
                                            type: "post",
                                            url: "{{url('api/product/get_size_color_price')}}/"+$(this).val(),
                                            data: {},
                                            dataType: "json",
                                            success: function (response) {
                                                console.log(response);
                                                $('#color').html('<option value="0">Chọn màu</option>')
                                                $.each(response.data.color,function(index,item){
                                                    $('#color').append(`<option value="${item.color_id}" data-color="${item.color}"></option>`)
                                                })
                                                $('#size').html(' <option value="0">Chọn kích cỡ</option>')
                                                $.each(response.data.size,function(index,item){
                                                    $('#size').append(`<option value="${item.size_id}">${item.size}</option>`)
                                                })
                                                $('#price').val(response.data.price.product_price)
                                                p_price=response.data.price.product_price;
                                            }
                                        });
                                    }
                                })
                                
                            </script>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="price">Giá <span style="color:Red"> *</span></label>
                        <input type="text" name="price" id="price" class="form-control" readonly="true">
                    </div>
                    <div class="col-md-4 form-group">
                            <label for="color">Màu </label>
                            <select name="color" id="color" class="form-control" style="width:100%;height:38px">
                                <option value="0">Chọn màu</option>
                            </select>
                            <script>
                                function formatState (state) {
                                    if (!state.id) {
                                            return state.text;
                                        }
                                        var color=$(state.element).attr('data-color');
                                        var $state = $(
                                        '<span><div style="width:15px;height:15px;background-color:#'+color+';display:inline-block"></div></span>'
                                        );
                                        return $state;
                                    };

                                    $("#color").select2({
                                        templateResult: formatState,
                                        templateSelection :formatState,
                                        placeholder: "Chọn màu",

                                    });
                            </script>
                            <div id="color_form_error" style="color:red" class="p_error"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="size">Kích cỡ</label>
                        <select name="size" id="size" class="form-control" style="width:100%">
                            <option value="0">Chọn kích cỡ</option>
                        </select>
                        <script>
                            $('#size').select2();
                        </script>
                        <div id="size_form_error" style="color:red" class="p_error"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="product_amount">Số lượng</label>
                        <input type="number" min="1" max="100" name="product_amount" placeholder="Số lượng" id="product_amount" class="form-control" style="border:0.01rem solid gray">
                        <div id="product_amount_form_error" class="p_error" style="color:Red"></div>
                    </div>
                    <div class="col-md-12 form-group">
                        <button type="button" style="float: right;" onclick="add_product()" class="btn btn-success">Thêm sản phẩm</button>
                        <!-- script add order -->
                        <script>
                                function edit_order(){
                                    var formdata= new FormData();
                                    var _token=$("input[name='_token']").val();
                                    formdata.append('_token',_token)
                                    formdata.append('user_id',$('#user_id').val());
                                    formdata.append('order_email',$('#order_email').val())
                                    formdata.append('order_phone',$('#order_phone').val());
                                    formdata.append('order_name',$('#order_name').val());
                                    formdata.append('province',$("#province").val())
                                    formdata.append('district',$('#district').val())
                                    formdata.append('order_address',$('#order_address').val())
                                    formdata.append('ward',$('#ward').val());
                                    formdata.append('order_status',$('#order_status').val())
                                    $.each($('#table_tbody_product .tbody_tr'),function(index,item){
                                        var product=[];
                                        product['product_id']=$(this).find('input[name="product_id"]').val();
                                        product['color']=$(this).find('input[name="color"]').val();
                                        product['size']=$(this).find('input[name="size"]').val();
                                        product['product_amount']=$(this).find('input[name="product_amount"]').val();
                                        product['price']=$(this).find('input[name="price"]').val();
                                        formdata.append('product_id[]',product['product_id'])
                                        formdata.append('product_color[]',product['color'])
                                        formdata.append('product_size[]',product['size']);
                                        formdata.append('product_amount[]',product['product_amount']);
                                        formdata.append('product_price[]',product['price']);
                                    })

                                    $.ajax({
                                        type: "post",
                                        url: "{{url('admin/order')}}/{{$order->order_id}}/edit",
                                        data: formdata,
                                        dataType: "json",
                                        contentType:false,
                                        processData:false,
                                        success: function (response) {
                                            console.log(response);
                                            if(!$.isEmptyObject(response.error)){
                                                $('.p_error').html('');
                                                $.each(response.error,function(index,item){
                                                    if(index=='product_id'){
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Chưa chọn sản phẩm',
                                                            text: 'Bạn cần thêm ít nhất 1 sản phẩm',
                                                        })
                                                    }else{
                                                        if(index=='size_500' || index=='color_500' || index=='product_500'){
                                                            Swal.fire({
                                                            icon: 'error',
                                                            title: 'Lỗi! Xin thử lại',
                                                            text: 'Hãy thử tải lại trang',
                                                            })
                                                        }else if(index=='amount'){
                                                            Swal.fire({
                                                                icon: 'error',
                                                                title: 'Lỗi! Xin thử lại',
                                                                text: item,
                                                            })
                                                        }else  $(`#${index}_error`).html(item);
                                                    }
                                                
                                                })
                                            }else{
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Chỉnh sửa đơn hàng thành công',
                                                    text: 'Bạn vừa chỉnh sửa 1 đơn hàng',
                                                }).then(()=>{
                                                    window.location.href="{{url('admin/order')}}"
                                                })
                                            }
                                        }
                                    });
                                }
                            // remove product
                            function remove_product(key){
                                
                                if($('#table_tbody_product .tbody_tr').length>1){
                                    $(`#remove_product_${key}`).parent().parent().remove();
                                }else{
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Xóa thất bại',
                                        text: 'Đơn hàng cần ít nhất 1 sản phẩm',
                                    })
                                }
                              
                            }
                        </script>
                    </div>
                </div>
                       
                <div class="form-row from-group">
                    <div class="card-title">Danh sách sản phẩm</div>
                    <div class="table-responsive">
                        <table class="table" id="table_product">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Màu</th>
                                    <th>Kích cỡ</th>
                                    <th>Số lượng</th>
                                    <th>Giá</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="table_tbody_product">
                                @foreach($order_detail as $details => $detail)
                                    <tr class="tbody_tr">
                                        <td>{{$detail->product_name}} <input type="hidden" name="product_id" value="{{$detail->product_id}}"> </td>
                                        <td><div style="width:15px;height:15px;background-color:#{{$detail->color}}"></div> <input type="hidden" name="color" value="{{$detail->product_color_id}}"></td>
                                        <td>{{$detail->size}} <input type="hidden" name="size" value="{{$detail->product_size_id}}"></td>
                                        <td class="product_amount_td"><span id="amount_text_{{$detail->product_id}}_{{$detail->product_color_id}}_{{$detail->product_size_id}}">{{$detail->product_amount}}</span> <button type="button" onclick="up_amount('{{$detail->product_id}}_{{$detail->product_color_id}}_{{$detail->product_size_id}}')" class="btn btn-success">+</button> <button type="button" onclick="down_amount('{{$detail->product_id}}_{{$detail->product_color_id}}_{{$detail->product_size_id}}')" class="btn btn-dark">-</button><input type="hidden" id="product_{{$detail->product_id}}_{{$detail->product_color_id}}_{{$detail->product_size_id}}" name="product_amount" value="{{$detail->product_amount}}"></td>
                                        <td>{{$detail->product_price}} VND <input type="hidden" name="price" value="{{$detail->product_price}}"></td>
                                        <td><button id="remove_product_{{$detail->product_id}}_{{$detail->product_color_id}}_{{$detail->product_size_id}}" onclick="remove_product('{{$detail->product_id}}_{{$detail->product_color_id}}_{{$detail->product_size_id}}')" type="button" class="btn btn-danger remove_product"><i class="fa fa-trash-alt"></i></button></td>
                                     
                                    @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-4">
                        <label for="order_status">Trạng thái <span style="color:red"> *</span></label>
                        <select name="order_status" id="order_status" class="form-control">
                            <option @if($order->order_status==0) selected @endif value="0">Chờ xử lý</option>
                            <option @if($order->order_status==1) selected @endif value="1">Đã tiếp nhận</option>
                            <option @if($order->order_status==2) selected @endif value="2">Đang giao hàng</option>
                            <option @if($order->order_status==3) selected @endif value="3">Đã nhận hàng</option>
                            <option @if($order->order_status==4) selected @endif value="4">Trả hàng về</option>
                        </select>
                        <div id="order_status_error" class="p-error" style="color:Red"></div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-12" >
                        <button  type="button"  onclick="edit_order()" class="btn btn-primary">Sửa</button>
                    </div>
                </div>
            </form>
@endsection