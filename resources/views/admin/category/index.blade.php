
@extends('layouts.admin')
@section('js')
<script src="{{asset('p_js/view_setting.js')}}"></script>
@endsection
@section('body')
<!-- Button trigger modal -->
@if($errors->has('error'))
<script>
    Swal.fire({
        icon:'error',
        title:'Xóa thất bại!',
        text:'Danh mục có danh mục con'
    })
</script>
@elseif($errors->has('error_sp'))
<script>
    Swal.fire({
        icon:'error',
        title:'Xóa thất bại!',
        text:'Danh mục có sản phẩm con'
    })
</script>
@endif
@if(\Session::has('success'))
<script>
    Swal.fire({
        icon:'success',
        title:'Xóa thành công!',
        text:'Bạn vừa xóa 1 danh mục'
    })
</script>
@endif
<div class="main-card card">
    <div class="card-body">
        <div id="filter_p" style="margin-bottom:15px;">
            <div class="collapse" id="collapseExample">     
                    <form class="form-group" action=""  method="GET" enctype="multipart/form-data"> 
                        <input type="hidden" name="search" value="true">
                        <div class="row ">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label for="category_name" class="">Tên danh mục</label>
                                    <input name="category_name" value="{{isset($_GET['category_name']) ?$_GET['category_name']:''}} " id="category_name" placeholder="Nhập tên danh mục cần tìm ở đây" type="text" class="form-control">
                                    <div id="category_name_error" style="color:red"></div>
                                </div>
                            </div>
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
                                            }
                                        });
                                    }
                                }
                                var arrInputdatetimePicker=[
                                    { inputId:'create_at_from',divId:'picker_time_create_at_from'},
                                    { inputId:'create_at_to',divId:'picker_time_create_at_to'},
                                    { inputId:'update_at_from',divId:'picker_time_update_at_from'},
                                    { inputId:'update_at_to',divId:'picker_time_update_at_to'},
                                ];
                                InterfacePickertime(arrInputdatetimePicker);
                            </script>
                            <!-- <div class="col-md-4">
                                <div class="card-title">Category parent</div>
                                <div class="position-relative form-group">
                                    
                                        <div class="form-check">
                                            <input type="radio" checked value="0" name="category_parent_id" id="category_parent_id0">
                                            <label for="category_parent_id0"  class="">No parent</label>
                                        </div>
                                
                                    @foreach($cate_tree as $key =>$value)
                                
                                        <div class="form-check"  @if($value->level!==0) style="margin-left:{{($value->level==1)?$value->level+1:$value->level+2}}0px" @endif>
                                            <input type="radio"  value="{{$value->category_id}}" name="category_parent_id" id="category_parent_id{{$value->category_id}}">
                                            <label for="category_parent_id{{$value->category_id}}" class="">{{$value->category_name}}</label>
                                        </div>
                                    
                                    @endforeach
                                </div>
                            </div> -->
                        </div>
                        <div class="form-group">
                            <button class=" btn btn-primary">Tìm kiếm</button>
                        </div>
                        
                    </form>
                
            </div>

            <div style="display:flex;justify-content:center;align-items:center">
                    <a  data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Tìm kiếm
                    </a>  
            </div>
         
        </div>
        <!-- end search -->
        <h5 class="card-title" style="font-size:36px;text-align:center">Danh sách danh mục</h5>
       
        <div class="">
            @if(p_author('add','tbl_category'))
            <a href="{{url('admin/category/create')}}" class="btn btn-success mb-2">Thêm mới danh mục</a>
            @endif
            <div style="float:right" class="col-md-9">
                <p style="text-align:right">
                    <a class="" data-toggle="collapse" href="#view" role="button" aria-expanded="false" aria-controls="view">
                        <i class="fas fa-cog"></i> Tùy chọn hiển thị
                    </a>
                </p>
                <div class="collapse col-md-12" id="view">
                    <div class="form-row" style="float:right">
                        
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('category','slug'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'category')" name="slugg" id="slugg" value="slug" class="form-control-checkbox view-setting" >
                            <label for="slugg">Đường dẫn</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('category','active'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'category')" name="activee" id="activee" value="active" class="form-control-checkbox view-setting" >
                            <label for="activee">Kích hoạt</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('category','totalProduct'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'category')" name="totalProduct" id="totalProduct" value="totalProduct" class="form-control-checkbox view-setting" >
                            <label for="totalProduct">Tổng sản phẩm</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('category','create_at'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'category')" name="create_att" id="create_att" value="create_at" class="form-control-checkbox view-setting" >
                            <label for="create_att">Ngày tạo</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('category','update_at'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'category')" name="update_att" id="update_att" value="update_at" class="form-control-checkbox view-setting" >
                            <label for="update_att">Ngày sửa</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('category','action'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'category')" name="action" id="action" value="action" class="form-control-checkbox view-setting" >
                            <label for="action">Thao tác</label>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>  
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th >Tên danh mục</th>
                    <th class="slug p_setting" scope="col" @if(!p_ui_setting('category','slug')) style="display:none" @endif>Đường dẫn</th>
                    <th class="active p_setting" @if(!p_ui_setting('category','active')) style="display:none" @endif>Kích hoạt</th>
                    <th class="totalProduct p_setting" @if(!p_ui_setting('category','totalProduct')) style="display:none" @endif>Tổng sản phẩm</th>
                    <th class="create_at p_setting" scope="col" @if(!p_ui_setting('category','create_at')) style="display:none" @endif>Ngày tạo</th>
                    <th class="update_at p_setting" scope="col" @if(!p_ui_setting('category','update_at')) style="display:none" @endif>Ngày sửa</th>
                    <th class="action p_setting" scope="col" @if(!p_ui_setting('category','action')) style="display:none" @endif>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($list_cate as $key =>$value)
                    <tr>
                    
                    <td  >{{$value->category_name}}</td>
                    <td class="slug p_setting" scope="col" @if(!p_ui_setting('category','slug')) style="display:none" @endif>{{$value->category_slug}}</td>
                    <td class="active p_setting" @if(!p_ui_setting('category','active')) style="display:none" @endif>
                        @if(p_author('active','tbl_category'))
                            @if($value->active==1)
                                <a href="#" class="p_category_active" data-id="{{$value->category_id}}"><div class="fa fa-check" style="color:#3ac47d"></div></a>
                            @else
                                <a href="#" class="p_category_active" data-id="{{$value->category_id}}"><div class="fa fa-times" style="color:#b81f44"></div></a>
                            @endif
                            <script>
                                $('.p_category_active').unbind().click(function(){
                                    var id=$(this).attr('data-id');
                                    var element=$(this);
                                    $.ajax({
                                        type: "POST",
                                        url: "{{url('api/category/active')}}",
                                        data: {id:id},
                                        dataType: "json",
                                        success: function (response) {
                                            console.log(response);
                                            if(!$.isEmptyObject(response.error)){

                                            }else{
                                                if(response.success==1){
                                                    element.html('<i class="fas fa-check" style="color:#3ac47d"></i>')
                                                }else{
                                                    element.html('<i class="fas fa-times" style="color:#b81f44"></i>')
                                                }
                                                
                                            }
                                        }
                                    });
                                    return false;
                                })
                            </script>
                        @else
                            @if($value->active==1)
                                <div class="fa fa-check" style="color:#3ac47d"></div>
                            @else
                                <div class="fa fa-times" style="color:#b81f44"></div>
                            @endif
                        @endif
                    </td>
                    <td  class="totalProduct p_setting" @if(!p_ui_setting('category','totalProduct')) style="display:none" @endif style="text-align: right;">{{$value->totalProduct}}</td>
                    <td class="create_at p_setting" scope="col" @if(!p_ui_setting('category','create_at')) style="display:none" @endif>{{$value->create_at}}</td>
                    <td class="update_at p_setting" scope="col" @if(!p_ui_setting('category','update_at')) style="display:none" @endif>{{$value->update_at}}</td>
                    <td class="action p_setting" scope="col" @if(!p_ui_setting('category','action')) style="display:none" @endif>
                        @if(p_author('edit','tbl_category'))
                        <a href="{{url('admin/category')}}/{{$value->category_id}}/edit" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                        @endif
                        @if(p_author('delete','tbl_category'))
                        <form onsubmit="" style=" display:inline-block" action="{{url('admin/category')}}/{{$value->category_id}}" method="post">
                            @method('DELETE')
                            @csrf
                           <button type="submit" class="btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                        </form>
                        @endif
                    </td>
                    </tr>
                @endforeach
                <tr>
                    <th  >Tên danh mục</th>
                    <th class="slug p_setting" scope="col" @if(!p_ui_setting('category','slug')) style="display:none" @endif>Đường dẫn</th>
                    <th class="active p_setting" @if(!p_ui_setting('category','active')) style="display:none" @endif>Kích hoạt</th>
                    <th class="totalProduct p_setting" @if(!p_ui_setting('category','totalProduct')) style="display:none" @endif>Tổng sản phẩm</th>
                    <th class="create_at p_setting" scope="col" @if(!p_ui_setting('category','create_at')) style="display:none" @endif>Ngày tạo</th>
                    <th class="update_at p_setting" scope="col" @if(!p_ui_setting('category','update_at')) style="display:none" @endif>Ngày sửa</th>
                    <th class="action p_setting" scope="col" @if(!p_ui_setting('category','action')) style="display:none" @endif>Thao tác</th>
                </tr>
            </tbody>
            
        </table>
        <div class="row" style="display:flex;justify-content:center;align-items:center">
            {{$list_cate->links()}} 
        </div>
          
    </div>
</div>

@endsection
