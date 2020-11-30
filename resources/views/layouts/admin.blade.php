<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@if(isset($title)) {{$title}} @else Capheny @endif</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">
    <!--
    =========================================================
    * ArchitectUI HTML Theme Dashboard - v1.0.0
    =========================================================
    * Product Page: https://dashboardpack.com
    * Copyright 2019 DashboardPack (https://dashboardpack.com)
    * Licensed under MIT (https://github.com/DashboardPack/architectui-html-theme-free/blob/master/LICENSE)
    =========================================================
    * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    -->
    <!-- css tree select  -->
    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.0.45/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{asset('treeselect')}}/tre_select.css">
    <!-- css template -->
    <link href="{{asset('')}}/main.css" rel="stylesheet">
    <!-- css jquery ui datepicker -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- css select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <!-- css datetimepicker -->
    <link rel="stylesheet" href="{{asset('datetimePicker/css/jquery.datetimepicker.css')}}">
    @section('css')
    @show
    <!-- jquery -->
    <script src='https://code.jquery.com/jquery-3.5.0.min.js'></script>
    <!-- js tree select -->
    <script src="{{asset('treeselect')}}/comboTreePlugin.js"></script>
    <!-- jquery ui datepicker -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- js select 2  -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <!-- js datetimepicker-->
    <script src="{{asset('/datetimePicker/js/jquery.datetimepicker.js')}}"></script>
    <!-- js sweet alert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- fontAwesome -->
    <script src="{{asset('fontAwesome/fontAwesome.js')}}"></script>
    @section('js')
    @show
</head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        @include('page.header_admin')    
              
        <div class="app-main">
              @include('page.left_menu_admin') 
                <div class="app-main__outer">
                    <div class="app-main__inner">
                           @section('body')

                           @show
                    </div>
                    @include('page.footer_admin')
                </div>
                
        </div>
    </div>
    
    
    <script type="text/javascript" src="{{url('')}}/assets/scripts/main.js"></script>
</body>
</html>
