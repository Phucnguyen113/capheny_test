@extends('layouts.admin')
@section('js')
<script src="{{asset('p_js/view_setting.js')}}"></script>
@endsection
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
            <!-- ui_setting -->
            <div class="">
                @if(p_author('add','tbl_order'))
                    <a href="{{url('admin/order/create')}}" class="btn btn-success mb-2">Thêm đơn hàng </a>
                @endif
                <div style="float:right" >
                    <p style="text-align:right">
                        <a class="" data-toggle="collapse" href="#view" role="button" aria-expanded="false" aria-controls="view">
                            <i class="fas fa-cog"></i> Tùy chọn hiển thị
                        </a>
                    </p>
                    <div class="collapse col-md-12" id="view">
                        <div class="form-row" style="float:right">
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','order_email'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="order_email" id="order_email" value="order_email" class="form-control-checkbox view-setting" >
                                <label for="order_email">Email</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','phone'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="phonee" id="phonee" value="phone" class="form-control-checkbox view-setting" >
                                <label for="phonee">Điện thoại</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','province'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="provincee" id="provincee" value="province" class="form-control-checkbox view-setting" >
                                <label for="provincee">Thành phố/Tỉnh</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','district'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="districtt" id="districtt" value="district" class="form-control-checkbox view-setting" >
                                <label for="districtt">Quận/Huyện</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','ward'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="wardd" id="wardd" value="ward" class="form-control-checkbox view-setting" >
                                <label for="wardd">Khu vực</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','address'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="addresss" id="adresss" value="address" class="form-control-checkbox view-setting" >
                                <label for="adresss">Địa chỉ</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','status'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="statuss" id="statuss" value="status" class="form-control-checkbox view-setting" >
                                <label for="statuss">Trạng thái</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','create_at'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="create_att" id="create_att" value="create_at" class="form-control-checkbox view-setting" >
                                <label for="create_att">Ngày tạo</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','update_at'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="update_att" id="update_att" value="update_at" class="form-control-checkbox view-setting" >
                                <label for="update_att">Ngày sửa</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','detail'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="detaill" id="detaill" value="detail" class="form-control-checkbox view-setting" >
                                <label for="detaill">Chi tiết</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('order','action'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'order')" name="action" id="action" value="action" class="form-control-checkbox view-setting" >
                                <label for="action">Hành động</label>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div> 
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên người mua</th>
                            <th class="p_setting order_email" @if(!p_ui_setting('order','order_email'))  style="display: none;" @endif >Email</th>
                            <th class="p_setting phone" @if(!p_ui_setting('order','phone'))  style="display: none;" @endif >Điện thoại</th>
                            <th class="p_setting province" @if(!p_ui_setting('order','province'))  style="display: none;" @endif >Thành phố/Tỉnh</th>
                            <th class="p_setting district" @if(!p_ui_setting('order','district'))  style="display: none;" @endif >Quận/Huyện</th>
                            <th class="p_setting ward" @if(!p_ui_setting('order','ward'))  style="display: none;" @endif >Khu vực</th>
                            <th class="p_setting address" @if(!p_ui_setting('order','address'))  style="display: none;" @endif >Địa chỉ</th>
                            <th class="p_setting status" @if(!p_ui_setting('order','status'))  style="display: none;" @endif >Trạng thái</th>
                            <th class="p_setting create_at" @if(!p_ui_setting('order','create_at'))  style="display: none;" @endif > Ngày tạo</th>
                            <th class="p_setting update_at" @if(!p_ui_setting('order','update_at'))  style="display: none;" @endif >Ngày Sửa</th>
                            <th class="p_setting detail" @if(!p_ui_setting('order','detail'))  style="display: none;" @endif >Chi tiết</th>
                            <th class="p_setting action" @if(!p_ui_setting('order','action'))  style="display: none;" @endif >Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_order as $orders => $order)
                            <tr>
                                <td>{{$order->order_name}}</td>
                                <td class="p_setting order_email" @if(!p_ui_setting('order','order_email'))  style="display: none;" @endif >{{$order->order_email}}</td>
                                <td class="p_setting phone" @if(!p_ui_setting('order','phone'))  style="display: none;" @endif >{{$order->order_phone}}</td>
                                <td class="p_setting province" @if(!p_ui_setting('order','province'))  style="display: none;" @endif >{{$order->province_}}</td>
                                <td class="p_setting district" @if(!p_ui_setting('order','district'))  style="display: none;" @endif>{{$order->district_}}</td>
                                <td class="p_setting ward" @if(!p_ui_setting('order','ward'))  style="display: none;" @endif>{{$order->ward_}}</td>
                                <td class="p_setting address" @if(!p_ui_setting('order','address'))  style="display: none;" @endif>{{$order->order_address}}</td>
                                <td class="p_setting status" @if(!p_ui_setting('order','status'))  style="display: none;" @endif>
                                   
                                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                       
                                            <button id="btnGroupDrop{{$order->order_id}}" type="button" class="btn btn-secondary " >
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
                                            </button>
                                            
                                            
                                    </div>
                                </div>
                                </td>
                                <td class="p_setting create_at" @if(!p_ui_setting('order','create_at'))  style="display: none;" @endif>{{$order->create_at}}</td>
                                <td class="p_setting update_at" @if(!p_ui_setting('order','update_at'))  style="display: none;" @endif>{{$order->update_at}}</td>
                                <td class="p_setting detail" @if(!p_ui_setting('order','detail'))  style="display: none;" @endif><a href="{{url('admin/order/')}}/{{$order->order_id}}/detail" class="btn btn-info">Chi tiết</a></td>
                                <td class="p_setting action" @if(!p_ui_setting('order','action'))  style="display: none;" @endif>
                                    @if(p_author('edit','tbl_order'))
                                        <a href="{{url('admin/order')}}/{{$order->order_id}}/edit" class="btn btn-primary"><div class="fa fa-edit"></div></a>
                                    @endif
                                    @if(p_author('delete','tbl_order'))
                                    <form action="{{url('admin/order')}}/{{$order->order_id}}/delete" method="POST" id="form_{{$order->order_id}}" style="display:inline-block" >
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
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                            <th>Tên người mua</th>
                            <th class="p_setting order_email" @if(!p_ui_setting('order','order_email'))  style="display: none;" @endif >Email</th>
                            <th class="p_setting phone" @if(!p_ui_setting('order','phone'))  style="display: none;" @endif >Điện thoại</th>
                            <th class="p_setting province" @if(!p_ui_setting('order','province'))  style="display: none;" @endif >Thành phố/Tỉnh</th>
                            <th class="p_setting district" @if(!p_ui_setting('order','district'))  style="display: none;" @endif >Quận/Huyện</th>
                            <th class="p_setting ward" @if(!p_ui_setting('order','ward'))  style="display: none;" @endif >Khu vực</th>
                            <th class="p_setting address" @if(!p_ui_setting('order','address'))  style="display: none;" @endif >Địa chỉ</th>
                            <th class="p_setting status" @if(!p_ui_setting('order','status'))  style="display: none;" @endif >Trạng thái</th>
                            <th class="p_setting create_at" @if(!p_ui_setting('order','create_at'))  style="display: none;" @endif > Ngày tạo</th>
                            <th class="p_setting update_at" @if(!p_ui_setting('order','update_at'))  style="display: none;" @endif >Ngày Sửa</th>
                            <th class="p_setting detail" @if(!p_ui_setting('order','detail'))  style="display: none;" @endif >Chi tiết</th>
                            <th class="p_setting action" @if(!p_ui_setting('order','action'))  style="display: none;" @endif >Thao tác</th>
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