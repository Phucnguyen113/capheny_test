@extends('layouts.admin')
@section('body')
    <a href="{{url()->previous()}}" class="btn btn-warning mb-2" style="color:white">Quay lại</a>
    <div class="card">
        <div class="card-body">
            <div class="card-title" style="font-size:36px;text-align:center">Cập nhật thông tin cá nhân</div>
            <form action="" method="POST" id="form" onsubmit="return add_user()">
                @csrf 
                <div class="form-row form-group">
                    <div class="col-md-3">
                        <label for="user_name">Tên tài khoản <span style="color:red"> *</span></label>
                        <input type="text" value="{{$user->user_name}}" name="user_name" id="user_name" class="form-control" placeholder="Tên tài khoản">
                        <div id="user_name_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="user_email">Email<span style="color:red"> *</span></label>
                        <input type="text" value="{{$user->user_email}}" name="user_email" id="user_email" class="form-control" placeholder="Email">
                        <div id="user_email_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="user_password">Mật khẩu<span style="color:red"> *</span></label>
                        <input type="password" name="user_password" id="user_password" class="form-control" placeholder="Mật khẩu">
                        <div id="user_password_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="user_password_confirm">Xác nhận mật khẩu<span style="color:red"> *</span></label>
                        <input type="password" name="user_password_confirm" id="user_password_confirm" class="form-control" placeholder="Xác nhận mật khẩu">
                        <div id="user_password_confirm_error" class="p_error" style="color:red"></div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-4">
                        <label for="user_last_name">Họ <span style="color:red"> *</span></label>
                        <input type="text" value="{{$user->user_last_name}}" name="user_last_name" id="user_last_name" class="form-control" placeholder="Họ">
                        <div id="user_last_name_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="user_first_name">Tên <span style="color:red"> *</span></label>
                        <input type="text" value="{{$user->user_first_name}}" name="user_first_name" id="user_first_name" class="form-control" placeholder="Tên">
                        <div id="user_first_name_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="user_phone">Điện thoại<span style="color:red"> *</span></label>
                        <input type="text" value="{{$user->user_phone}}" name="user_phone" id="user_phone" class="form-control" placeholder="Điện thoại">
                        <div id="user_phone_error" class="p_error" style="color:red"></div>
                    </div>
                </div>
                <div class="form-row form-group">
                   <div class="col-md-3">
                        <label for="province">Thành phố/Tỉnh <span style="color:red"> *</span></label>
                        <select name="province" id="province" class="form-control">
                            <option value="0">Chọn Thành phố/Tỉnh...</option>
                            @foreach($list_province as $key => $value)
                                <option @if($value->id== $user->province) selected @endif value="{{$value->id}}">{{$value->_name}}</option>
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
                            @foreach($list_district as $districts =>$district)
                                <option @if($district->id== $user->district) selected @endif value="{{$district->id}}">{{$district->_name}}</option>
                            @endforeach
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
                            @foreach($list_ward as $wards => $ward)
                                <option @if($ward->id == $user->ward) selected @endif value="{{$ward->id}}">{{$ward->_name}}</option>
                            @endforeach
                        </select>
                        <div id="ward_error" class="p_error" style="color:red"></div>
                   </div>
                   <div class="col-md-3">
                       <label for="address">Địa chỉ <span style="color:red"> *</span></label>
                       <input type="text" name="user_address" id="user_address" value="{{$user->user_address}}" class="form-control">
                       <div id="user_address_error" class="p_error" style="color:red"></div>
                   </div>
                </div>
                
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <label for="avatar">Ảnh đại diện</label>
                        <div class="custom-file">
                            <input type="file" name="avatar"  class="custom-file-input" id="avatar" >
                            <label class="custom-file-label" for="avatar">Chọn ảnh...</label>
                            <div class="invalid-feedback">Example invalid custom file feedback</div>
                        </div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-12">
                       
                        <button type="submit" class="btn btn-primary">Sửa</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function add_user(){
            var formdata= new FormData();
            var _token=$('input[name="_token"]').val();
            var user_name=$('#user_name').val();
            var user_email=$('#user_email').val();
            var user_password=$('#user_password').val();
            var user_password_confirm=$('#user_password_confirm').val();
            var user_first_name=$('#user_first_name').val();
            var user_last_name=$('#user_last_name').val();
            var user_phone=$('#user_phone').val();
            var province=$('#province').val();
            var district=$('#district').val();
            var ward=$('#ward').val();
            var user_address=$('#user_address').val();
            var avatar=$('#avatar').prop('files')[0];
           
            formdata.append('_token',_token);
            formdata.append('user_name',user_name);
            formdata.append('user_email',user_email);
            formdata.append('user_password',user_password);
            formdata.append('user_password_confirm',user_password_confirm);
            formdata.append('user_first_name',user_first_name);
            formdata.append('user_last_name',user_last_name)
            formdata.append('user_phone',user_phone)
            formdata.append('province',province);
            formdata.append('district',district)
            formdata.append('ward',ward);
            formdata.append('user_address',user_address)
            if(avatar!==undefined){
                formdata.append('avatar',avatar)
            }   
            
            Swal.fire ({
            title: 'Xin chờ...',
            onBeforeOpen: () => {
                Swal.showLoading ()
            }
            ,allowEscapeKey: false,
            allowOutsideClick: false,
            showCloseButton:false,
            showCancelButton:false,
            showConfirmButton:false,
            timer:8000
        })
            $.ajax({
                type: "post",
                url: "{{url('admin/user')}}/edit",
                data: formdata,
                contentType:false,
                processData:false,
                dataType: "json",
                success: function (response) {
                    if(!$.isEmptyObject(response.error)){
                        $('.p_error').html('');
                        console.log(response.error);
                        $.each(response.error,function(index,item){
                            $('#'+index+"_error").html(item)
                        })
                        Swal.close();
                    }else{
                        Swal.fire({
                            icon:'success',
                            title:'Sửa thành công !',
                            text:'Bạn vừa cập nhật thông tin cá nhân'
                        }).then(()=>{
                            window.location.href="{{url('admin/user/edit')}}"
                        })
                        
                    }
                }
            });
            return false;
        }
    </script>
@endsection