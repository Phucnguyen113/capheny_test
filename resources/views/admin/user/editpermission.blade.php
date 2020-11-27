@extends('layouts.admin')
@section('js')
<script>
    function edit_permission(){
        var data=$('#form').serialize();
        $.ajax({
            type: "POST",
            url: "{{url('admin/user/')}}/{{$user->user_id}}/editpermission",
            data: data,
            dataType: "json",
            success: function (response) {
                console.log(response );
                if(!$.isEmptyObject(response.error)){
                    $('.p_error').html('')
                    $.each(response.error,function(index,item){
                        $(`#${index}_error`).html(item);
                        if(index=='admin'){
                            Swal.fire({
                                icon:'error',
                                title:'Sửa thất bại!',
                                text:'Không thể chỉnh sửa tài khoản Super Admin'
                            })
                        }
                    })
                }else{
                    Swal.fire({
                        icon:'success',
                        title:'Sửa thành công',
                        text:'Bạn vừa sửa quyền 1 người dùng'
                    }).then(()=>{
                        window.location.href='{{urL("admin/user")}}';
                    })
                }
            }
        });
        return false;
    }
</script>
@endsection
@section('css')
    <style>
        .select2-selection__rendered {
            line-height: 30px !important;
        }
        .select2-container .select2-selection--single {
            height: 32px !important;
        }
        .select2-selection__arrow {
            height: 32px !important;
        }
    </style>
@endsection
@section('body')
<div class="card card-body">
    <div class="card-body">
        <div class="card-title" style="text-align: center;font-size:36px">Chỉnh sửa quyền cho người dùng</div>
        <form action="" method="post" onsubmit=" return edit_permission()" id="form">
            @csrf
            <div class="form-row form-group">
                <div class="col-md-12">
                   
                    <h3 for="user_id" style="font-weight: bold;">Thông tin cá nhân </h3>
                    <p style="text-transform: uppercase;">
                        <p style="font-weight: bold;margin:0">Họ tên</p>
                        {{$user->user_first_name}} &nbsp;{{$user->user_last_name}}
                        <hr>
                    </p>
                    <p style="text-transform: uppercase;">
                        <p style="font-weight: bold;margin:0">Email</p>
                        {{$user->user_email}}
                        <hr>
                    </p>
                    <p style="text-transform: uppercase;">
                        <p style="font-weight: bold;margin:0">Điện thoại</p>
                        {{$user->user_phone}}
                        <hr>
                    </p>
                   
                    <div id="user_id" class="p_error" style="color:red"></div>
                   
                </div>
                <div class="col-md-12">
                    <h3 style="font-weight: bold;">Quyền</h3>
                    <label for="permission_id"> Quyền<span style="color:Red"> *</span></label>
                    <select name="permission_id[]" id="permission_id" class="form-control" multiple style="width:100%">
                        <option value="0">Không cấp quyền</option>
                        @foreach($list_permission as $permissions => $permission)
                            <option @if(in_array($permission->permission_id,$id_permission_of_user)) selected @endif value="{{$permission->permission_id}}">{{$permission->permission}}</option>
                        @endforeach
                    </select>
                    <div id="permission_id_error" class="p_error" style="color:red"></div>
                    <script>
                        $('#permission_id').select2({
                          placeholder:'Chọn quyền'
                        });
                    </script>
                </div>
            </div>
            <div class="form-row form-group">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Sửa quyền </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection