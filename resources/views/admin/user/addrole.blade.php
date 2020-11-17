@extends('layouts.admin')
@section('js')
<script>
    function add_role(){
        var data=$('#form').serialize();
        $.ajax({
            type: "POST",
            url: "{{url('admin/user/addrole')}}",
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
                        title:'Thêm thành công',
                        text:'Bạn vừa thêm 1 vai trò cho người dùng'
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
        <div class="card-title" style="text-align: center;font-size:36px">Thêm vai trò cho người dùng</div>
        <form action="" method="post" onsubmit=" return add_role()" id="form">
            @csrf
            <div class="form-row form-group">
                <div class="col-md-6">
                    <label for="user_id">Người dùng Admin <span style="color:Red"> *</span></label>
                    <select name="user_id" id="user_id" class="form-control" style="width:100%" >
                        @foreach($list_user as $users => $user)
                            <option @if(request()->has('user_id') && request()->user_id == $user->user_id) selected @endif value="{{$user->user_id}}">{{$user->user_email}}</option>
                        @endforeach
                    </select>
                    <div id="user_id" class="p_error" style="color:red"></div>
                    <script>
                        $('#user_id').select2({
                            placeholder:'Chọn người dùng admin'
                        })
                    </script>
                </div>
                <div class="col-md-6">
                    <label for="role_id"> Vai trò <span style="color:Red"> *</span></label>
                    <select name="role_id[]" id="role_id" class="form-control" multiple style="width:100%">
                        @foreach($list_role as $roles => $role)
                            <option value="{{$role->role_id}}">{{$role->role}}</option>
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
                    <button type="submit" class="btn btn-success">Thêm vai trò </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection