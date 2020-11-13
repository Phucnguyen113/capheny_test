@extends('layouts.admin')
@section('body')
<a href="{{url()->previous()}}" class="btn btn-warning mb-2" style="color:white">Quay lại</a>

<div class="main-card mb-3 card">
    <div class="card-body"><h5 class="card-title" style="text-align:center;font-size:36px">Thêm mới màu sản phẩm</h5>
        <form class="" onsubmit="return add_color()">
            @csrf
                <div class="position-relative form-row form-group">
                    <div class="col-md-12">
                        <label for="color" >Mã màu <span style="color:Red"> *</span></label>
                        <input type="text" name="color" id="color" class="form-control" placeholder="Mã màu">
                        <div id="color_error" class="error_p" style="color:Red"></div>
                    </div>
                </div>
                
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <button class=" btn btn-success" type="submit">Thêm màu</button>
                    </div>
                </div>
               
                <script>
                    function add_color(){
                        var color=$('#color').val();
                        var _token=$('input[name="_token"]').val();
                        $.ajax({
                            type: "post",
                            url: "{{url('admin/color')}}",
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