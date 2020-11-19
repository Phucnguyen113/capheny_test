@extends('layouts.admin')
@section('body')
    <div class="card main-card">
        <div class="card-body">
            <div class="card-title" style="text-align:center;font-size:36px">
                    Thêm vài trò
            </div>
            <form action="" method="post" id="form" onsubmit="return add_role()">
                @csrf 
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <label for="role">Vai trò <span style="color:red"> *</span></label>
                        <input type="text" name="role" id="role" class="form-control">
                        <div id="role_error" class="p_error" style="color:Red">
                        
                        </div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Thêm vai trò</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
<script>
    function add_role(){
        var data=$("#form").serialize();
        $.ajax({
            type: "POST",
            url: "{{url('admin/role/create')}}",
            data: data,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!$.isEmptyObject(response.error)){
                    $(".p_error").html('');
                    $.each(response.error,function(index,item){
                        $(`#${index}_error`).html(item)
                    })
                }else{
                    Swal.fire({
                        icon:'success',
                        title:'Thêm thành công!',
                        text:'Bạn vừa thêm 1 vai trò'
                    }).then(()=>{
                        window.location.href='{{url("admin/role")}}';
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