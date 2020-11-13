@extends('layouts.admin')
@section('body')
<a href="{{url()->previous()}}" class="btn btn-warning mb-2" style="color:white">Quay lại</a>

    <div class="card">
        <div class="card-body">
            <div class="card-title">Sửa số lượng đã nhập của sản phẩm</div>
            <form action="" id="form" method="post" onsubmit="return edit_product_from_store()">
                @csrf
                <div class="form-row">
                    <label for="product_amount"> Màu : <span style="background-color:#{{$data->color}};width:15px;height:15px;display:inline-block"></span> Kích thước : {{$data->size}}</label>
                    <input type="text" value="{{$data->product_amount}}" name="product_amount" id="product_amount" class="form-control">
                    <div id="product_amount_error" class="p_error" style="color:Red"></div>
                </div>
                <div class="form-row mt-2">
                    <button type="submit" class="btn btn-primary">Sửa</button>
                </div>
                
            </form>
        </div>
    </div>
    <script>
        function edit_product_from_store(){
            var data=$('#form').serialize();
            $.ajax({
                type: "post",
                url: "{{url('admin/store/editproduct')}}/{{$data->id}}",
                data: data,
                dataType: "json",
                success: function (response) {
                    if(!$.isEmptyObject(response.error)){
                        $('.p_error').html('');
                        $.each(response.error,function(index,item){
                            $('#'+index+'_error').html(item)
                        });
                    }else{
                        window.location.href='{{url()->previous()}}'
                    }
                }
            });
            return false;
        }
    </script>
@endsection