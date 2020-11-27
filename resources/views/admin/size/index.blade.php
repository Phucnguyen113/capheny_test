
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
        title:'Xóa thất bại',
        text:'Kích cỡ này đã có gán cho sản phẩm'
    })
</script>
@endif
@if(\Session::has('success'))
<script>
    Swal.fire({
        icon:'success',
        title:'Xóa thành công',
        text:'Bạn vừa xóa 1 màu'
    })
</script>
@endif


<div class="main-card card">
    <div class="card-body">
        <div id="filter_p" style="margin-bottom:15px;">
            <div class="collapse" id="collapseExample">
                        
                        
                        <form class="form-group"  action="" onsubmit="return validate_filter()" method="GET" enctype="multipart/form-data"> 
                            <input type="hidden" name="search" value="true">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label for="category_name" class="">Kích thước</label>
                                        <input name="size" autocomplete="FALSE" id="category_name" placeholder="Nhập size cần tìm ở đây" type="text" class="form-control">
                                        <div id="category_name_error" style="color:red"></div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="datepicker" class="">Ngày tạo bắt đầu</label>
                                        <input name="create_at_from" value="" id="create_at_from" readonly='true' autocomplete="FALSE" placeholder="Ngày tạo bắt đầu" type="text" class="form-control">
                                        <div id="picker_time_create_at_from" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="datepicker_create_to" class="">Ngày tạo kết thúc</label>
                                        <input name="create_at_to" value=""  readonly='true' autocomplete="FALSE" id="create_at_to" placeholder="Ngày tạo kết thúc" type="text" class="form-control">
                                        <div id="picker_time_create_at_to" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="datepicker_update_from" class="">Ngày sửa bắt đầu</label>
                                        <input name="update_at_from" value="" readonly='true' autocomplete="FALSE" id="update_at_from" placeholder="Ngày sửa bắt đầu" type="text" class="form-control">
                                        <div id="picker_time_update_at_from" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="datepicker_update_to" class="">Ngày sửa kết thúc</label>
                                        <input name="update_at_to" value="" readonly='true' autocomplete="FALSE" id="update_at_to" placeholder="Ngày sửa kết thúc" type="text" class="form-control">
                                        <div id="picker_time_update_at_to" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
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
                         
                                <button class="mt-1 btn btn-primary">Tìm kiếm</button>
                            </form>

            </div>

            <div style="display:flex;justify-content:center;align-items:center">
                    <a  data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Tìm kiếm
                    </a>  
            </div>
          
        </div>
        <!-- end search -->
        <h5 class="card-title" style="font-size:36px;text-align:center">Danh sách kích cỡ</h5>
        
        <div class="">
            @if(p_author('add','tbl_size'))
                <a href="{{url('admin/size/create')}}" style="color:white" class="btn btn-success mb-2">Thêm mới kích cỡ</a>
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
                            <input type="checkbox" @if(p_ui_setting('size','create_at'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'size')" name="create_att" id="create_att" value="create_at" class="form-control-checkbox view-setting" >
                            <label for="create_att">Ngày tạo</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('size','update_at'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'size')" name="update_att" id="update_att" value="update_at" class="form-control-checkbox view-setting" >
                            <label for="update_att">Ngày sửa</label>
                        </div>
                        <div class="m-2">
                            <input type="checkbox" @if(p_ui_setting('size','action'))  checked @endif onclick="view_setting({{p_user()['user_id']}},'size')" name="action" id="action" value="action" class="form-control-checkbox view-setting" >
                            <label for="action">Ngày sửa</label>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div> 
        <table class="table table-bordered" >
            <thead>
                <tr>
                   
                    <th scope="col">Kích cỡ</th>
                    <th scope="col" class="p_setting create_at" @if(!p_ui_setting('size','create_at'))  style="display: none;" @endif>Ngày tạo</th>
                    <th scope="col"  class="p_setting update_at" @if(!p_ui_setting('size','update_at'))  style="display: none;" @endif >Ngày chỉnh sửa</th>
                    <th scope="col" class="p_setting action" @if(!p_ui_setting('size','action'))  style="display: none;" @endif>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($list_size as $key =>$value)
                    <tr>
                   
                    <th>{{$value->size}}</th>
                    <td class="p_setting create_at" @if(!p_ui_setting('size','create_at'))  style="display: none;" @endif>{{$value->create_at}}</td>
                    <td class="p_setting update_at" @if(!p_ui_setting('size','update_at'))  style="display: none;" @endif>{{$value->update_at}}</td>
                    <td class="p_setting action" @if(!p_ui_setting('size','action'))  style="display: none;" @endif>
                        @if(p_author('edit','tbl_size'))
                        <a href="{{url('admin/size')}}/{{$value->size_id}}/edit" class="btn btn-primary"><div class="fa fa-edit"></div></a>
                        @endif
                        @if(p_author('delete','tbl_size'))
                        <form  style=" display:inline-block" action="{{url('admin/size')}}/{{$value->size_id}}" method="post">
                            @method('DELETE')
                            @csrf
                           <button type="submit" class="btn btn-danger"><div class="fa fa-trash-alt"></div></button>
                        </form>
                        @endif
                    </td>
                    </tr>
                @endforeach
                <tr>
                    <th scope="col">Kích cỡ</th>
                    <th scope="col" class="p_setting create_at" @if(!p_ui_setting('size','create_at'))  style="display: none;" @endif>Ngày tạo</th>
                    <th scope="col"  class="p_setting update_at" @if(!p_ui_setting('size','update_at'))  style="display: none;" @endif >Ngày chỉnh sửa</th>
                    <th scope="col" class="p_setting action" @if(!p_ui_setting('size','action'))  style="display: none;" @endif>Thao tác</th>
                </tr>
            </tbody>
            
        </table>
        <div class="row" style="display:flex;justify-content:center;align-items:center">
            {{$list_size->links()}} 
        </div>
          
    </div>
</div>

@endsection
