
@extends('layouts.admin')
@section('js')
<script src="{{asset('p_js/view_setting.js')}}"></script>
@endsection
@section('body')
<!-- Button trigger modal -->

@if($errors->has('error'))
<script>
    Swal.fire({
        icon:'error',
        title:'Xóa thất bại!',
        text:'Cửa hàng đã nhập sản phẩm'
    })
</script>
@endif
@if(\Session::has('success'))
<script>
    Swal.fire({
        icon:'success',
        title:'Xóa thành công!',
        text:'Bạn vừa xóa 1 cửa hàng'
    })
</script>
@endif
<div class="main-card card">
    <div class="card-body">
        <div id="filter_p" style="margin-bottom:15px;">
            <div class="collapse" id="collapseExample">
                <form class="form-group" action="" onsubmit="return validate_filter()" method="GET" enctype="multipart/form-data"> 
                    <input type="hidden" name="search" value="true">
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="store_name" >Tên cửa hàng</label>
                                <input type="text"  value="{{(request()->store_name)?request()->store_name:''}}" name="store_name" id="store_name" class="form-control">
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
        <!-- end search -->
        <h5 class="card-title" style="text-align:center;font-size:36px">Danh sách cửa hàng</h5>
       
        <div class="">
            @if(p_author('add','tbl_store'))
                <a href="{{url('admin/store/create')}}" style="color:white" class="btn btn-success mb-2">Thêm mới cửa hàng</a>
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
                            <input type="checkbox" @if(p_ui_setting('store','province'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'store')" name="provincee" id="provincee" value="province" class="form-control-checkbox view-setting" >
                            <label for="provincee">Thành phố/Tỉnh</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('store','district'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'store')" name="districtt" id="districtt" value="district" class="form-control-checkbox view-setting" >
                            <label for="districtt">Quận/Huyện</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('store','ward'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'store')" name="wardd" id="wardd" value="ward" class="form-control-checkbox view-setting" >
                            <label for="wardd">Khu vực</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('store','address'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'store')" name="addresss" id="addresss" value="address" class="form-control-checkbox view-setting" >
                            <label for="addresss">Địa chỉ</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('store','create_at'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'store')" name="create_att" id="create_att" value="create_at" class="form-control-checkbox view-setting" >
                            <label for="create_att">Ngày tạo</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('store','update_at'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'store')" name="update_att" id="update_att" value="update_at" class="form-control-checkbox view-setting" >
                            <label for="update_att">Ngày sửa</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('store','detail'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'store')" name="detailt" id="detailt" value="detail" class="form-control-checkbox view-setting" >
                            <label for="detailt">Chi tiết</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('store','action'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'store')" name="action" id="action" value="action" class="form-control-checkbox view-setting" >
                            <label for="action">Thao tác</label>
                        </div>
                    </div>
                    
                </div>
            </div>
            </div> 
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Tên cửa hàng</th>
                    <th scope="col" class="p_setting province" @if(!p_ui_setting('store','province'))  style=" display:none" @endif>Thành phố/ tỉnh</th>
                    <th scope="col" class="p_setting district" @if(!p_ui_setting('store','district'))  style=" display:none" @endif>Quận/ huyện</th>
                    <th scope="col" class="p_setting ward" @if(!p_ui_setting('store','ward'))  style=" display:none" @endif>Khu vực</th>
                    <th scope="col" class="p_setting address" @if(!p_ui_setting('store','address'))  style=" display:none" @endif>Địa chỉ</th>
                    <th scope="col" class="p_setting create_at" @if(!p_ui_setting('store','create_at'))  style=" display:none" @endif>Ngày tạo</th>
                    <th scope="col" class="p_setting update_at" @if(!p_ui_setting('store','update_at'))  style=" display:none" @endif>Ngày sửa</th>
                    <th scope="col" class="p_setting detail" @if(!p_ui_setting('store','detail'))  style=" display:none" @endif>Chi tiết</th>
                    <th scope="col" class="p_setting action" @if(!p_ui_setting('store','action'))  style=" display:none" @endif>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($list_store as $key =>$value)
                    <tr>
                   
                    <th >{{$value->store_name}}</th>
                    <th class="p_setting province" @if(!p_ui_setting('store','province'))  style=" display:none" @endif>{{$value->province}}</th>
                    <th class="p_setting district" @if(!p_ui_setting('store','district'))  style=" display:none" @endif>{{$value->district}}</th>
                    <th class="p_setting ward" @if(!p_ui_setting('store','ward'))  style=" display:none" @endif>{{$value->ward}}</th>
                    <th class="p_setting address" @if(!p_ui_setting('store','address'))  style=" display:none" @endif>{{$value->store_address}}</th>
                    <td class="p_setting create_at" @if(!p_ui_setting('store','create_at'))  style=" display:none" @endif> {{$value->create_at}}</td>
                    <td  class="p_setting update_at" @if(!p_ui_setting('store','update_at'))  style=" display:none" @endif>{{$value->update_at}}</td>
                    <th class="p_setting detail" @if(!p_ui_setting('store','detail'))  style=" display:none" @endif><a href="{{url('admin/store')}}/{{$value->store_id}}/detail" class="btn btn-info">Chi tiết</a></th>
                    <td class="p_setting action" @if(!p_ui_setting('store','action'))  style=" display:none" @endif>
                        @if(p_author('edit','tbl_store'))
                            <a href="{{url('admin/store')}}/{{$value->store_id}}/edit" class="btn btn-primary"><i class="fa fa-edit"></i> </a>
                        @endif
                        @if(p_author('delete','tbl_store'))
                            <form onsubmit="" style=" display:inline-block" action="{{url('admin/store/delete')}}/{{$value->store_id}}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-danger"> <i class="fa fa-trash-alt"></i></button>
                            </form>
                        @endif
                    </td>
                    </tr>
                @endforeach
                <tr>
                    <th scope="col">Tên cửa hàng</th>
                    <th scope="col" class="p_setting province" @if(!p_ui_setting('store','province'))  style=" display:none" @endif>Thành phố/ tỉnh</th>
                    <th scope="col" class="p_setting district" @if(!p_ui_setting('store','district'))  style=" display:none" @endif>Quận/ huyện</th>
                    <th scope="col" class="p_setting ward" @if(!p_ui_setting('store','ward'))  style=" display:none" @endif>Khu vực</th>
                    <th scope="col" class="p_setting address" @if(!p_ui_setting('store','address'))  style=" display:none" @endif>Địa chỉ</th>
                    <th scope="col" class="p_setting create_at" @if(!p_ui_setting('store','create_at'))  style=" display:none" @endif>Ngày tạo</th>
                    <th scope="col" class="p_setting update_at" @if(!p_ui_setting('store','update_at'))  style=" display:none" @endif>Ngày sửa</th>
                    <th scope="col" class="p_setting detail" @if(!p_ui_setting('store','detail'))  style=" display:none" @endif>Chi tiết</th>
                    <th scope="col" class="p_setting action" @if(!p_ui_setting('store','action'))  style=" display:none" @endif>Action</th>
                </tr>
            </tbody>
            
        </table>
        <div class="row" style="display:flex;justify-content:center;align-items:center">
            {{$list_store->links()}} 
        </div>
          
    </div>
</div>

@endsection
