@extends('layouts.admin')
@section('js')
  <script src="{{asset('p_js/view_setting.js')}}"></script>
   
@endsection
@section('body')
@if($errors->has('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Xóa thất bại',
                text: 'Người dùng này đã mua hàng',
            })
        </script>
    @elseif($errors->has('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Xóa thành công',
                text: 'Bạn vừa xóa 1 người dùng',
            })
        </script>
    @elseif($errors->has('error_sv'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi không xác định',
                text: 'Hãy thử tải lại trang',
            })
        </script>
    @elseif($errors->has('admin'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Xóa thất bại',
                text: 'Không thể xóa Super Admin',
            })
        </script>
    @elseif($errors->has('user_index'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Xóa thất bại',
                text: 'Không thể xóa bản thân',
            })
        </script>
    @endif
    <div class="card">
        <div class="card-body">
        <div id="filter_p" style="margin-bottom:15px;">
            <div class="collapse" id="collapseExample">
                <form class="form-group" action="" onsubmit="return validate_filter()" method="GET" enctype="multipart/form-data"> 
                    <input type="hidden" name="search" value="true">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="user_email" >Email</label>
                                <input type="text"  value="{{(request()->user_email)?request()->user_email:''}}" name="user_email" id="user_email" class="form-control"> 
                            </div> 
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="user_phone" >Điện thoại</label>
                                <input type="text"  value="{{(request()->user_phone)?request()->user_phone:''}}" name="user_phone" id="user_phone" class="form-control">
                            </div> 
                        </div>
                        <!-- province -->
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
                        <!-- district -->
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
                        <!-- ward -->
                        <div class="col-md-4 form-group">
                            <label for="ward">Khu vực</label>
                            <select name="ward" id="ward" class="form-control">
                                <option  value="0">Chọn khu vực...</option>
                                @foreach($list_ward as $wards => $ward)
                                    <option @if(request()->ward==$ward->id) selected @endif value="{{$ward->id}}">{{$ward->_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- datetime picker -->
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
                        <div class="col-md-3">
                            <label for="active">Kích hoạt</label>
                            <select name="active" id="active" class="form-control">
                                <option value="0" @if(request()->active==0) selected @endif>Tất cả</option>
                                <option value="1" @if(request()->active==1) selected @endif >Kích hoạt</option>
                                <option value="2" @if(request()->active==2) selected @endif>Không kích hoạt</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="user_type">Người dùng Admin</label>
                            <select name="user_type" id="user_type" class="form-control">
                                <option value="0" @if(request()->user_type==0) selected  @endif>Tất cả</option>
                                <option value="1" @if(request()->user_type==1) selected  @endif>Admin</option>
                                <option value="2" @if(request()->user_type==2) selected  @endif>Client</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="role">Vai trò</label>
                            <select name="role" id="role" class="form-control">
                                <option value="0"  >Tất cả</option>
                                @foreach($list_role as $roles => $role)
                                    <option value="{{$role->role_id}}" @if(request()->role==$role->role_id) selected  @endif >{{$role->role}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="permission">Quyền</label>
                            <select name="permission" id="permission" class="form-control">
                                <option value="0">Tất cả</option>
                                @foreach($list_permission as $permissions => $permission)
                                    <option value="{{$permission->permission_id}}" @if(request()->permission==$permission->permission_id) selected  @endif >{{$permission->permission}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
            <div class="card-title" style="font-size:36px;text-align:center">
                Danh sách người dùng
            </div>
            <!-- setting view -->
            <div class="">
                <a href="{{url('admin/user/create')}}" class="btn btn-success mb-2">Thêm mới người dùng</a>
                <div style="float:right" class="col-md-9">
                   
                    <p style="text-align:right">
                        <a class="" data-toggle="collapse" href="#view" role="button" aria-expanded="false" aria-controls="view">
                            <i class="fas fa-cog"></i> Tùy chọn hiển thị
                        </a>
                    </p>
                    <div class="collapse col-md-12" id="view">
                        <div class="form-row" style="float:right">
                           
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','email'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="email" id="email" class="form-control-checkbox view-setting" value="email">
                                <label for="email">Email</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','phone'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="phone" id="phone" class="form-control-checkbox view-setting" value="phone">
                                <label for="phone">Điện thoại</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','province')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="provincee" id="provincee" class="form-control-checkbox view-setting" value="province">
                                <label for="provincee">Thành phố/Tỉnh</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','district')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="districtt" id="districtt" class="form-control-checkbox view-setting" value="district">
                                <label for=districtt>Quận/huyện</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','ward')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="wardd" id="wardd" class="form-control-checkbox view-setting" value="ward">
                                <label for=wardd>Khu vực</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','address')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="addresss" id="adresss" class="form-control-checkbox view-setting" value="address">
                                <label for=adresss>Địa chỉ</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','address')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="admin" id="admin" class="form-control-checkbox view-setting" value="admin">
                                <label for=admin>Admin</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','detail')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="detail" id="detail" class="form-control-checkbox view-setting" value="detail">
                                <label for=detail>Chi tiết</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','role')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')"  name="rolee" id="rolee" class="form-control-checkbox view-setting" value="role">
                                <label for=rolee>Vai trò</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','permission')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="permissionn" id="permissionn" class="form-control-checkbox view-setting" value="permission">
                                <label for=permissionn>Quyền</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','permission')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="activee" id="activee" class="form-control-checkbox view-setting" value="active">
                                <label for=activee>Kích hoạt</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','create_at')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="create_att" id="create_att" class="form-control-checkbox view-setting" value="create_at">
                                <label for=create_att>Ngày tạo</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','create_at')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="update_att" id="update_att" class="form-control-checkbox view-setting" value="update_at">
                                <label for=update_att>Ngày sửa</label>
                            </div>
                            <div class="m-2">
                                <input type="checkbox" @if(p_ui_setting('user','action')) checked @endif onclick="view_setting({{p_user()['user_id']}},'user')" name="action" id="action" class="form-control-checkbox view-setting" value="action">
                                <label for=action>Thao tác</label>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>  
            <!-- //table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <th  >Họ tên</th>
                        <th class="email p_setting" @if(!p_ui_setting('user','email'))  style="display:none" @endif>Email</th>
                        <th class="phone p_setting" @if(!p_ui_setting('user','phone'))  style="display:none" @endif>Điện thoại</th>
                        <th class="province p_setting" @if(!p_ui_setting('user','province'))  style="display:none" @endif>Thành phố/Tỉnh</th>
                        <th class="district p_setting" @if(!p_ui_setting('user','district'))  style="display:none" @endif>Quận/Huyện</th>
                        <th class="ward p_setting" @if(!p_ui_setting('user','ward'))  style="display:none" @endif>Khu vực</th>
                        <th class="address p_setting" @if(!p_ui_setting('user','address'))  style="display:none" @endif>Địa chỉ</th>
                        <th class="admin p_setting" @if(!p_ui_setting('user','admin'))  style="display:none" @endif>Admin</th>
                        <th class="detail p_setting" @if(!p_ui_setting('user','detail'))  style="display:none" @endif>Chi tiết</th>
                        <th class="role p_setting" @if(!p_ui_setting('user','role'))  style="display:none" @endif>Vai trò</th>
                        <th class="permission p_setting" @if(!p_ui_setting('user','permission'))  style="display:none" @endif>Quyền</th>
                        <th class="active p_setting" @if(!p_ui_setting('user','active'))  style="display:none" @endif>Kích hoạt</th>
                        <th class="create_at p_setting" @if(!p_ui_setting('user','create_at'))  style="display:none" @endif>Ngày tạo</th>
                        <th class="update_at p_setting" @if(!p_ui_setting('user','update_at'))  style="display:none" @endif>Ngày sửa</th>
                        <th class="action p_setting" @if(!p_ui_setting('user','action'))  style="display:none" @endif>Thao tác</th>
                    </thead>
                    <tbody>
                       @foreach($list_user as $users => $user)
                            <tr>
                               
                                <td >{{$user->user_first_name}}&nbsp; {{$user->user_last_name}}</td>
                                <td class="email p_setting" @if(!p_ui_setting('user','email'))  style="display:none" @endif>{{$user->user_email}}</td>
                                <td class="phone p_setting" @if(!p_ui_setting('user','phone'))  style="display:none" @endif>{{$user->user_phone}}</td>
                                <td class="province p_setting" @if(!p_ui_setting('user','province')) style="display:none" @endif  >{{$user->province}}</td>
                                <td class="district p_setting" @if(!p_ui_setting('user','district')) style="display:none" @endif >{{$user->district}}</td>
                                <td class="ward p_setting" @if(!p_ui_setting('user','ward')) style="display:none" @endif >{{$user->ward}}</td>
                                <td class="address p_setting" @if(!p_ui_setting('user','address')) style="display:none" @endif >{{$user->user_address}}</td>
                                <td class="admin p_setting" @if(!p_ui_setting('user','admin')) style="display:none" @endif >
                                    @if($user->user_type==1)
                                        <i class="fas fa-check" style="color:#3ac47d"></i>
                                    @else
                                        <i class="fas fa-times" style="color:#b81f44"></i>
                                    @endif
                                </td>
                                <td class="detail p_setting" @if(!p_ui_setting('user','detail')) style="display:none" @endif >
                                    <a href="{{url('admin/user')}}/{{$user->user_id}}/detail" class="btn btn-info">Chi tiết</a>
                                </td>
                                <td class="role p_setting" @if(!p_ui_setting('user','role')) style="display:none" @endif >
                                    @if(p_author('edit_role','tbl_user'))
                                        @if($user->user_type==1)
                                            @if(!empty($user->role))
                                                <a href="{{url('admin/user')}}/{{$user->user_id}}/editrole" >
                                                    @php
                                                        $roleText='';  
                                                    @endphp  
                                                    @foreach($user->role as $roles => $role)
                                                        @php
                                                            $roleText.=$role->role.',';  
                                                        @endphp 
                                                    @endforeach
                                                    {{Str::limit(rtrim($roleText,','),30)}}
                                                </a>
                                            @else
                                                <a href="{{url('admin/user')}}/{{$user->user_id}}/editrole" style="font-size:25px" >+</a>
                                            @endif
                                        @endif
                                    @else
                                        @if($user->user_type==1)
                                            @if(!empty($user->role))
                                                @php
                                                    $roleText='';  
                                                @endphp  
                                                @foreach($user->role as $roles => $role)
                                                    @php
                                                        $roleText.=$role->role.',';  
                                                    @endphp 
                                                @endforeach
                                                {{Str::limit(rtrim($roleText,','),30)}}
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                <td class="permission p_setting" @if(!p_ui_setting('user','permission')) style="display:none" @endif >
                                    @if(p_author('edit_permission','tbl_user'))
                                        @if($user->user_type==1)
                                            <a href="{{url('admin/user')}}/{{$user->user_id}}/editpermission" >
                                                @if(!empty($user->permission))
                                                    @php
                                                        $permissionText='';  
                                                    @endphp 
                                                    @foreach($user->permission as $permissions => $permission)
                                                        @php
                                                            $permissionText.=$permission->permission.',';  
                                                        @endphp 
                                                    @endforeach
                                                    {{Str::limit(rtrim($permissionText,','),30)}}
                                                @else
                                                <a href="{{url('admin/user')}}/{{$user->user_id}}/editpermission" style="font-size:25px" >+</a>
                                                @endif
                                            </a>
                                        @endif
                                    @else
                                        @if($user->user_type==1)
                                            @if(!empty($user->permission))
                                                @php
                                                    $permissionText='';  
                                                @endphp  
                                                @foreach($user->permission as $permissions => $permission)
                                                    @php
                                                        $permissionText.=$permission->permission.',';  
                                                    @endphp 
                                                @endforeach
                                                {{Str::limit(rtrim($permissionText,','),30)}}
                                          
                                            @endif
                                            
                                        @endif
                                    @endif
                                </td>
                                <td class="active p_setting" @if(!p_ui_setting('user','active')) style="display:none" @endif>
                                    @if(!p_author('active','tbl_user'))
                                        {!!($user->active==1)?'<i class="fas fa-check" style="color:#3ac47d"></i>':'<i class="fas fa-times" style="color:#b81f44"></i>'!!}
                                    @else
                                        @if($user->active==1)
                                            <a href="#active" class="p_user_active" data-id="{{$user->user_id}}"><i class="fas fa-check" style="color:#3ac47d"></i></a>
                                        @else
                                            <a href="#" class="p_user_active" data-id="{{$user->user_id}}"><i class="fas fa-times" style="color:#b81f44"></i></a>
                                        @endif
                                        <!-- script active user -->
                                        <script>
                                                $('.p_user_active').unbind().click(function(){
                                                    var id=$(this).attr('data-id');
                                                    if(id=='{{p_user()["user_id"]}}'){
                                                        return;
                                                    }
                                                    var element=$(this);
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "{{url('api/user/active')}}",
                                                        data: {id:id},
                                                        dataType: "json",
                                                        success: function (response) {
                                                            console.log(response);
                                                            if(!$.isEmptyObject(response.error)){
                                                                $.each(response.error,function(index,item){
                                                                    if(index =='admin'){
                                                                        Swal.fire({
                                                                            icon:'error',
                                                                            title:'Không thể hủy kích hoạt',
                                                                            text:'Đây là Super Admin'
                                                                        })
                                                                    }
                                                                })
                                                            }else{
                                                                if(response.success==1){
                                                                    element.html('<i class="fas fa-check" style="color:#3ac47d"></i>')
                                                                }else{
                                                                    element.html('<i class="fas fa-times" style="color:#b81f44"></i>')
                                                                }
                                                                
                                                            }
                                                        }
                                                    });
                                                    return false;
                                                })
                                        </script>
                                    @endif

                                </td>
                                <td class="create_at p_setting" @if(!p_ui_setting('user','create_at')) style="display:none" @endif>{{$user->create_at}}</td>
                                <td class="update_at p_setting" @if(!p_ui_setting('user','update_at')) style="display:none" @endif> {{$user->update_at}}</td>
                                <td class="action p_setting" @if(!p_ui_setting('user','action')) style="display:none" @endif>
                                    @if(p_author('edit','tbl_user'))
                                    <a class="btn btn-primary" style="display:inline-block" href="{{url('admin/user/')}}/{{$user->user_id}}/edit"><div class="fa fa-edit"></div></a>
                                    @endif
                                    @if(p_author('delete','tbl_user'))
                                        <form action="{{url('admin/user')}}/{{$user->user_id}}/delete" method="post" style="display:inline-block;margin:0">
                                            @csrf
                                            <button type="submit" class="btn btn-danger"><div class="fa fa-trash-alt"></div></button>
                                        </form>
                                    @endif
                                </td>   
                            </tr>
                       @endforeach
                    </tbody>
                    <tfoot>
                    <thead>
                        <th >Họ tên</th>
                        <th class="email p_setting" @if(!p_ui_setting('user','email'))  style="display:none" @endif>Email</th>
                        <th class="phone p_setting" @if(!p_ui_setting('user','phone'))  style="display:none" @endif>Điện thoại</th>
                        <th class="province p_setting" @if(!p_ui_setting('user','province'))  style="display:none" @endif>Thành phố/Tỉnh</th>
                        <th class="district p_setting" @if(!p_ui_setting('user','district'))  style="display:none" @endif>Quận/Huyện</th>
                        <th class="ward p_setting" @if(!p_ui_setting('user','ward'))  style="display:none" @endif>Khu vực</th>
                        <th class="address p_setting" @if(!p_ui_setting('user','address'))  style="display:none" @endif>Địa chỉ</th>
                        <th class="admin p_setting" @if(!p_ui_setting('user','admin'))  style="display:none" @endif>Admin</th>
                        <th class="detail p_setting" @if(!p_ui_setting('user','detail'))  style="display:none" @endif>Chi tiết</th>
                        <th class="role p_setting" @if(!p_ui_setting('user','role'))  style="display:none" @endif>Vai trò</th>
                        <th class="permission p_setting" @if(!p_ui_setting('user','permission'))  style="display:none" @endif>Quyền</th>
                        <th class="active p_setting" @if(!p_ui_setting('user','active'))  style="display:none" @endif>Kích hoạt</th>
                        <th class="create_at p_setting" @if(!p_ui_setting('user','create_at'))  style="display:none" @endif>Ngày tạo</th>
                        <th class="update_at p_setting" @if(!p_ui_setting('user','update_at'))  style="display:none" @endif>Ngày sửa</th>
                        <th class="action p_setting" @if(!p_ui_setting('user','action'))  style="display:none" @endif>Thao tác</th>
                    </thead>
                    </tfoot>
                </table>
            </div> 
            <!-- end table response -->
            {{$list_user->appends(request()->all())->links()}}
        </div>
    </div>
    
@endsection
