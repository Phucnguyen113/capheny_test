@extends('layouts.admin')
@section('body')
<a href="{{url()->previous()}}" class="btn btn-warning mb-2" style="color:white">Quay lại</a>

<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title" style="font-size:36px;text-align:center">Chỉnh sửa màu sản phẩm</h5>
        <form class="" onsubmit="return edit_color()">
            @csrf
                <div class="position-relative form-row form-group">
                    <div class="col-md-12">
                        <label for="size" >Mã màu <span style="color:red"> *</span></label>
                        <input type="text" name="color" value="{{$color->color}}" id="color" class="form-control" placeholder="Mã màu">
                        <div id="color_error" class="error_p" style="color:Red"></div>
                    </div>
                </div>
        
                <div class="form-group form-row">
                    <div class="col-md-12">
                        <button class=" btn btn-primary" type="submit">Edit Color</button>
                    </div>
                </div>
               
                <script>
                    function edit_color(){
                        var color=$('#color').val();
                        var _token=$('input[name="_token"]').val();
                        $.ajax({
                            type: "put",
                            url: "{{url('admin/color')}}/{{$color->color_id}}",
                            data: {_token:_token,color:color},
                            dataType: "json",
                            success: function (response) {
                                if($.isEmptyObject(response.error)){
                                    window.location.href="{{url('admin/color')}}"
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