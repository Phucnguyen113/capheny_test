@extends('layouts.admin')
@section('body')
    <div class="card main-card">
        <div class="card-body">
            <div id="filter_p" style="margin-bottom:15px;">
                <div class="collapse" id="collapseExample">
                    <form class="form-group" action="" onsubmit="return validate_filter()" method="GET" enctype="multipart/form-data"> 
                        <input type="hidden" name="search" value="true">
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label for="order_name" >Tên cửa hàng</label>
                                    <input type="text"  value="{{(request()->order_name)?request()->order_name:''}}" name="order_name" id="store_name" class="form-control">
                                    <div id="category_name_error" style="color:red"></div>
                                </div> 
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="province">Thành phố/Tỉnh</label>
                                <select name="province" id="province" class="form-control">
                                    <option value="0">Chọn thành phố/tỉnh..</option>
                                    @foreach($list_province as $provinces => $province)
                                        <option @if(request()->province==$province->id) selected @endif value="{{$province->id}}">{{$province->_name}}</option>
                                    @endforeach
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
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="district">Quận/Huyện</label>
                                <select name="district" id="district" class="form-control">
                                    <option value="0">Chọn quận/huyện...</option>
                                    @foreach($list_district as $districts => $district)
                                        <option @if(request()->district==$district->id) selected @endif value="{{$district->id}}">{{$district->_name}}</option>
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
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="ward">Khu vực</label>
                                <select name="ward" id="ward" class="form-control">
                                    <option  value="0">Chọn khu vực...</option>
                                    @foreach($list_ward as $wards => $ward)
                                        <option @if(request()->ward==$ward->id) selected @endif value="{{$ward->id}}">{{$ward->_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="create_at_from" class="">Ngày tạo bắt đầu</label>
                                    <input name="create_at_from" value="@isset($_GET['create_at_from']) {{$_GET['create_at_from']}} @endisset" id="create_at_from" placeholder="Ngày sửa"  readonly='true' type="text" class="form-control">
                                    <div id="picker_time_create_at_from" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                    <div id="update_at_error" style="color:red"></div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="create_at_to" class="">Ngày tạo kết thúc</label>
                                    <input name="create_at_to" value="@isset($_GET['create_at_to']) {{$_GET['create_at_to']}} @endisset" id="create_at_to" placeholder="Ngày sửa" readonly='true'  type="text" class="form-control">
                                    <div id="picker_time_create_at_to" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                    <div id="update_at_error" style="color:red"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="update_at_from" class="">Ngày sửa bắt đầu</label>
                                    <input name="update_at_from" value="@isset($_GET['update_at_from']) {{$_GET['update_at_from']}} @endisset" id="update_at_from" placeholder="Ngày sửa" readonly='true' type="text" class="form-control">
                                    <div id="picker_time_update_at_from" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                    <div id="update_at_error" style="color:red"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="update_at_to" class="">Ngày sửa kết thúc</label>
                                    <input name="update_at_to" value="@isset($_GET['update_at_to']) {{$_GET['update_at_to']}} @endisset" id="update_at_to" placeholder="Ngày sửa" readonly='true' type="text" class="form-control">
                                    <div id="picker_time_update_at_to" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                    <div id="update_at_error" style="color:red"></div>
                                </div>
                            </div>
                            
                        </div>
                            <!-- datetime picker  -->
                            <script>
                                function InterfacePickertime(input=[]){
                                    for (let i = 0; i < input.length; i++) {
                                        $('#'+input[i].divId).slideUp();
                                        $('#'+input[i].inputId).focus(function(){
                                            $('#'+input[i].divId).slideDown();
                                        })
                                        $('#'+input[i].divId).datetimepicker({
                                            date: new Date(),
                                            startDate:null,
                                            endDate: null,
                                            viewMode: 'YMDHMS',
                                            onDateChange: function(){
                                                $('#'+input[i].inputId).val(this.getText());
                                            },
                                            onOk:function(){
                                                $('#'+input[i].divId).slideUp();
                                            }
                                        });
                                    }
                                }
                                var arrInputdatetimePicker=[
                                    { inputId:'create_at_from',divId:'picker_time_create_at_from'},
                                    { inputId:'create_at_to',divId:'picker_time_create_at_to'},
                                    { inputId:'update_at_from',divId:'picker_time_update_at_from'},
                                    { inputId:'update_at_to',divId:'picker_time_update_at_to'},
                                ];
                                InterfacePickertime(arrInputdatetimePicker);
                                
                                //select 2 
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

                                
                                
                            </script>
                        
                        <div class="form-row form-group">
                            <div class="col-md-12">
                                <button class="mt-1 btn btn-info">Tìm kiếm</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div style="display:flex;justify-content:center;align-items:center">
                    <a  data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Tìm kiếm
                    </a>  
                </div>
            
            </div>
             <!-- list order -->
            <div class="card-title" style="text-align: center;font-size:36px">Danh sách đơn hàng</div>
            <a href="{{url('admin/order/create')}}" class="btn btn-success mb-2">Thêm đơn hàng </a>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên người mua</th>
                            
                            <th>Điện thoại</th>
                            <th>Thành phố/Tỉnh</th>
                            <th>Quận/Huyện</th>
                            <th>Khu vực</th>
                            <th>Địa chỉ</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Ngày Sửa</th>
                            <th>Chi tiết</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_order as $orders => $order)
                            <tr>
                                <td>{{$order->order_name}}</td>
                               
                                <td>{{$order->order_phone}}</td>
                                <td>{{$order->province_}}</td>
                                <td>{{$order->district_}}</td>
                                <td>{{$order->ward_}}</td>
                                <td>{{$order->order_address}}</td>
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
                                <td>{{$order->create_at}}</td>
                                <td>{{$order->update_at}}</td>
                                <td><a href="{{url('admin/order/')}}/{{$order->order_id}}/detail" class="btn btn-info">Chi tiết</a></td>
                                <td>
                                    <a href="{{url('admin/order')}}/{{$order->order_id}}/edit" class="btn btn-primary"><div class="fa fa-edit"></div></a>
                                    <form action="{{url('admin/order')}}/{{$order->order_id}}/delete" method="POST" id="form_{{$order->order_id}}" >
                                        @csrf
                                        @method('PUT')
                                        <button class="btn btn-danger" onclick="check_delete('{{$order->order_id}}')"><div class="fa fa-trash-alt"></div></button>
                                    </form>
                                    <script>
                                        function check_delete(key){
                                           event.preventDefault();
                                            var form=$(`#form_${key}`);
                                            console.log(form);
                                            Swal.fire({
                                                title: 'Bạn có muốn xóa ?',
                                                text: "Bạn sẽ không thể khôi phục lại",
                                                type: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#3085d6',
                                                cancelButtonColor: '#d33',
                                                confirmButtonText: 'Tiếp tục xóa',
                                                cancelButtonText: 'Hủy'
                                            }).then((isConfirm)=>{
                                                if(isConfirm.isConfirmed){
                                                    form.submit()
                                                }
                                            })
                                            
                                        }
                                    </script>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                            <th>Tên người mua</th>
                            
                            <th>Điện thoại</th>
                            <th>Thành phố/Tỉnh</th>
                            <th>Quận/Huyện</th>
                            <th>Khu vực</th>
                            <th>Địa chỉ</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Ngày Sửa</th>
                            <th>Chi tiết</th>
                            <th>Thao tác</th>
                        </tr>
                    </tfoot>
                </table>
                <div style="display: flex;justify-content:center">
                    {!!$list_order->appends( request()->all() )->links()!!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
@endsection
@section('js')
@endsection