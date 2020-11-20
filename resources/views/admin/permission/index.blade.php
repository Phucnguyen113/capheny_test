@extends('layouts.admin')
@section('body')
    <div class="card main-card">
        <div class="card-body">
            <div class="card-title" style="font-size:36px;text-align:center">
                Danh sách quyền
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Quyền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_permission as $permissions => $permission)
                            <tr>
                                <td>{{$permission->permission}}</td>
                                <td>
                                    <a href="{{url('admin/permission')}}/{{$permission->permission_id}}/edit" class="btn btn-primary"> <div class="fa fa-edit"></div></a>
                                    <form action="{{url('admin/permission')}}/{{$permission->permission_id}}/delete" method="post" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger"> <div class="fa fa-trash-alt"></div></button>
                                    </form>
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
@section('js')
@endsection