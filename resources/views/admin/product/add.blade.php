@extends('layouts.admin')
@section('body')
<a href="{{url()->previous()}}" class="btn btn-warning mb-2" style="color:white">Quay lại</a>
<div class="main-card mb-3 card">
    <div class="card-body"><h5 class="card-title" style="font-size:36px;text-align:center">Thêm mới sản phẩm</h5>
        <form class="" enctype="multipart/form-data" onsubmit="return add_product()">
        @csrf
        <div class="form-row">
            <!-- name product -->
            <div class="col-md-4">
                <div class="position-relative form-group">
                    <label for="product_name" class="">Tên sản phẩm <span style="color:Red"> *</span></label>
                    <input name="product_name"  onkeyup="convert_vn_to_en()" id="product_name" placeholder="Tên sản phẩm" type="text" class="form-control">
                    <div id="product_name_error" class="p_error"></div>    
                </div>
            </div>
            <!-- slug -->
            <div class="col-md-4">
                <div class="position-relative form-group">
                    <label for="product_slug" class="">Đường dẫn sản phẩm <span style="color:Red"> *</span></label>
                    <input name="product_slug" id="product_slug" placeholder="Đường dẫn sản phẩm" type="text" class="form-control">
                    <div id="product_slug_error" class="p_error"></div>
                </div>
            </div>
            <!-- category  -->
            <div class="col-md-4">
                <div class="position-relative form-group">
                    <label for="category" class="">Danh mục <span style="color:Red"> *</span></label>
                    <input name="category" id="category" placeholder="Đường dẫn sản phẩm" readonly='true' type="text" class="form-control">
                    <div id="category_error" class="p_error"></div>
                    <script>
                        var data=[];
                        $.ajax({
                            type: "post",
                            url: "{{url('category/tree_category/select/0')}}",
                            data: {_token:$('input[name="_token"]').val()},
                            dataType: "json",
                            async:false,
                            success: function (response) {
                                if(!$.isEmptyObject(response.error)){
                                    console.log(response.error);
                                }else{
                                    data=response
                                }
                            }
                        });
                        console.log(data);
                        var category=$('#category').comboTree({
                            source : data,
                            isMultiple: true,
                            cascadeSelect: true,
                            collapse: false
                        });
                    </script>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-12">
            <label for="product_slug" class="">Mô tả sản phẩm <span style="color:Red"> *</span></label>
                <script src="//cdn.ckeditor.com/4.15.0/standard/ckeditor.js"></script>
                <textarea name="description" id="description" cols="30" rows="10">

                </textarea>
                <script>
                    CKEDITOR.replace('description', {
                        
                        filebrowserBrowseUrl:'',
                        filebrowserUploadUrl:'{{url("ckeditor/upload/image")}}',
                        // filebrowserUploadMethod:"form"
                    });
                </script>
                    <div id="description_error" class="p_error"></div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-3">
                <div class="position-relative form-group">
                    <label for="discount_type" class="">Giảm giá</label>
                    <select name="discount_type" id="discount_type" class="form-control ">
                        <option value="0">Không giảm giá</option>
                        <option value="1">Giảm tiền cố định</option>
                        <option value="2">Giảm theo % giá bán</option>
                    </select>
                    <div id="discount_type_error" class="p_error"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="position-relative form-group">
                    <label for="discount_amount" class="">Số tiền giảm</label>
                    <input name="discount_amount" style="text-align:right" disabled id="discount_amount" type="text" class="form-control">
                    <div id="discount_amount_error" class="p_error"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="position-relative form-group">
                    <label for="discount_from_date" class="">Ngày bắt đầu giảm</label>
                    <input name="discount_from_date" disabled value="@isset($_GET['discount_from_date']) {{$_GET['discount_from_date']}} @endisset" id="discount_from_date" placeholder="Ngày sửa"  readonly='true' type="text" class="form-control">
                    <div id="picker_time_discount_from_date"   style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                    <div id="discount_from_date_error" class="p_error" style="color:red"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="position-relative form-group">
                    <label for="discount_end_date" class="">Ngày kết thúc giảm</label>
                    <input name="discount_end_date" disabled value="@isset($_GET['discount_end_date']) {{$_GET['discount_end_date']}} @endisset" id="discount_end_date" placeholder="Ngày sửa" readonly='true'  type="text" class="form-control">
                    <div id="picker_time_discount_end_date" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                    <div id="discount_end_date_error"  style="color:red"></div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <!-- price -->
            <div class="col-md-3">
                <label for="product_price">Giá sản phẩm <span style="color:Red">*</span></label>
                <input type="text" name="product_price"  style="text-align:right" id="product_price" placeholder="Giá sản phẩm" class="form-control">
                <div id="product_price_error" class="p_error"></div>
            </div>
            <!-- img -->
            <div class="col-md-3">
                <label for="">Ảnh <span style="color:Red"> *</span></label>
                <div class="custom-file">
                    <input type="file" name="image[]" multiple class="custom-file-input" id="image" >
                    <label class="custom-file-label" for="image">Chọn ảnh...</label>
                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                </div>
                <div id="image_error" class="p_error"></div>
            </div>
            <!-- color -->
            <div class="col-md-3">
                <label for="color">Màu <span style="color:Red">*</span></label>
                <select class="select-2 js-example-basic-multiple form-control" id="color" style="width:100%" multiple name="color[]">
                    @foreach($color as $key =>$value)
                        <option value="{{$value->color_id}}" data-color="{{$value->color}}"></option>
                    @endforeach
                    <script>
                        function formatState (state) {
                            if (!state.id) {
                                return state.text;
                            }
                            var color=$(state.element).attr('data-color');
                            var $state = $(
                            '<span><div style="width:15px;height:15px;background-color:#'+color+';display:inline-block"></div></span>'
                            );
                            return $state;
                        };

                            $(".select-2").select2({
                                templateResult: formatState,
                                templateSelection :formatState,
                                placeholder: "Chọn màu",

                            });
                    </script>
                </select>
                <div id="color_error"  class="p_error"></div>

            </div>
            <div class="col-md-3">
                <label for="size">Kích cỡ <span style="color:Red">*</span></label>
                <select class="select-2-size js-example-basic-multiple form-control" id="size" style="width:100%" multiple name="size[]">
                    @foreach($size as $key =>$value)
                        <option value="{{$value->size_id}}" >{{$value->size}}</option>
                    @endforeach
                    <script>
                            $(".select-2-size").select2({
                                placeholder: "Chọn kích cỡ",
                            });
                    </script>
                </select>
                <div id="size_error"  class="p_error"></div>
            </div>
        </div>
        <!-- active -->
        <div class="form-row" style="margin-top:10px;">
            <div class="col-md-12">
                <div class="position-relative form-group ">    
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="active" id="active" value="1" >
                            Kích hoạt
                        </label>
                    </div>
                </div>
            </div>
        </div>
             
            <!-- <div class="position-relative form-check">
                <input name="check" id="exampleCheck" type="checkbox" class="form-check-input">
                <label for="exampleCheck" class="form-check-label">Check me out</label>
            </div> -->
         <div class="form-row form-group">
             <div class="col-md-12">
                 
                <button class=" btn btn-success">Thêm mới sản phẩm</button>
             </div>
         </div>                   
        
        </form>
    </div>
