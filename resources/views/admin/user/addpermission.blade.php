@extends('layouts.admin')
@section('js')
    <script>
        function add_permission(){
            var data=$('#form').serialize()
            $.ajax({
                type: "POST",
                url: "{{url('admin/user/addpermission')}}",
                data: data,
                dataType: "json",
                success: function (response) {
                    if(!$.isEmptyObject(response.error)){
                        $('.p_error').html('')
                        $.each(response.error,function(index,item){
                            $(`#${index}_error`).html(item);
                            if(index=='admin'){
                                Swal.fire({
                                    icon:'error',
                                    title:'Thêm thất bại!',
                                    text:'Super admin đã có toàn quyền'
                                })
                            }
                        })
                    }else{
                        Swal.fire({
                            icon:'success',
                            title:'Thêm thành công',
                            text:'Bạn vừa thêm quyền cho người dùng'
                        }).then(()=>{
                            window.location.href='{{url("admin/user")}}'
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
<div class="card card-main">
    <div class="card-body">
        <div class="card-title" style="font-size: 36px;text-align:center">Thêm quyền cho người dùng</div>
        <form action="" method="post" onsubmit="return add_permission()" id="form">
                @csrf
                <div class="form-row form-group">
                    <div class="col-md-6">
                        <label for="user_id" > Người dùng Admin<span style="color:Red"> *</span></label>
                        <select name="user_id" id="user_id" class="form-control">
                            @foreach($list_user as $users => $user)
                                <option value="{{$user->user_id}}">{{$user->user_email}}</option>
                            @endforeach
                        </select>
                        <script>
                            $("#user_id").select2();
                        </script>
                        <div id="user_id" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="permission_id">Quyền <span style="color:red"> *</span></label>
                        <select name="permission_id[]" id="permission_id" class="form-control" multiple>
                            @foreach($list_permission as $permissions => $permission)
                                    <option value="{{$permission->permission_id}}">{{$permission->permission}}</option>
                            @endforeach
                        </select>
                        <div id="permission_id_error" class="p_error" style="color:Red"></div>
                        <script>
                            $("#permission_id").select2({
                                placeholder:'Chọn quyền'
                            })
                        </script>
                    </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Thêm quyền</button>
                    </div>
                </div>
        </form>
    </div>
</div>
@endsection