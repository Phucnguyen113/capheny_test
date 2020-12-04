@extends('layouts.admin')
@section('body')
    <div class="card">
        <div class="card-body">
            <div class="card-title" style="text-align:center;font-size:36px">Nhập sản phẩm về cửa hàng</div>
            <form action=""  onsubmit="return submit_()" method="post" id="addproduct">
                @csrf
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="store">Cửa hàng <span style="color:red"> *</span></label>
                        <select name="store[]" multiple id="store" class="form-control store-select">
                            
                            @foreach($list_store as $stores => $store)
                                <option value="{{$store->store_id}}">{{$store->store_name}}</option>
                            @endforeach
                            
                        </select>
                        <div class="p_error" id="store_error" style="color:red"></div>
                        <script>
                             $(".store-select").select2({})
                        </script>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="product">Sản phẩm <span style="color:red"> *</span></label>
                        <select name="product" id="product" class="form-control">
                            <option value="">Chọn sản phẩm</option>
                            @foreach($list_product as $products => $product)
                                @php 
                                    $old_product=(old('product'))?old('product'):[];
                                @endphp
                                <option @if(in_array($product->product_id,$old_product)) selected @endif value="{{$product->product_id}}">{{$product->product_name}}</option>
                            @endforeach
                        </select>
                       <div class="p_error" id="product_error" style="color:red"></div>
                        <!-- script get color& size product -->
                        <script>
                                $('#product').change(function(){
                                    if($(this).val()==null || $(this).val()==""){
                                        $('#body_add_product').html('')
                                    }else{
                                        $.ajax({
                                            type: "post",
                                            url: "{{url('api/product/get_size_color')}}/"+$(this).val(),
                                            data: {},
                                            dataType: "json",
                                            success: function (response) {
                                                color = response.data.color;
                                                size  = response.data.size;
                                                $('#body_add_product').html('')
                                                for (let i = 0; i < color.length; i++) {
                                                   for (let j = 0; j < size.length; j++) {
                                                       var html='<div class="col-md-3 form-group" name="product_amount[]">';
                                                            html+=  '<label for="'+color[i].color+size[j].size+'">'
                                                            html+=  ' Màu : <span style="width:15px;height:15px;background-color:#'+color[i].color+';display:inline-block;margin-right:10px"></span>'
                                                            html+=  'Size : '+size[j].size;
                                                            html+=  '</label>'
                                                            html+=  '<input type="text" id="'+color[i].color+size[j].size+'" name="product_amount[]" class="form-control" placeholder="Số lượng">'
                                                            html+= '<div class="p_error" id="product_amount'+j+'_error" style="color:red"></div>'
                                                            html+='</div>'
                                                            $('#body_add_product').append(html);
                                                       
                                                   }
                                                    
                                                }
                                            },
                                            error: function(error){
                                                console.log(error);
                                            }
                                        });
                                    }
                                })
                        </script>
                    </div>
                   
                </div>
                <div class="form-row " id="body_add_product">
                    
                </div>
                <div class="form-row ">
                    <div class="col-md-12">
                        <div id="product_amount0_error" class=""></div>
                    </div>
                    
                </div>
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <a href="{{url()->previous()}}" style="color:white" class="btn btn-warning">Quay lại</a>
                        <button type="submit" class="btn btn-success">Nhập kho</button>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
    <script>
            function submit_(){
                var data=$('#addproduct').serialize();
                Swal.fire ({
                    title: 'Xin chờ...',
                    onBeforeOpen: () => {
                        Swal.showLoading ()
                    }
                    ,allowEscapeKey: false,
                    allowOutsideClick: false,
                    showCloseButton:false,
                    showCancelButton:false,
                    showConfirmButton:false,
                })
                $.ajax({
                    type: "post",
                    url: "{{url('admin/store/addproduct')}}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if(!$.isEmptyObject(response.error)){
                            $('.p_error').html('')
                            $.each(response.error,function(index,item){
                                var str=index.replace('\.','');
                                $('#'+str+'_error').html(item)
                            })
                            Swal.close();
                            
                        }else{
                            Swal.fire({
                                icon:'success',
                                title:'Thêm thành công!',
                                text:'Bạn vừa nhập sản phẩm về 1 cửa hàng'
                            }).then(()=>{
                                window.location.href="{{url('admin/store')}}";
                            })
                           
                        }
                    }
                });
               
                return false;
            }
    </script>
@endsection