</div>

<script>
    function add_product(){
        var _token=$('input[name="_token"]').val();
        var product_name=$('#product_name').val();
        var product_slug=$('#product_slug').val();
        var description=CKEDITOR.instances['description'].getData();
        var discount_type=$('#discount_type').val();
        var discount_amount=$('#discount_amount').val();
        var discount_from_date=$('#discount_from_date').val();
        var discount_end_date=$('#discount_end_date').val();
        var active =$('input[name="active"]:checked').val()
        var image= $('#image').prop('files');
        var color=$('#color').val();
        var size=$('#size').val();
        var price= $('#product_price').val();
        var formdata= new FormData();
        formdata.append('_token',_token);
        formdata.append('product_name',product_name);
        formdata.append('product_slug',product_slug);
        formdata.append('description',description);
        formdata.append('discount_type',discount_type);
        formdata.append('product_price',price)
        if(discount_type!=0){
            formdata.append('discount_amount',discount_amount);
            formdata.append('discount_from_date',discount_from_date);
            formdata.append('discount_end_date',discount_end_date);
        }
        for (let i = 0; i < color.length; i++) {
            formdata.append('color[]',color[i]);
        }
        for (let i = 0; i < size.length; i++) {
            formdata.append('size[]',size[i]);
        }
        if(active==undefined) active=0
        formdata.append('active',active);
        for (let i = 0; i < image.length; i++) {
            formdata.append('image[]',image[i]);
        }
        var value_category=category.getSelectedIds();
        if(value_category!==null) formdata.append('category[]',value_category);

        $.ajax({
            type: "post",
            url: "{{url('admin/product')}}",
            data: formdata,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!$.isEmptyObject(response.error)){
                    $('.p_error').html('')
                $.each(response.error,function(index,item){
                    $('#'+index+'_error').html(item);
                })
                }else{
                    
                    Swal.fire({
                            icon: 'success',
                            title: 'Thêm sản phẩm',
                            text: 'Thêm sản phẩm thành công',
                    }).then(()=>{
                        window.location.href="{{url('admin/product')}}";
                    })
                }
            }
        });
        return false;
    }
        function convert_vn_to_en(){
            console.log('keyup');
            var oldtext=$('#product_name').val().trim();
            var newtext = oldtext.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
                newtext = newtext.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, 'A');
                newtext = newtext.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
                newtext = newtext.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, 'E');
                newtext = newtext.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
                newtext = newtext.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, 'I');
                newtext = newtext.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,'o');
                newtext = newtext.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g,'O');
                newtext = newtext.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
                newtext = newtext.replace(/Ù|ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, 'U');
                newtext = newtext.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,'y');
                newtext = newtext.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g,'Y');
                newtext = newtext.replace(/đ/g, 'd');
                newtext = newtext.replace(/Đ/g, 'D');
                newtext = newtext.replace(/\s/g, '-');                    
            $('#product_slug').val(newtext)
        }
