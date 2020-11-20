@extends('layouts.admin')
@section('css')
@endsection
@section('js')
<script>
    function add_permission(){
        var data=$('#form').serialize();
        $.ajax({
            type: "POST",
            url: "{{url('admin/permission/create')}}",
            data:data ,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!$.isEmptyObject(response.error)){
                    $('.p_error').html('')
                    $.each(response.error,function(index,item){
                        $(`#${index}_error`).html(item);
                    })
                }else{
                    Swal.fire({
                        icon:'success',
                        title:'Thêm thành công!',
                        text:'Bạn vừa thêm thành công 1 quyền'
                    }).then(()=>{
                        window.location.href="{{url('admin/permission')}}"
                    })
                }
            }
        });
        return false;
    }
</script>
@endsection
@section('body')
    <div class="card main-card">
        <div class="card-body">
            <div class="card-title" style="font-size:36px;text-align:center">Thêm quyền    </div>
            <form action="" method="post" onsubmit="return add_permission()" id="form">
                @csrf
                <div class="form-row form-group">
                    <div class="col-md-4">
                        <label for="permission">Quyền <span style="color:red"> *</span></label>
                        <input type="text" name="permission" id="permission" class="form-control">
                        <div id="permission_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="table">Table <span style="color:red"> *</span></label>
                        <select name="table" id="table" class="form-control">
                            <option value="">Chọn bảng...</option>
                            <option value="tbl_user">Bảng người dùng</option>
                            <option value="tbl_permission">Bảng quyền</option>
                            <option value="tbl_role">Bảng vai trò</option>
                            <option value="tbl_category">Bảng danh mục</option>
                            <option value="tbl_product">Bảng sản phẩm</option>
                            <option value="tbl_comment">Bảng bình luận</option>
                            <option value="tbl_store">Bảng cửa hàng</option>
                            <option value="tbl_order">Bảng đơn hàng </option>
                            <option value="tbl_color">Bảng màu</option>
                            <option value="tbl_size">Bảng kích thước</option>
                        </select>
                        <div id="table_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="action">Thao tác <span style="color:red"> *</span></label>
                        <select name="action" id="action" class="form-control">
                            <option value="">Chọn hành động</option>
                            <!-- <option value="add">Thêm mới</option>
                            <option value="edit">Chỉnh sửa</option>
                            <option value="delete">Xóa</option>
                            <option value="view">Xem danh sách, chi tiết</option>
                            <option value="add_product">Thêm sản phẩm vào kho</option>
                            <option value="active">Kích hoạt</option>
                            <option value="add_permission">Thêm quyền người dùng</option>
                            <option value="edit_permission">Sửa quyền người dùng</option>
                            <option value="add_role">Thêm vai trò người dùng</option>
                            <option value="edit_role">Sửa vai trò người dùng</option> -->
                        </select>
                        <div id="action_error" class="p_error" style="color:red"></div>
                        <script>
                            $('#table').change(function(){
                                $.ajax({
                                    type: "POST",
                                    url: "{{url('api/get_action')}}/"+$(this).val(),
                                    data: {},
                                    dataType: "json",
                                    success: function (response) {
                                        console.log(response);
                                        if(!$.isEmptyObject(response.error)){

                                        }else{
                                            $('#action').html('<option value="">Chọn hành động</option>')
                                            $.each(response.data,function(index,item){
                                                $(`#action`).append(`<option value="${item.action}">${item.action}</option>`)
                                            })
                                        }
                                    }
                                });
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