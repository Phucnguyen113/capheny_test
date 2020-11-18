@extends('layouts.admin')
@section('body')
    <div class="card main-card">
        <div class="card-body">
            <div class="card-title" style="font-size:36px;text-align:center">Chỉnh sửa vai trò</div>
            <form action="" onsubmit="return edit_role()" method="post" id="form">
                    @csrf
                    <div class="form-row form-group">
                        <div class="col-md-12">
                            <label for="role">Vai trò <span style="color:red"> *</span></label>
                            <input type="text" name="role" id="role" value="{{$role->role}}" class="form-control">
                        </div>
                        <div id="role_error" class="p_error" style="color:Red"></div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-md-12">
                            <label for="permission">Quyền của vai trò <span style="color:red"> *</span></label>
                            <select name="permission[]" id="permission" multiple class="form-control">
                                @foreach($list_permission as $permissions => $permission)
                                    <option @if(in_array($permission->permission_id,$permission_id_old)) selected @endif value="{{$permission->permission_id}}">{{$permission->permission}}</option>
                                @endforeach
                            </select>
                            <script>
                                $('#permission').select2()
                            </script>
                            <div id="permission_error" class="p_error" style="color:red"></div>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Sửa vai trò</button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
<script>
    function edit_role(){
        var data=$('#form').serialize();
        $.ajax({
            type: "POST",
            url: "{{url('admin/role')}}/{{$role->role_id}}/edit",
            data: data,
            dataType: "json",
            success: function (response) {
               
                if(!$.isEmptyObject(response.error)){
                   $('.p_error').html('')
                    $.each(response.error,function(index,item){
                        $(`#${index}_error`).html(item)
                    })
                }else{
                    Swal.fire({
                        icon:'success',
                        title:'Sửa thành công!',
                        text:'Bạn vừa sửa 1 vai trò'
                    }).then(()=>{
                        window.location.href='{{url("admin/role")}}'
                    })
                }
            }   
        });
        return false;
    }
</script>
@endsection
@section('css')
@endsection