</script>
<style>
    .p_error{
        color:red;
    }
</style>
<!-- script time picker -->
    <script>
        function InterfacePickertime(input=[]){
            for (let i = 0; i < input.length; i++) {
                $('#'+input[i].divId).slideUp();
                $('#'+input[i].inputId).focus(function(){
                    $('#'+input[i].divId).slideDown();
                    
                })
                $('#'+input[i].divId).datetimepicker({
                    date: new Date(),
                    startDate:null,
                    endDate: null,
                    viewMode: 'YMDHMS',
                    onDateChange: function(){
                        $('#'+input[i].inputId).val(this.getText());
                    },
                    onOk:function(){
                        $('#'+input[i].divId).slideUp();
                    }
                });
            }
        }
        var arrInputdatetimePicker=[
            { inputId:'discount_from_date',divId:'picker_time_discount_from_date'},
            { inputId:'discount_end_date',divId:'picker_time_discount_end_date'},
        
        ];
        InterfacePickertime(arrInputdatetimePicker);

        //open input discount_amount,discount_date
        $('#discount_type').change(function(){
            if($(this).val()!=='0') {
                $('#discount_amount').prop('disabled',false)
                $('#discount_from_date').prop('disabled',false)
                $('#discount_end_date').prop('disabled',false)
            }else{
                $('#discount_amount').prop('disabled',true)
                $('#discount_from_date').prop('disabled',true)
                $('#discount_end_date').prop('disabled',true)
            }
        })
    </script>
@endsection