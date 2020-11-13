@extends('layouts.admin')
@section('body')
<a href="{{url()->previous()}}" style="color:white" class="btn btn-warning mb-2">Quay lại</a>
<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title" style="text-align:center;font-size:36px">Thêm mới cửa hàng</h5>
        <form class="" onsubmit="return add_store()" method="post" id="formStore">
            @csrf
            <div class=" form-row form-group">
                <div class="col-md-12">
                    <label for="size" >Tên cửa hàng <span style="color:red"> *</span></label>
                    <input type="text" value="{{old('store_name')}}" name="store_name" id="size" class="form-control" placeholder="Tên cửa hàng">
                    <div id="store_name_error" class="error_p" style="color:Red">
                        @if($errors->has('store_name')) {{$errors->first('store_name')}} @endif
                    </div>
                </div>
            </div>
            <div class="form-row form-group">
                <div class="col-md-4">
                    <label for="province">Thànhphố/Tỉnh <span style="color:red">*</span></label>
                    <select name="province" class="form-control" id="province">
                        <option value="0">Chọn thành phố, tỉnh</option>
                        @foreach ($list_province as $provinces => $province)
                            <option value="{{$province->id}}">{{$province->_name}}</option>
                        @endforeach
                    </select>
                    <!-- script get district -->
                    <script>
                        $('#province').change(function(){
                            if($(this).val()==0){
                                $('#district').html('<option value="0">Chọn quận, huyện</option>');
                                $('#ward').html('<option value="0">Chọn khu vực</option>');
                            }else{
                                $.ajax({
                                type: "post",
                                url: "{{url('api/district/get')}}/"+$(this).val(),
                                data: {},
                                dataType: "json",
                                success: function (response) {
                                    if(!$.isEmptyObject(response.error)){
                                        $('#district').html('<option value="0">Chọn quận, huyện</option>');
                                        $('#ward').html('<option value="0">Chọn khu vực</option>');
                                    }else{
                                        $('#district').html('<option value="0">Chọn quận, huyện</option>');
                                        $('#ward').html('<option value="0">Chọn khu vực</option>');
                                        $.each(response.data,function(index,item){
                                            var district='<option value="'+item.id+'">'+item._name+'</option>'
                                            $('#district').append(district);
                                        })
                                    }
                                }
                            });
                            }
                        })
                    </script>
                    <div id="province_error" class="error_p" style="color:Red">
                        @if($errors->has('province')) {{$errors->first('province')}} @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="district">Quận/ Huyện <span style="color:red">*</span></label>
                    <select name="district" class="form-control" id="district">
                        <option value="0">Chọn quận, huyện</option>
                    </select>
                    <!-- script get ward -->
                    <script>
                        $('#district').change(function(){
                            if($(this).val()==0){
                                $('#ward').html('<option value="0">Chọn khu vực </option>');
                            }else{
                                $.ajax({
                                type: "post",
                                url: "{{url('api/ward/get')}}/"+$(this).val(),
                                data: {},
                                dataType: "json",
                                success: function (response) {
                                    if(!$.isEmptyObject(response.error)){

                                    }else{
                                        $('#ward').html('<option value="0">Chọn khu vực </option>');
                                        $.each(response.data,function(index,item){
                                            var ward='<option value="'+item.id+'">'+item._name+'</option>'
                                            $('#ward').append(ward);
                                        })
                                    }
                                }
                            });
                            }
                            
                        })
                    </script>
                    <div id="district_error" class="error_p" style="color:Red">
                        @if($errors->has('district')) {{$errors->first('district')}} @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="ward">Khu vực <span style="color:red">*</span></label>
                    <select name="ward" class="form-control" id="ward">
                        <option value="0">Chọn khu vực</option>
                    </select>
                    <div id="ward_error" class="error_p" style="color:Red">
                        @if($errors->has('ward')) {{$errors->first('ward')}} @endif
                    </div>
                </div>
            </div>
            <div class="form-row form-group">
                <div class="col-md-12">
                    <label for="store_address" >Địa chỉ <span style="color:red"> *</span></label>
                    <input type="text" name="store_address" id="store_address" class="form-control" placeholder="Tên cửa hàng">
                    <div id="store_address_error" class="error_p" style="color:Red"></div>
                </div>
            </div>
            <div class="form-row form-group">
                <div class="col-md-12">
                   
                    <button class="  btn btn-success" type="submit">Thêm cửa hàng</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    function add_store(){
        
        $.ajax({
            type: "post",
            url: "{{url('admin/store/create')}}",
            data: $('#formStore').serialize(),
            dataType: "json",
            success: function (response) {
                if(!$.isEmptyObject(response.error)){
                    $('.p_error').html('');
                    $.each(response.error,function(index,item){
                        $('#'+index+"_error").html(item);
                    });
                }else{
                    window.location.href='{{url("admin/store")}}';
                }
            }
        });
        return false;
    }
</script>
@endsection