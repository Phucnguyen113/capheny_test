@extends('layouts.admin')
@section('js')
@endsection
@section('body')
    @if(\Session::has('success'))
        <script>
            Swal.fire({
                icon:'success',
                title:'Xóa thành công!',
                text:'Bạn vừa xóa 1 vai trò'
            })
        </script>
    @endif
    @if($errors->has('error_permission'))
        <script>
            Swal.fire({
                icon:'error',
                title:'Xóa thất bại!',
                text:'Vai trò có quyền đang được gán'
            })
        </script>
    @endif
    @if($errors->has('error_user'))
        <script>
            Swal.fire({
                icon:'error',
                title:'Xóa thất bại!',
                text:'Còn người dùng được gán vai trò này'
            })
        </script>
    @endif

    <div class="card main-card">
        <div class="card-body">
            <div class="card-title" style="text-align:center;font-size:36px;">
                Danh sách vai trò
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Vai trò</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_role as $roles => $role)
                            <tr>
                                <td>{{$role->role}}</td>
                                <td>
                                    @if(p_author('edit','tbl_role'))
                                        <a href="{{url('admin/role')}}/{{$role->role_id}}/edit" class="btn btn-primary"><div class="fa fa-edit"></div></a>
                                    @endif
                                    @if(p_author('delete','tbl_role'))
                                        <form action="{{url('admin/role')}}/{{$role->role_id}}/delete" method="post" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger"><div class="fa fa-trash-alt"></div></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
@endsection
@section('css')
@endsection
