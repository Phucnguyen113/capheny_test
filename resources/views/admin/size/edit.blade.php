@extends('layouts.admin')
@section('body')
<a href="{{url()->previous()}}" class="btn btn-warning mb-2" style="color:white">Quay lại</a>

<div class="main-card mb-3 card">
    <div class="card-body"><h5 class="card-title" style="font-size:36px;text-align:center">Chỉnh sửa kích cỡ</h5>
        <form class="" onsubmit="return edit_size()">
            @csrf
                <div class="position-relative form-row form-group">
                    <label for="size" >Kích cỡ <span style="color:red"> *</span></label>
                    <input type="text" name="size" value="{{$size->size}}" id="size" class="form-control" placeholder="Kích cỡ">
                    <div id="size_error" class="error_p" style="color:Red"></div>
                </div>
                <div class="form-row form-group">
                    
                    <button class="  btn btn-primary" type="submit">Sửa</button>
                </div>
                    
               
                <script>
                    function edit_size(){
                        var size=$('#size').val();
                        var _token=$('input[name="_token"]').val();
                        $.ajax({
                            type: "put",
                            url: "{{url('admin/size')}}/{{$size->size_id}}",
                            data: {_token:_token,size:size},
                            dataType: "json",
                            success: function (response) {
                                if($.isEmptyObject(response.error)){
                                    window.location.href="{{url('admin/size')}}"
                                }else{
                                    console.log(response.error);
                                    $('.error_p').html('');
                                    $.each(response.error,function(index,item){
                                        $('#'+index+'_error').html(item)
                                    })
                                }
                            }
                        });
                        return false;
                    }
                </script>
        </form>
    </div>
</div>

@endsection