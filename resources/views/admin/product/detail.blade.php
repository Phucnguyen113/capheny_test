@extends('layouts.admin')
@section('body')
<a href="{{url()->previous()}}" class="btn btn-warning mb-2" style="color:white">Quay lại</a>
    <div class="card">
        <div class="card-body">
            <div class="card-title" style="text-align:center;font-size:36px;">Chi tiết sản phẩm {{$product->product_name}}</div>
            <div class="row mb-2">
                <div class="col-md-12">
               
                    <a href="{{url('admin/product')}}/{{$product->product_id}}/edit" class="btn btn-primary">Sửa sản phẩm</a>
                </div>
            </div>
            <!-- table detail product -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Tên </th>
                            <th>Đường dẫn</th>
                            <th>Giá</th>
                            <th>Mô tả</th>
                            <th>Ảnh</th>
                            <th>Kích hoạt</th>
                            <th>Ngày tạo</th>
                            <th>Ngày sửa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$product->product_id}}</td>
                            <td>{{$product->product_name}}</td>
                            <td>{{$product->product_slug}}</td>
                            <td style="text-align:right">{{number_format($product->product_price)}} VNĐ</td>
                            <td>{!!$product->description!!}</td>
                            <td>
                                @foreach ($product->product_image as $images => $image)
                                    <img style="width:50px;height:50px;" src="{{asset('images/product')}}/{{$image}}" alt="">
                                @endforeach
                            </td>
                            <td>
                                {{($product->active==1)?'Kích hoạt':'Không'}}
                            </td>
                            <td>{{$product->create_at}}</td>
                            <td>{{$product->update_at}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
   
    <!-- detail amount product -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="card-title" style="text-align:center;font-size:25px">Chi tiết số lượng sản phẩm</div>
            <div class="form-row form-group">
                <div class="col-md-2">
                    <form action="" id="form_amount" method="get">
                        <select name="store" id="store" class="form-control" style>
                            <option value="0">Tất cả cửa hàng</option>
                            @foreach($list_store as $stores => $store) 
                                <option @if(request()->store==$store->store_id) selected @endif value="{{$store->store_id}}">{{$store->store_name}}</option>
                            @endforeach
                        </select>
                    </form>
                    <script>
                            $('#store').change(function(){
                                $('#form_amount').submit();
                            })
                    </script>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Màu</th>
                            <th>Kích cỡ</th>
                            <th style="text-align:right">Số lượng</th>
                        </tr>
                    </thead>
                    <tbody id="body_amount">
                        @foreach($list_size_color_amount_product as $amounts => $amount)
                            <tr>
                                <td>{{$product->product_name}}</td>
                                <td> <div style="width:15px;height:15px;background-color:#{{$amount->color}};"></div></td>
                                <td> {{$amount->size}}</td>
                                <td style="text-align:right">{{$amount->product_amount}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="paginate_amount" style="display:flex;justify-content:center">
                {!! $list_size_color_amount_product->appends(request()->all())->links()!!}
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
             <!-- detail discount -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="card-title" style="font-size:25px;text-align:center">
                        Chi tiết giảm giá của sản phẩm
                    </div>
                    @if(count($list_discount)>0) 
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Loại giảm giá</th>
                                    <th>Số tiền </th>
                                    <th>Ngày bắt đầu </th>
                                    <th>Ngày kết thúc </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list_discount as $discounts =>$discount)
                                    <tr>
                                        <td>{{($discount->discount_type==1)?'Giảm tiền trực tiếp':'Giảm theo %'}}</td>
                                        <td>{{number_format($discount->discount_amount)}} {{($discount->discount_type==1)?' VNĐ':'%'}}</td>
                                        <td>{{$discount->discount_from_date}}</td>
                                        <td>{{$discount->discount_end_date}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        Hiện tại chưa có giảm giá
                    @endif
                </div>
            </div>
        </div>
          <!-- discount using -->                   
        <div class="col-md-6">
            <div class="card mt-3">
                <div class="card-body">
                    <div class="card-title" style="font-size:25px;text-align:center">
                        Giảm giá đang được áp dụng
                    </div>
                @if(!empty($discount_using)) 
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Loại giảm giá</th>
                                    <th>Số tiền </th>
                                    <th>Ngày bắt đầu </th>
                                    <th>Ngày kết thúc </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{($discount_using->discount_type==1)?'Giảm tiền trực tiếp':'Giảm theo %'}}</td>
                                    <td>{{number_format($discount_using->discount_amount)}} {{($discount_using->discount_type==1)?' VNĐ':'%'}}</td>
                                    <td>{{$discount_using->discount_from_date}}</td>
                                    <td>{{$discount_using->discount_end_date}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    Hiện tại chưa có giảm giá nào đang áp dụng
                @endif
                </div>
            </div>
        </div>
    </div>
   
   
    <div class="form-row">
        <div class="col-md-4">
            <div class="card mt-3">
                <div class="card-body">
                    <!-- table list color -->
                    <div class="card-title" style="font-size:25px;text-align:center">
                        Màu của sản phẩm
                    </div>
                    <a href="{{url('admin/product')}}/{{$product->product_id}}/addcolor" class="btn btn-success mb-2">Thêm màu mới</a>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Màu</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->list_color as $colors => $color)
                                    <tr>
                                        <td>{{$color->color_id}}</td>
                                        <td>
                                            <div style="width:15px;height:15px;background-color:#{{$color->color}}"></div>
                                        </td>
                                        @if(p_author('edit','tbl_product',false,true))
                                            <td>
                                                <form action="" method="post" onsubmit="return delete_color('{{$product->product_id}}','{{$color->color_id}}')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger"><div class="fa fa-trash-alt"></div></button>
                                                    <!-- delete color script -->
                                                    <script>
                                                        function delete_color(id_product,id_color){
                                                            $.ajax({
                                                                type: "post",
                                                                url: "{{url('api/product/color/delete')}}/"+id_product+'/'+id_color,
                                                                data: {},
                                                                dataType: "json",
                                                                success: function (response) {
                                                                    console.log(response);
                                                                    if($.isEmptyObject(response.error)){
                                                                        Swal.fire(
                                                                            'Xóa thành công',
                                                                            '',
                                                                            'success'
                                                                        ).then(()=>{
                                                                            window.location.reload();
                                                                        })
                                                                    }else{
                                                                    
                                                                        Swal.fire({
                                                                        icon: 'error',
                                                                        title: 'Xóa thất bại',
                                                                        text: 'Không thể xóa',
                                                                        footer: '<a href>Tại sao lại không thể xóa?</a>'
                                                                        })
                                                                    }
                                                                },
                                                                error: function(){
                                                                    console.log('error');
                                                                }
                                                            });
                                                            return false;
                                                        }
                                                    </script>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mt-3 ">
                <div class="card-body">
                    <!-- table list size --> 
                    <div class="card-title" style="font-size:25px;text-align:center">
                            Kích cỡ của sản phẩm
                    </div>
                    @if(p_author('edit','tbl_product',false,true))
                        <a href="{{url('admin/product')}}/{{$product->product_id}}/addsize" class="btn btn-success mb-2">Thêm kích cỡ mới</a>
                    @endif
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                <th>Id</th>
                                <th>Kích cỡ</th>
                                <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->list_size as $sizes => $size)
                                    <tr>
                                        <td>{{$size->size_id}}</td>
                                        <td>
                                            {{$size->size}}
                                        </td>
                                        @if(p_author('edit','tbl_product',false,true))
                                            <td>
                                                <form action="" method="post" onsubmit="return delete_size('{{$product->product_id}}','{{$size->size_id}}')">
                                                    <button type="submit" class="btn btn-danger">
                                                        <div class="fa fa-trash-alt"></div>
                                                    </button>
                                                    <!-- script delete size -->
                                                    <script>
                                                        function delete_size(id_product,id_size){
                                                            $.ajax({
                                                                type: "post",
                                                                url: "{{url('api/product/size/delete')}}/"+id_product+'/'+id_size,
                                                                data: {},
                                                                dataType: "json",
                                                                success: function (response) {
                                                                    console.log(response);
                                                                    if($.isEmptyObject(response.error)){
                                                                        Swal.fire(
                                                                            'Xóa thành công',
                                                                            '',
                                                                            'success'
                                                                        ).then(()=>{
                                                                            window.location.reload();
                                                                        })
                                                                    }else{
                                                                    
                                                                        Swal.fire({
                                                                        icon: 'error',
                                                                        title: 'Xóa thất bại',
                                                                        text: 'Không thể xóa',
                                                                        footer: '<a href>Tại sao lại không thể xóa?</a>'
                                                                        })
                                                                    }
                                                                },
                                                                error: function(){
                                                                    console.log('error');
                                                                }
                                                            });
                                                            return false;
                                                        }
                                                    </script>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mt-3">
                <div class="card-body">
                    <div class="card-title" style="font-size:25px;text-align:center">
                            Danh mục của sản phẩm
                    </div>
                    @if(p_author('edit','tbl_product',false,true))
                    <a href="{{url('admin/product')}}/{{$product->product_id}}/edit" class="btn btn-success mb-2">Thêm danh mục mới</a>
                    @endif
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tên danh mục</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list_category as $categories => $category)
                                    <tr>
                                        <td>{{$category->category_name}}</td>
                                        @if(p_author('delete','tbl_product',false,true))
                                            <td>
                                                <form action="" method="post" onsubmit="return delete_category('{{$product->product_id}}','{{$category->category_id}}')"">
                                                        <button  type="submit" class="btn btn-danger">
                                                            <div class="fa fa-trash-alt"></div>
                                                        </button>
                                                </form>
                                                
                                                <!-- script delete size -->
                                                <script>
                                                        function delete_category(id_product,id_cate){
                                                            $.ajax({
                                                                type: "post",
                                                                url: "{{url('api/product/category/delete')}}/"+id_product+'/'+id_cate,
                                                                data: {},
                                                                dataType: "json",
                                                                success: function (response) {
                                                                    console.log(response);
                                                                    if($.isEmptyObject(response.error)){
                                                                        Swal.fire(
                                                                            'Xóa thành công',
                                                                            '',
                                                                            'success'
                                                                        ).then(()=>{
                                                                            window.location.reload();
                                                                        })
                                                                    }else{
                                                                    
                                                                        Swal.fire({
                                                                        icon: 'error',
                                                                        title: 'Xóa thất bại',
                                                                        text: 'Không thể xóa',
                                                                        footer: '<a href>Tại sao lại không thể xóa?</a>'
                                                                        })
                                                                    }
                                                                },
                                                                error: function(){
                                                                    console.log('error');
                                                                }
                                                            });
                                                            return false;
                                                        }
                                                    </script>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
@endsection

