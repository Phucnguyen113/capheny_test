@extends('layouts.admin')
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

<script>
    $(document).ready(function () {
        
        $.ajax({
            type: "POST",
            url: "{{url('api/dashboard/order')}}",
            data: {},
            dataType: "json",
            success: function (response) {
                if(!$.isEmptyObject(response.error)){

                }else{
                    data = {
                        datasets: [{
                            data: [
                                    response.data.status_0,
                                    response.data.status_1,
                                    response.data.status_2,
                                    response.data.status_3,
                                    response.data.status_4
                                ],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }],

                        // These labels appear in the legend and in the tooltips when hovering different arcs
                        labels: [
                            'Chờ xử lý',
                            'Đã tiếp nhận',
                            'Đang giao hàng',
                            'Đã nhận hàng',
                            'Trả hàng về'
                        ]
                    };
                    
                    var ctx = document.getElementById('myChart').getContext('2d');
                    var myPieChart = new Chart(ctx, {
                        type: 'pie',
                        data: data,
                       
                    });
                }
            }
        });
    });

</script>
@endsection
@section('css')
@endsection
@section('body')
<div class="card-title" style="text-align:center;font-size:36px">Thống kế</div>

    <div class="card main-card mb-3">
        <div class="card-body ">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Người dùng</th>
                            <th>Thao tác</th>
                            <th>Lúc</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_history as $histories => $history)
                        <tr>
                            <td>{{$history->user_email}}</td>
                            <td>{!!$history->history!!}
                                
                      
                            <td>{{$history->create_at}} </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-md-6 col-xl-4"> 
        <div class="card mb-3 widget-content bg-midnight-bloom">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">Tổng đơn hàng</div>
                    <div class="widget-subheading">Tới hiện tại</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{number_format($total_order)}}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card mb-3 widget-content bg-arielle-smile">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">Tổng khách hàng đăng ký</div>
                    <div class="widget-subheading">Tới hiện tại</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{number_format($total_user)}}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card mb-3 widget-content bg-grow-early">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">Tổng khách hàng không đăng ký</div>
                    <div class="widget-subheading">Tới hiện tại</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{number_format($total_user_not_login)}}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-md-6">
            <div class="card main-card mb-3">
                <div class="card-body ">
                    <div class="card-title">Thống kê loại đơn hàng</div>
                    <canvas id="myChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card main-card">
                <div class="card-body">
                    <div class="card-title">Thống kê sản phẩm ở cửa hàng</div>
                    <canvas id="store_canvas" width="400" height="400"></canvas>
                    <script>
                        $.ajax({
                            type: "post",
                            url: "{{url('api/dashboard/store_product')}}",
                            data: {},
                            dataType: "json",
                            success: function (response) {
                                console.log(response);
                                if(!$.isEmptyObject(response.error)){

                                }else{
                                    var ctx = document.getElementById('store_canvas').getContext('2d');
                                    var myChart = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: response.data.total_store,
                                            datasets: [{
                                                label: 'Sản phẩm',
                                                data: response.data.total_product,
                                                backgroundColor: [
                                                    'rgba(255, 99, 132, 0.2)',
                                                    'rgba(54, 162, 235, 0.2)',
                                                    'rgba(255, 206, 86, 0.2)',
                                                    'rgba(75, 192, 192, 0.2)',
                                                    'rgba(153, 102, 255, 0.2)',
                                                    'rgba(255, 159, 64, 0.2)'
                                                ],
                                                borderColor: [
                                                    'rgba(255, 99, 132, 1)',
                                                    'rgba(54, 162, 235, 1)',
                                                    'rgba(255, 206, 86, 1)',
                                                    'rgba(75, 192, 192, 1)',
                                                    'rgba(153, 102, 255, 1)',
                                                    'rgba(255, 159, 64, 1)'
                                                ],
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            scales: {
                                                yAxes: [{
                                                    ticks: {
                                                        beginAtZero: true
                                                    }
                                                }]
                                            }
                                        }
                                    });
                                }
                            }
                        });
                        
                        </script>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Tổng danh mục</div>
                            <div class="widget-subheading">Tới hiện tại</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-danger">{{number_format($total_cate)}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Tổng sản phẩm</div>
                            <div class="widget-subheading">Tới hiện tại</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-danger">{{number_format($total_product)}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Tổng tiền đã thu</div>
                            <div class="widget-subheading">Tới hiện tại</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-danger">{{number_format($total_price)}} VNĐ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Tổng cửa hàng</div>
                            <div class="widget-subheading">Tới hiện tại</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-danger">{{number_format($total_store)}} </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection