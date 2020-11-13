
@extends('layouts.admin')
@section('body')
<!-- Button trigger modal -->
@if($errors->has('error'))
<script>
    Swal.fire({
        icon:'error',
        title:'Xóa thất bại',
        text:'Màu này đã có gán cho sản phẩm'
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
                        <div class="card-title">Filter</div>
                        <form class="form-group" action="" onsubmit="return validate_filter()" method="GET" enctype="multipart/form-data"> 
                            <input type="hidden" name="search" value="true">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label for="category_name" >Color</label>
                                        <select class="select-2 js-example-basic-multiple" style="width:100%" multiple name="color[]">
                                            @foreach($color_search as $key => $value)
                                                <option value="{{$value->color_id}}" data-color="{{$value->color}}"
                                                    @if(isset($_GET['color']) && in_array($value->color_id,$_GET['color'])) selected
                                                    @endif
                                                >
                                                    
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="category_name_error" style="color:red"></div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="create_at_from" class="">Create_at from</label>
                                        <input name="create_at_from" value="@isset($_GET['create_at_from']) {{$_GET['create_at_from']}} @endisset" id="create_at_from" placeholder="Ngày sửa"  readonly='true' type="text" class="form-control">
                                        <div id="picker_time_create_at_from" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="create_at_to" class="">Create_at to</label>
                                        <input name="create_at_to" value="@isset($_GET['create_at_to']) {{$_GET['create_at_to']}} @endisset" id="create_at_to" placeholder="Ngày sửa" readonly='true'  type="text" class="form-control">
                                        <div id="picker_time_create_at_to" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="update_at_from" class="">Update_at from</label>
                                        <input name="update_at_from" value="@isset($_GET['update_at_from']) {{$_GET['update_at_from']}} @endisset" id="update_at_from" placeholder="Ngày sửa" readonly='true' type="text" class="form-control">
                                        <div id="picker_time_update_at_from" style="display:flex;justify-content:center;position:absolute;z-index:99"></div>
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="update_at_to" class="">Update_at to</label>
                                        <input name="update_at_to" value="@isset($_GET['update_at_to']) {{$_GET['update_at_to']}} @endisset" id="update_at_to" placeholder="Ngày sửa" readonly='true' type="text" class="form-control">
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

                                        $(".select-2").select2({
                                            templateResult: formatState,
                                            templateSelection :formatState
                                        });
                                   
                                </script>
                         
                            <button class="mt-1 btn btn-primary">Filter</button>
                        </form>

            </div>

            <div style="display:flex;justify-content:center;align-items:center">
                    <a  data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Filter
                    </a>  
            </div>
          
        </div>
        <!-- end search -->
        <h5 class="card-title" style="text-align: center;font-size:36px;">Danh sách màu</h5>
        <a href="{{url('admin/color/create')}}" class="btn btn-success mb-2">Thêm mới màu</a>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Màu</th>
                    <th scope="col">Ngày tạo</th>
                    <th scope="col">Ngày sửa</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($list_color as $key =>$value)
                    <tr>
                    <th scope="row">{{$value->color_id}}</th>
                    <th ><div style="height:15px;width:15px;background-color:#{{$value->color}}"></div></th>
                    <td>{{$value->create_at}}</td>
                    <td>{{$value->update_at}}</td>
                    <td>
                        <a href="{{url('admin/color')}}/{{$value->color_id}}/edit" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                        <form  style=" display:inline-block" action="{{url('admin/color')}}/{{$value->color_id}}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                        </form>
                      
                    </td>
                    </tr>
                @endforeach
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Màu</th>
                    <th scope="col">Ngày tạo</th>
                    <th scope="col">Ngày sửa</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </tbody>
            
        </table>
        <div class="row" style="display:flex;justify-content:center;align-items:center">
            {{$list_color->links()}} 
        </div>
          
    </div>
</div>

@endsection
