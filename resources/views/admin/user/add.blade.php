@extends('layouts.admin')
@section('body')
    <a href="{{url()->previous()}}" class="btn btn-warning mb-2" style="color:white">Quay lại</a>
    <div class="card">
        <div class="card-body">
            <div class="card-title" style="font-size:36px;text-align:center">Thêm mới người dùng</div>
            <form action="" method="POST" id="form" onsubmit="return add_user()">
                @csrf 
                <div class="form-row form-group">
                    <div class="col-md-3">
                        <label for="user_name">Tên tài khoản <span style="color:red"> *</span></label>
                        <input type="text" name="user_name" id="user_name" class="form-control" placeholder="Tên tài khoản">
                        <div id="user_name_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="user_email">Email<span style="color:red"> *</span></label>
                        <input type="text" name="user_email" id="user_email" class="form-control" placeholder="Email">
                        <div id="user_email_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="user_password">Mật khẩu<span style="color:red"> *</span></label>
                        <input type="text" name="user_password" id="user_password" class="form-control" placeholder="Mật khẩu">
                        <div id="user_password_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="user_password_confirm">Xác nhận mật khẩu<span style="color:red"> *</span></label>
                        <input type="text" name="user_password_confirm" id="user_password_confirm" class="form-control" placeholder="Xác nhận mật khẩu">
                        <div id="user_password_confirm_error" class="p_error" style="color:red"></div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-4">
                        <label for="user_last_name">Họ <span style="color:red"> *</span></label>
                        <input type="text" name="user_last_name" id="user_last_name" class="form-control" placeholder="Họ">
                        <div id="user_last_name_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="user_first_name">Tên <span style="color:red"> *</span></label>
                        <input type="text" name="user_first_name" id="user_first_name" class="form-control" placeholder="Tên">
                        <div id="user_first_name_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="user_phone">Điện thoại<span style="color:red"> *</span></label>
                        <input type="text" name="user_phone" id="user_phone" class="form-control" placeholder="Điện thoại">
                        <div id="user_phone_error" class="p_error" style="color:red"></div>
                    </div>
                </div>
                <div class="form-row form-group">
                   <div class="col-md-3">
                        <label for="province">Thành phố/Tỉnh <span style="color:red"> *</span></label>
                        <select name="province" id="province" class="form-control">
                            <option value="0">Chọn Thành phố/Tỉnh...</option>
                            @foreach($list_province as $key => $value)
                                <option value="{{$value->id}}">{{$value->_name}}</option>
                            @endforeach
                        </select>
                        <div id="province_error" class="p_error" style="color:red"></div>
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
                   </div>
                   <div class="col-md-3">
                        <label for="district">Quận/Huyện <span style="color:red"> *</span></label>
                        <select name="district" id="district" class="form-control">
                            <option value="0">Chọn Quận/Huyện ...</option>
                        </select>
                        <div id="district_error" class="p_error" style="color:red"></div>
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
                   <div class="col-md-3">
                        <label for="ward">Khu vực <span style="color:red"> *</span></label>
                        <select name="ward" id="ward" class="form-control">
                            <option value="0">Chọn khu vực ...</option>
                        </select>
                        <div id="ward_error" class="p_error" style="color:red"></div>
                   </div>
                   <div class="col-md-3">
                       <label for="address">Địa chỉ <span style="color:red"> *</span></label>
                       <input type="text" name="user_address" id="address" class="form-control">
                       <div id="user_address_error" class="p_error" style="color:red"></div>
                   </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-2">
                        <input type="checkbox" name="active" id="active" class="form-control-checkbox">
                        <label for="active">Kích hoạt</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" name="user_type" id="user_type" class="form-control-checkbox">
                        <label for="user_type">Người dùng Admin</label>
                    </div>
                </div>
                <div class="form-row form-group">
                   <div class="col-md-12">
                        
                        <button type="submit" class="btn btn-success">Thêm mới người dùng</button>
                   </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function add_user(){
            var data=$('#form').serialize()
            console.log(data._token);
            console.log(`data`, data);
            $.ajax({
                type: "post",
                url: "{{url('admin/user/create')}}",
                data: data,
                dataType: "json",
                success: function (response) {
                    if(!$.isEmptyObject(response.error)){
                        $('.p_error').html('');
                        console.log(response.error);
                        $.each(response.error,function(index,item){
                            $('#'+index+"_error").html(item)
                        })
                    }else{
                        window.location.href="{{url('admin/user')}}"
                    }
                }
            });
            return false;
        }
    </script>
@endsection