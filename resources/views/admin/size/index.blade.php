
@extends('layouts.admin')
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
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src='https://code.jquery.com/jquery-3.5.0.min.js'></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
                                        <label for="category_name" class="">Size</label>
                                        <input name="size"  id="category_name" placeholder="Nhập size cần tìm ở đây" type="text" class="form-control">
                                        <div id="category_name_error" style="color:red"></div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="datepicker" class="">Create_at from</label>
                                        <input name="create_at_from" value="" id="datepicker" placeholder="Ngày sửa" type="text" class="form-control">
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="datepicker_create_to" class="">Create_at to</label>
                                        <input name="create_at_to" value="" id="datepicker_to" placeholder="Ngày sửa" type="text" class="form-control">
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="datepicker_update_from" class="">Update_at from</label>
                                        <input name="update_at_from" value="" id="datepicker_update" placeholder="Ngày sửa" type="text" class="form-control">
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="datepicker_update_to" class="">Update_at to</label>
                                        <input name="update_at_to" value="" id="datepicker_update_to" placeholder="Ngày sửa" type="text" class="form-control">
                                        <div id="update_at_error" style="color:red"></div>
                                    </div>
                                </div>
                               
                            </div>
                            
                                <script>
                                    $( "#datepicker" ).datepicker();
                                    $('#datepicker').datepicker("option","dateFormat","yy-mm-dd")
                                    $( "#datepicker_update" ).datepicker();
                                    $('#datepicker_update').datepicker("option","dateFormat","yy-mm-dd")
                                    $( "#datepicker_to" ).datepicker();
                                    $('#datepicker_to').datepicker("option","dateFormat","yy-mm-dd")
                                    $( "#datepicker_update_to" ).datepicker();
                                    $('#datepicker_update_to').datepicker("option","dateFormat","yy-mm-dd")
                                    $('#datepicker').val("{{isset($_GET['create_at_from']) ?$_GET['create_at_from']:''}}");
                                    $('#datepicker_update').val("{{isset($_GET['update_at_from']) ?$_GET['update_at_from']:''}}");
                                    $('#datepicker_to').val("{{isset($_GET['create_at_to']) ?$_GET['create_at_to']:''}}");
                                    $('#datepicker_update_to').val("{{isset($_GET['update_at_to']) ?$_GET['update_at_to']:''}}");
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
        <h5 class="card-title" style="font-size:36px;text-align:center">Danh sách kích cỡ</h5>
        @if(p_author('add','tbl_size'))
            <a href="{{url('admin/size/create')}}" style="color:white" class="btn btn-success mb-2">Thêm mới kích cỡ</a>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Kích cỡ</th>
                    <th scope="col">Ngày tạo</th>
                    <th scope="col">Ngày chỉnh sửa</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($list_size as $key =>$value)
                    <tr>
                    <th scope="row">{{$value->size_id}}</th>
                    <th >{{$value->size}}</th>
                    <td>{{$value->create_at}}</td>
                    <td>{{$value->update_at}}</td>
                    <td>
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
                    <th scope="col">#</th>
                    <th scope="col">Kích cỡ</th>
                    <th scope="col">Ngày tạo</th>
                    <th scope="col">Ngày chỉnh sửa</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </tbody>
            
        </table>
        <div class="row" style="display:flex;justify-content:center;align-items:center">
            {{$list_size->links()}} 
        </div>
          
    </div>
</div>

@endsection
