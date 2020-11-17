
@extends('layouts.admin')
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
        @if(p_author('add','tbl_category'))
        <a href="{{url('admin/category/create')}}" class="btn btn-success mb-2">Thêm mới danh mục</a>
        @endif
        <table class="table">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Tên danh mục</th>
                <th scope="col">Đường dẫn</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col">ngày sửa</th>
                <th scope="col">Thao tác</th>
                <th scope="col"><button type="button" class="btn btn-success" data-toggle="modal" data-target="#staticBackdrop">Xem sơ đồ </button></th>
                </tr>
            </thead>
            <tbody>
                @foreach($list_cate as $key =>$value)
                    <tr>
                    <th scope="row">{{$value->category_id}}</th>
                    <td>{{$value->category_name}}</td>
                    <td>{{$value->category_slug}}</td>
                    <td>{{$value->create_at}}</td>
                    <td>{{$value->update_at}}</td>
                    <td>
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
                    <th scope="col">#</th>
                    <th scope="col">Tên danh mục</th>
                    <th scope="col">Dường dẫn</th>
                    <th scope="col">Ngày tạo</th>
                    <th>Ngày sửa</th>
                    <th scope="col">Thao tác</th>
                    <th scope="col">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#staticBackdrop">Xem sơ đồ </button>
                    </th>
                </tr>
            </tbody>
            
        </table>
        <div class="row" style="display:flex;justify-content:center;align-items:center">
            {{$list_cate->links()}} 
        </div>
          
    </div>
</div>

@endsection
<!-- Modal -->
<div class="modal fade"  id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" >
        <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Cấu trúc Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
            <div class="position-relative form-group">

                @foreach($cate_tree as $key =>$value)
                
                    <div class="alert alert-dark" role="alert"  @if($value->level!==0) style="margin-left:{{($value->level==1)?$value->level+1:$value->level+2}}0px" @endif>
                      {{$value->category_name}} / ID: {{$value->category_id}}
                    </div>
                @endforeach
            </div>              
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Understood</button>
      </div>
    </div>
  </div>
</div>