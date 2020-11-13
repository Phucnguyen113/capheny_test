@extends('layouts.admin')
@section('js')
   
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
                        <div class="col-md-2">
                            <input type="checkbox" @if(request()->has('active'))  checked @endif name="active" id="active" class="form-control-checkbox">
                            <label for="active">Kích hoạt</label>
                        </div>
                        <div class="col-md-2">
                            <input type="checkbox"  @if(request()->has('user_type'))  checked @endif name="user_type" id="user_type" class="form-control-checkbox">
                            <label for="user_type">Người dùng Admin</label>
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
            <a href="{{url('admin/user/create')}}" class="btn btn-success mb-2">Thêm mới người dùng</a>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Thành phố/Tỉnh</th>
                        <th>Quận/Huyện</th>
                        <th>Khu vực</th>
                        <th>Địa chỉ</th>
                        <th>Kích hoạt</th>
                        <th>Ngày tạo</th>
                        <th>Ngày sửa</th>
                        <th>Thao tác</th>
                    </thead>
                    <tbody>
                       @foreach($list_user as $users => $user)
                            <tr>
                               
                                <td>{{$user->user_first_name}}&nbsp; {{$user->user_last_name}}</td>
                                <td>{{$user->user_email}}</td>
                                <td>{{$user->user_phone}}</td>
                                <td>{{$user->province}}</td>
                                <td>{{$user->district}}</td>
                                <td>{{$user->ward}}</td>
                                <td>{{$user->user_address}}</td>
                                <td>{{($user->active==1)?'Kích hoạt':'Không'}}</td>
                                <td>{{$user->create_at}}</td>
                                <td>{{$user->update_at}}</td>
                                <td>
                                    <a class="btn btn-primary" style="display:inline-block" href="{{url('admin/user/')}}/{{$user->user_id}}/edit"><div class="fa fa-edit"></div></a>
                                    <form action="{{url('admin/user')}}/{{$user->user_id}}/delete" method="post" style="display:inline-block;margin:0">
                                        @csrf
                                        <button type="submit" class="btn btn-danger"><div class="fa fa-trash-alt"></div></button>
                                    </form>
                                </td>   
                            </tr>
                       @endforeach
                    </tbody>
                    <tfoot>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Thành phố/Tỉnh</th>
                        <th>Quận/Huyện</th>
                        <th>Khu vực</th>
                        <th>Địa chỉ</th>
                        <th>Kích hoạt</th>
                        <th>Ngày tạo</th>
                        <th>Ngày sửa</th>
                        <th>Thao tác</th>
                    </tfoot>
                </table>
            </div> 
            <!-- end table response -->
            {{$list_user->appends(request()->all())->links()}}
        </div>
    </div>
@endsection