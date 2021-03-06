@extends('layouts.admin');
@section('js')
<script src="{{asset('p_js/view_setting.js')}}"></script>
@endsection
@section('body')
@if($errors->has('isset'))
    <script>
            Swal.fire({
                icon: 'error',
                title: 'Xóa thất bại',
                text: 'Sản phẩm này đã nhập hàng',
            })
    </script>
@endif
@if(\Session::has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Xóa thành công',
            text: 'Bạn vừa xóa 1 sản phẩm',
        })
    </script>
@endif
<div class="card">
    <div class="card-body">
        <div id="filter_p" style="margin-bottom:15px;">
            <div class="collapse" id="collapseExample">
                <div class="card-title">Filter</div>
                @csrf
                <form class="form-group" action="" onsubmit="return validate_filter()" method="GET" enctype="multipart/form-data"> 
                    <!-- product,category -->
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="product_name" class="">Tên sản phẩm</label>
                                <input name="product_name" id="product_name" value="{{request()->product_name?request()->product_name:''}}"  placeholder="Tên sản phẩm"  type="text" class="form-control">                                   
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="category" class="">Danh mục</label>
                                <input name="category" id="category" placeholder="Đường dẫn sản phẩm" readonly='true' type="text" class="form-control">
                                <div id="category_error" class="p_error"></div>
                                    <!-- process list category selected -->
                                    @php 
                                        $category_selected="";
                                        if(request()->category !== null){
                                            foreach(request()->category as $categories => $category){
                                             $category_selected.=",".$category;
                                            }
                                            $category_selected=ltrim($category_selected,',');
                                        }
                                        
                                       
                                    @endphp
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
                                    var selected= '{{$category_selected}}';
                                    selected=selected.split(',');
                                    var category=$('#category').comboTree({
                                        source : data,
                                        isMultiple: true,
                                        cascadeSelect: true,
                                        collapse: false,
                                        selected:selected
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                    <!-- discount -->
                    <div class="form-row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="discount_type" class="">Giảm giá</label>
                                <select name="discount_type" id="discount_type" class="form-control ">
                                    <option value="">Chọn loại giảm giá</option>
                                    <option @isset(request()->discount_type) 
                                                @if(request()->discount_type==0) selected @endif
                                            @endisset 
                                    value="0">Không giảm giá</option>
                                    <option @isset(request()->discount_type) 
                                                @if(request()->discount_type==1) selected @endif
                                            @endisset 
                                    value="1">Giảm tiền cố định</option>
                                    <option @isset(request()->discount_type) 
                                                @if(request()->discount_type==2) selected @endif
                                            @endisset
                                    value="2">Giảm theo % giá bán</option>
                                </select>
                                <div id="discount_type_error" class="p_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="discount_from_date" class="">Ngày bắt đầu giảm giá</label>
                                <input name="discount_from_date" value="@isset($_GET['discount_from_date']) {{$_GET['discount_from_date']}} @endisset" id="discount_from_date" placeholder="Ngày bắt đầu giảm giá"  readonly='true' type="text" class="form-control">
                                <div id="picker_time_discount_from_date" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                <div id="discount_from_date_error" style="color:red"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="discount_end_date" class="">Ngày kết thúc giảm giá </label>
                                <input name="discount_end_date" value="@isset($_GET['discount_end_date']) {{$_GET['discount_end_date']}} @endisset" id="discount_end_date" placeholder="Ngày kết thúc giảm giá" readonly='true'  type="text" class="form-control">
                                <div id="picker_time_discount_end_date" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                <div id="discount_end_date_error" style="color:red"></div>
                            </div>
                        </div>
                    
                            
                    </div>
                    <!-- color & size -->
                    <div class="form-row">
                        <!-- color -->
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="" >Màu </label>
                                <select id="color" class="select-2 js-example-basic-multiple" style="width:100%" multiple name="color[]">
                                    @foreach($color_search as $key => $value)
                                        <option value="{{$value->color_id}}" data-color="{{$value->color}}"
                                            @if(isset($_GET['color']) && in_array($value->color_id,$_GET['color'])) selected
                                            @endif
                                        >
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                        <!-- size -->
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="" >Kích cỡ</label>
                                <select id="size" class="select-2 js-example-basic-multiple" style="width:100%" multiple name="size[]">
                                    @foreach($size_search as $key => $value)
                                    <option value="{{$value->size_id}}">{{$value->size}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    <!-- time picker -->
                    <div class="form-row">
                        <div class="col-md-3">
                            <div class="position-relative form-group">
                                <label for="create_at_from" class="">Ngày tạo bắt đầu</label>
                                <input name="create_at_from" value="@isset($_GET['create_at_from']) {{$_GET['create_at_from']}} @endisset" id="create_at_from" placeholder="Ngày sửa"  readonly='true' type="text" class="form-control">
                                <div id="picker_time_create_at_from" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                <div id="update_at_error" style="color:red"></div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="position-relative form-group">
                                <label for="create_at_to" class="">Ngày tạo kết thúc</label>
                                <input name="create_at_to" value="@isset($_GET['create_at_to']) {{$_GET['create_at_to']}} @endisset" id="create_at_to" placeholder="Ngày sửa" readonly='true'  type="text" class="form-control">
                                <div id="picker_time_create_at_to" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                <div id="update_at_error" style="color:red"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="position-relative form-group">
                                <label for="update_at_from" class="">Ngày sửa bắt đầu</label>
                                <input name="update_at_from" value="@isset($_GET['update_at_from']) {{$_GET['update_at_from']}} @endisset" id="update_at_from" placeholder="Ngày sửa" readonly='true' type="text" class="form-control">
                                <div id="picker_time_update_at_from" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                <div id="update_at_error" style="color:red"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="position-relative form-group">
                                <label for="update_at_to" class="">Ngày sửa kết thúc</label>
                                <input name="update_at_to" value="@isset($_GET['update_at_to']) {{$_GET['update_at_to']}} @endisset" id="update_at_to" placeholder="Ngày sửa" readonly='true' type="text" class="form-control">
                                <div id="picker_time_update_at_to" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                <div id="update_at_error" style="color:red"></div>
                            </div>
                        </div>
                            
                    </div>
                    <!-- script picker time  -->
                    <script>

                        // datetime picker 
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
                                    },
                                });
                            }
                        }
                        // time picker product
                        var arrInputdatetimePicker=[
                            { inputId:'create_at_from',divId:'picker_time_create_at_from'},
                            { inputId:'create_at_to',divId:'picker_time_create_at_to'},
                            { inputId:'update_at_from',divId:'picker_time_update_at_from'},
                            { inputId:'update_at_to',divId:'picker_time_update_at_to'},
                        ];
                        InterfacePickertime(arrInputdatetimePicker);
                        // discount time picker
                        var arr=[
                            {inputId:'discount_from_date',divId:'picker_time_discount_from_date'},
                            {inputId:'discount_end_date',divId:'picker_time_discount_end_date'}
                        ]
                        InterfacePickertime(arr);
                    
                        // disabled input discount time picker 
                        $('#discount_from_date').prop('disabled',true);
                        $('#discount_end_date').prop('disabled',true);

                        $('#discount_type').change(function(){
                            if($(this).val()!=='0'){
                                $('#discount_from_date').prop('disabled',false);
                                $('#discount_end_date').prop('disabled',false);
                            }else{
                                $('#discount_from_date').prop('disabled',true);
                                $('#discount_end_date').prop('disabled',true);
                            }
                        })

                        //select 2 
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

                            $("#color").select2({
                                templateResult: formatState,
                                templateSelection :formatState,
                                placeholder: "Chọn màu",
                            });
                            $("#size").select2({
                                placeholder: "Chọn kích cỡ",
                            })
                    </script>
                  <div class="form-row form-group">
                      <div class="col-md-12">
                        <button class=" btn btn-primary">Tìm kiếm</button>
                      </div>
                  </div>
                </form>
            </div>
               <!-- button collapse  -->
            <div style="display:flex;justify-content:center;align-items:center">
                <a  data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                    Tìm kiếm
                </a>  
            </div>
            
        </div>
        <!-- end search -->
        <div class="card-title" style="font-size:36px;text-align:center">Danh sách sản phẩm</div>
        <!-- setting view -->
        <div class="">
            @if(p_author('add','tbl_product'))
                <a href="{{url('admin/product/create')}}" class="btn btn-success mb-2">Thêm mới sản phẩm</a>
            @endif
            <div style="float:right" >
                <p style="text-align:right">
                    <a class="" data-toggle="collapse" href="#view" role="button" aria-expanded="false" aria-controls="view">
                        <i class="fas fa-cog"></i> Tùy chọn hiển thị
                    </a>
                </p>
                <div class="collapse col-md-12" id="view">
                    <div class="form-row" style="float:right">
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('product','description'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'product')" name="descriptionn" id="descriptionn" value="description" class="form-control-checkbox view-setting" >
                            <label for="descriptionn">Mô tả</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('product','list_cate'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'product')" name="list_cate" id="list_cate" value="list_cate" class="form-control-checkbox view-setting" >
                            <label for="list_cate">Danh mục</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('product','color'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'product')" name="colorr" id="colorr" value="color" class="form-control-checkbox view-setting" >
                            <label for="colorr">Màu</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('product','size'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'product')" name="sizee" id="sizee" value="size" class="form-control-checkbox view-setting" >
                            <label for="sizee">Kích cỡ</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('product','price'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'product')" name="pricee" id="pricee" value="price" class="form-control-checkbox view-setting" >
                            <label for="pricee">Giá sản phẩm</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('product','discount'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'product')" name="discountt" id="discountt" value="discount" class="form-control-checkbox view-setting" >
                            <label for="discountt">Đang giảm giá</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('product','active'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'product')" name="activee" id="activee" value="active" class="form-control-checkbox view-setting" >
                            <label for="activee">Kích hoạt</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('product','detail'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'product')" name="detaile" id="detaile" value="detail" class="form-control-checkbox view-setting" >
                            <label for="detaile">Chi tiết</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('product','action'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'product')" name="action" id="action" value="action" class="form-control-checkbox view-setting" >
                            <label for="action">Thao tác</label>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div> 
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th >Tên sản phẩm</th>
                    <th class="p_setting description" @if(!p_ui_setting('product','description')) style="display: none;" @endif> Mô tả</th>
                    <th class="p_setting list_cate" @if(!p_ui_setting('product','list_cate')) style="display: none;" @endif>Danh mục</th>
                    <th class="p_setting color" @if(!p_ui_setting('product','color')) style="display: none;" @endif>Màu</th>
                    <th class="p_setting size" @if(!p_ui_setting('product','size')) style="display: none;" @endif>Kích cỡ</th>
                    <th class="p_setting price" @if(!p_ui_setting('product','price')) style="display: none;" @endif>Giá sản phẩm</th>
                    <th class="p_setting discount" @if(!p_ui_setting('product','discount')) style="display: none;" @endif>Đang giảm giá</th>
                    <th class="p_setting active" @if(!p_ui_setting('product','active')) style="display: none;" @endif>Kích hoạt</th>
                    <th class="p_setting detail" @if(!p_ui_setting('product','detail')) style="display: none;" @endif>Chi tiết</th>
                    <th class="p_setting action" @if(!p_ui_setting('product','action')) style="display: none;" @endif>Thao tác</th>
                </tr>
            </thead>
            <tbody id="p_list_product">
                @foreach($list_product as $key =>$value)
                    <tr>
                        
                        <td >{{$value->product_name}}</td>
                        <td class="p_setting description" @if(!p_ui_setting('product','description')) style="display: none;" @endif>  {!!Str::limit($value->description,50)!!}</td>
                        <td  class="p_setting list_cate" @if(!p_ui_setting('product','list_cate')) style="display: none;" @endif>
                            @php 
                                $categoryText='';
                            @endphp
                            @foreach($value->category as $categories => $category)
                                @php 
                                    $categoryText.=$category->category_name.','
                                @endphp
                            @endforeach
                            @if(p_author('edit','tbl_product'))
                                <a href="{{url('admin/product')}}/{{$value->product_id}}/edit" class="href">{{Str::limit(rtrim($categoryText,','),30)}}   </a> 
                            @else
                                {{Str::limit(rtrim($categoryText,','),30)}}   
                            @endif
                        </td>
                        <td class="p_setting color" @if(!p_ui_setting('product','color')) style="display: none;" @endif>
                            @foreach ($value->colors as $colors => $color)
                                <div style="width:15px;height:15px;display:inline-block;background-color:#{{$color}}"></div>
                            @endforeach
                        </td>
                        <td class="p_setting size" @if(!p_ui_setting('product','size')) style="display: none;" @endif>
                            @foreach ($value->sizes as $sizes => $size)
                                <p style="display:inline-block;margin:0; margin-right:5px;">{{$size}}</p>
                            @endforeach
                        </td>
                        <td class="p_setting price" @if(!p_ui_setting('product','price')) style="display: none;" @endif style="text-align:right">{{number_format($value->product_price)}} VND</td>
                        <td class="p_setting discount" @if(!p_ui_setting('product','discount')) style="display: none;" @endif>{{$value->discount}}</td>
                        
                        <td class="p_setting active" @if(!p_ui_setting('product','active')) style="display: none;" @endif>
                            @if(p_author('active','tbl_product'))
                                @if($value->active==1) 
                                    <a href="#" class="p_product_active" data-id="{{$value->product_id}}"><i class="fa fa-check" style="color:green" aria-hidden="true"></i></a>
                                @else 
                                    <a href="#" class="p_product_active" data-id="{{$value->product_id}}"><i class="fas fa-times" style="color:#b81f44"></i></a>
                                @endif
                              
                            @else
                                @if($value->active==1) 
                                    <i class="fa fa-check" style="color:green" aria-hidden="true"></i>
                                @else 
                                    <i class="fas fa-times" style="color:#b81f44"></i> 
                                @endif
                            @endif
                        </td>
                        <td class="p_setting detail" @if(!p_ui_setting('product','detail')) style="display: none;" @endif>
                            <a href="{{url('admin/product')}}/{{$value->product_id}}/detail" class="btn btn-info">Chi tiết</a>
                        </td>
                        <td class="p_setting action" @if(!p_ui_setting('product','action')) style="display: none;" @endif>
                            @if(p_author('edit','tbl_product'))
                                <a href="{{url('admin/product')}}/{{$value->product_id}}/edit" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                            @endif
                            @if(p_author('delete','tbl_product'))
                                <form action="{{url('admin/product')}}/{{$value->product_id}}" style="display:inline-block;margin:0" method="post">
                                    @csrf
                                    @method('DELETE')
                                <button type="submit" class="btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
            <tr>
                    <th >Tên sản phẩm</th>
                    <th class="p_setting description" @if(!p_ui_setting('product','description')) style="display: none;" @endif> Mô tả</th>
                    <th class="p_setting list_cate" @if(!p_ui_setting('product','list_cate')) style="display: none;" @endif>Danh mục</th>
                    <th class="p_setting color" @if(!p_ui_setting('product','color')) style="display: none;" @endif>Màu</th>
                    <th class="p_setting size" @if(!p_ui_setting('product','size')) style="display: none;" @endif>Kích cỡ</th>
                    <th class="p_setting price" @if(!p_ui_setting('product','price')) style="display: none;" @endif>Giá sản phẩm</th>
                    <th class="p_setting discount" @if(!p_ui_setting('product','discount')) style="display: none;" @endif>Đang giảm giá</th>
                    <th class="p_setting active" @if(!p_ui_setting('product','active')) style="display: none;" @endif>Kích hoạt</th>
                    <th class="p_setting detail" @if(!p_ui_setting('product','detail')) style="display: none;" @endif>Chi tiết</th>
                    <th class="p_setting action" @if(!p_ui_setting('product','action')) style="display: none;" @endif>Thao tác</th>
                </tr>
            </tfoot>
        </table>
    </div>
            <div  style="display:flex;justify-content:center">
                {{$list_product->appends(request()->all())->links()}}
            </div>
            <div class="toast">
                <div class="toast-header">
                    Toast Header
                </div>
                <div class="toast-body">
                    Some text inside the toast body
                </div>
            </div>
</div>
@if(p_author('active','tbl_product'))
    <script>
        $('.p_product_active').unbind().click(function(){
            var id=$(this).attr('data-id');
            var element=$(this);
            $.ajax({
                type: "POST",
                url: "{{url('api/product/active')}}",
                data: {id:id},
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if(!$.isEmptyObject(response.error)){

                    }else{
                        if(response.success==1){
                            var html='<i class="fa fa-check" style="color:green" aria-hidden="true"></i>'        
                        }else{
                            var html='<i class="fas fa-times" style="color:#b81f44"></i>'
                        }
                        element.html(html)
                    }
                }
            });
        })
    </script>
@endif
<script>
    function validate_filter(){
        var value_category=category.getSelectedIds();
        $('#category').val(value_category);
    }
</script>
 

@endsection