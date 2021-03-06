@extends('layouts.admin')
@section('body')
    <div class="card main-card">
        <div class="card-body">
            <div class="card-title">Thêm bình luận</div>
            <form action="" method="post" onsubmit="return add_comment()" id="form">
                @csrf
                <div class="form-row form-group">
                    <div class="col-md-6">
                        <label for="user_id">Người dùng <span style="color:Red"> *</span></label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">Chọn người dùng</option>
                            @foreach($list_user as $users => $user)
                                <option value="{{$user->user_id}}">{{$user->user_email}}</option>
                            @endforeach
                        </select>
                        <div id="user_id_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="product_id">Sản phẩm <span style="color:Red"> *</span></label>
                        <select name="product_id" id="product_id" class="form-control">
                            <option value="">Chọn sản phẩm</option>
                            @foreach($list_product as $products => $product)
                                <option value="{{$product->product_id}}">{{$product->product_name}}</option>
                            @endforeach
                        </select>
                        <div id="product_id_error" class="p_error" style="color:red"></div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <label for="content">Nội dụng bình luận <span style="color:Red"> *</span></label>
                        <textarea name="content" id="content" cols="30" rows="10" class="form-control" ></textarea>
                        <script>
                            CKEDITOR.replace('content')
                        </script>
                        <div id="content_error" class="p_error" style="color:red"></div>
                    </div>
                </div>
                <div class="form-row form-group">
                   <div class="col-md-12">
                        <input type="checkbox" name="active" id="active" class="form-control-checkbox">
                        <label for="active">Kích hoạt</label>
                   </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <button class="btn btn-success"> Thêm bình luận</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
<script src="//cdn.ckeditor.com/4.15.0/standard/ckeditor.js"></script>
<script>
    function add_comment(){
        var content=CKEDITOR.instances['content'].getData()
        var _token=$('input[name="_token"]').val();
        var user_id=$('#user_id').val();
        var product_id=$('#product_id').val()
        var active=$('input[name="active"]:checked').val();
        if(active!==undefined){
            active=1;
        }else{
            active=0;
        }
        var formdata= new FormData();
        formdata.append('_token',_token);
        formdata.append('user_id',user_id);
        formdata.append('product_id',product_id);
        formdata.append('content',content);
        formdata.append('active',active)
        $.ajax({
            type: "POST",
            url: "{{url('admin/comment/create')}}",
            data: formdata,
            dataType: "json",
            contentType:false,
            processData:false,
            success: function (response) {
                console.log(response);
                if(!$.isEmptyObject(response.error)){
                    $('.p_error').html('');
                    $.each(response.error,function(index,item){
                        $(`#${index}_error`).html(item)
                    })
                }else{  
                    Swal.fire({
                        icon:'success',
                        title:'Thêm thành công!',
                        text:'Bạn vừa thêm 1 bình luận'
                    }).then(()=>{
                        window.location.href='{{url("admin/comment")}}'
                    });
                }
            }
        });
        return false;
    }
</script>
<script>
    $(document).ready(function () {
        $('#user_id').select2({
        minimumInputLength:3,
        placeholder:'Tìm người dùng',
        ajax:{
            type:'post',
            dataType:'json',
            url:"{{url('api/get_list_user')}}",
            data:function (params){
                return {keyword:params.term}
            },
            processResults:function(data){
                return {
                    results:data
                }
            },
            
            cache:true
        },
        language:{
                searching:function(){
                    return 'Đang tìm người dùng phù hợp';
                },
                inputTooShort: function (e) {
                    var t = e.minimum - e.input.length
                    var n = "Hãy nhập thêm ít nhất " + t + " ký tự  để tìm kiếm";
                    return n
                },
                noResults: function () {
                    return "Không tìm thấy người dùng phù hợp"
                },
                errorLoading: function () {
                    return "Đã xảy ra lỗi, chưa thể tải người dùng."
                }
            },
        })

        $('#product_id').select2({
        minimumInputLength:3,
        placeholder:'Tìm sản phẩm',
        ajax:{
            type:'post',
            dataType:'json',
            url:"{{url('api/get_list_product')}}",
            data:function (params){
                return {keyword:params.term}
            },
            processResults:function(data){
                return {
                    results:data
                }
            },
            
            cache:true
        },
        language:{
                searching:function(){
                    return 'Đang tìm sản phẩm phù hợp';
                },
                inputTooShort: function (e) {
                    var t = e.minimum - e.input.length
                    var n = "Hãy nhập thêm ít nhất " + t + " ký tự  để tìm kiếm";
                    return n
                },
                noResults: function () {
                    return "Không tìm thấy sản phẩm phù hợp"
                },
                errorLoading: function () {
                    return "Đã xảy ra lỗi, chưa thể tải sản phẩm."
                }
            },
        })
    });
    

</script>
@endsection
@section('css')
<style>
    .select2-selection__rendered {
        line-height: 35px !important;
    }
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
    .select2-selection__arrow {
        height: 38px !important;
    }
</style>
@endsection