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
                           
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_permission as $permissions => $permission)
                            <tr>
                                <td>{{$permission->permission}}</td>
                                
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