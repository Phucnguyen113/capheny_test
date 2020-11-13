@extends('layouts.admin')
@section('js')
<script src="{{asset('treeview/mgaccordion.js')}}"></script>
@endsection
@section('css')
<link rel="stylesheet" href="{{asset('treeview/mgaccordion.css')}}">
@endsection
@section('body')
<a href="{{url()->previous()}}" class="btn btn-warning mb-2" style="color:white">Quay lại</a>

    <div class="card">
        <div class="card-body" >
            <div class="row">
                <div class="col-md-4">
                    <div class="card-title">
                        Thông tin cửa hàng
                    </div>
                    <div class="row mb-2">
                        Name: {{$data->store_name}} <br>
                        province: {{$data->province}}<br>
                        district : {{$data->district}}<br>
                        ward : {{$data->ward}}<br>
                        address : {{$data->store_address}}<br>
                    </div>
                    <div class="row mt-5">
                        <div class="card-title" >
                            Danh sách sản phẩm 
                        </div>
                        <nav class="my-menu">
                            <ul class="my-nav2">
                                @foreach($list_product_distinct as $products => $product)
                                    <li>
                                        <a href="#" title="">{{$product->product_name}}</a>
                                        <ul>
                                            @foreach($product->detail as $details => $detail)
                                                <li>
                                                    <a href="#">
                                                        Màu : <span style="width:15px;height:15px;display:inline-block;background-color:#{{$detail['color']}}"></span>
                                                        Kích thước : {{$detail['size']}} <br>
                                                        Số lượng : {{$detail['amount']}}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>

                                @endforeach
                            </ul>
                        </nav>
                            <script>
                                $(document).ready(function () {
                                    $('.my-nav2').mgaccordion();
                                });
                            </script>
                    </div>
                </div>
                <div class="col-md-8">
                 
                <div class="card-title">
                        Chi tiết nhập hàng
                    </div>
                    <table class="table table-striped table-inverse ">
                            <thead class="thead-inverse">
                                <tr>
                                    <th>Tên sản phẩm</th>
                                    <th>Màu</th>
                                    <th>Kích thước</th>
                                    <th>Số lượng nhập</th>
                                    <th>Số lượng còn</th>
                                    <th>Ngày nhập</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->list_product as $products => $product)
                                        <tr>
                                            <td scope="row">{{$product->product_name}}</td>
                                            <td><div style="width:15px;height:15px;background-color:#{{$product->color}};display:inline-block"></div></td>
                                            <td>{{$product->size}}</td>
                                            <td>{{$product->amount_}}</td>
                                            <td>{{$product->product_amount}}</td>
                                            <td>{{$product->create_at}}</td>
                                            <td>
                                                <a href="{{url('admin/store/editproduct')}}/{{$product->id}}" class="btn btn-primary"><div class="fa fa-edit"></div></a>
                                                <form style="display:inline-block" action="{{url('admin/store/delete/addproduct')}}/{{$product->id}}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger"><div class="fa fa-trash-alt"></div></button>
                                                </form>
                                            </td>
                                        </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                        {{$data->list_product->appends(request()->all())->links()}}
                       
                </div>
            </div>
        </div>
    </div>
@endsection