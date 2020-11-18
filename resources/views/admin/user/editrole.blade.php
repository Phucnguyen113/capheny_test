@extends('layouts.admin')
@section('js')
<script>
    function add_role(){
        var data=$('#form').serialize();
        $.ajax({
            type: "POST",
            url: "{{url('admin/user/')}}/{{$user->user_id}}/editrole",
            data: data,
            dataType: "json",
            success: function (response) {
                console.log(response );
                if(!$.isEmptyObject(response.error)){
                    $('.p_error').html('')
                    $.each(response.error,function(index,item){
                        $(`#${index}_error`).html(item);
                    })
                }else{
                    Swal.fire({
                        icon:'success',
                        title:'Sửa thành công',
                        text:'Bạn vừa sửa vai trò 1 người dùng'
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
        <div class="card-title" style="text-align: center;font-size:36px">Chỉnh sửa vai trò cho người dùng</div>
        <form action="" method="post" onsubmit=" return add_role()" id="form">
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
                    <h3 style="font-weight: bold;">Vai trò</h3>
                    <label for="role_id"> Vai trò <span style="color:Red"> *</span></label>
                    <select name="role_id[]" id="role_id" class="form-control" multiple style="width:100%">
                        <option value="0">Không cấp quyền</option>
                        @foreach($list_role as $roles => $role)
                            <option @if(in_array($role->role_id,$list_role_id_of_user)) selected @endif value="{{$role->role_id}}">{{$role->role}}</option>
                        @endforeach
                    </select>
                    <div id="role_id_error" class="p_error" style="color:red"></div>
                    <script>
                        $('#role_id').select2({
                          placeholder:'Chọn vai trò'
                        });
                    </script>
                </div>
            </div>
            <div class="form-row form-group">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Sửa vai trò </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection