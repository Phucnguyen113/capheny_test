@extends('layouts.admin')
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
@section('js')
<script src="//cdn.ckeditor.com/4.15.0/standard/ckeditor.js"></script>
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
<script>
    function add_comment(){
        var data=$('#form').serialize();
        $.ajax({
            type: "POST",
            url: "{{url('admin/comment')}}/{{$comment->comment_id}}/edit",
            data: data,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!$.isEmptyObject(response.error)){
                    $('.p_error').html('');
                    $.each(response.error,function(index,item){
                        $(`#${index}_error`).html(item);
                    })
                }else{
                    Swal.fire({
                        icon:'success',
                        title:'Sửa thành công!',
                        text:'Bạn vừa chỉnh sửa 1 bình luận'
                    }).then(()=>{
                        window.location.href="{{url('admin/comment')}}"
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
            <div class="card-title" style="font-size:36px;text-align:center"> Sửa bình luận</div>
            <form action="" method="post" onsubmit="return add_comment()" id="form">
                @csrf
                <div class="form-row form-group">
                    <div class="col-md-6">
                        <label for="user_id">Người dùng <span style="color:Red"> *</span></label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="{{$user->user_id}}">{{$user->user_email}}</option>

                        </select>
                        <div id="user_id_error" class="p_error" style="color:red"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="product_id">Sản phẩm <span style="color:Red"> *</span></label>
                        <select name="product_id" id="product_id" class="form-control">
                            <option value="{{$product->product_id}}">{{$product->product_name}}</option>
                           
                        </select>
                        <div id="product_id_error" class="p_error" style="color:red"></div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <label for="content">Nội dụng bình luận <span style="color:Red"> *</span></label>
                        <textarea name="content" id="content" cols="30" rows="10" class="form-control" >{!!$comment->content!!}</textarea>
                        <script>
                            CKEDITOR.replace('content')
                        </script>
                        <div id="content_error" class="p_error" style="color:red"></div>
                    </div>
                </div>
                
                <div class="form-row form-group">
                   <div class="col-md-12">
                        <input type="checkbox" name="active" id="active" @if($comment->active==1) checked @endif class="form-control-checkbox">
                        <label for="active">Kích hoạt</label>
                   </div>
                </div>
                
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <button class="btn btn-primary"> Sửa bình luận</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection