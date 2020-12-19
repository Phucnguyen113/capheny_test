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
    <link rel="stylesheet" href="{{asset('p_js/jquery.toast.css')}}">
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
    <script src="//js.pusher.com/3.1/pusher.min.js"></script>
    <!-- fontAwesome -->
    <script src="{{asset('fontAwesome/fontAwesome.js')}}"></script>
    <script src="{{asset('p_js/jquery.toast.js')}}"></script>
    <script>
     var pusher = new Pusher('c76eed35cec6f6ddf74a', {
        encrypted: true,
        cluster:'ap1'
      });

      // Subscribe to the channel we specified in our Laravel Event
    @if(!request()->is('admin/product/create'))
        var channel = pusher.subscribe('product-add');
        channel.bind('App\\Events\\pusherProduct', function(data) {
            
            $.toast({
                heading: 'Thêm mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    
    @if(!request()->is('admin/product/*/edit'))
        var channel_edit = pusher.subscribe('product-edit');
        channel_edit.bind('App\\Events\\pusherProductEdit', function(data) {
        
            $.toast({
                heading: 'Cập nhật mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    @if(!request()->is('admin/product'))
        var channel_delete = pusher.subscribe('product-delete');
        channel_delete.bind('App\\Events\\pusherProductDelete', function(data) {
        
            $.toast({
                heading: 'Xóa dữ liệu',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'Warning'
            })
        })
    @endif
    // order
    @if(!request()->is('admin/order/create'))
        var channel_order_add = pusher.subscribe('order-add');
        channel_order_add.bind('App\\Events\\pusherOrder', function(data) {
            $.toast({
                heading: 'Thêm mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    @if(!request()->is('admin/order/*/edit'))
        var channel_order_edit = pusher.subscribe('order-edit');
        channel_order_edit.bind('App\\Events\\pusherOrderEdit', function(data) {
            $.toast({
                heading: 'Cập nhật mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    @if(!request()->is('admin/order'))
        var channel_order_delete = pusher.subscribe('order-delete');
        channel_order_delete.bind('App\\Events\\pusherOrderDelete', function(data) {
        
            $.toast({
                heading: 'Xóa dữ liệu',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'Warning'
            })
        })
    @endif
    // user
    @if(!request()->is('admin/user/create'))
        var channel_user_add = pusher.subscribe('user-add');
        channel_user_add.bind('App\\Events\\pusherUser', function(data) {
        
            $.toast({
                heading: 'Thêm mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    @if(!request()->is('admin/user/*/edit'))
        var channel_user_edit = pusher.subscribe('user-edit');
        channel_user_edit.bind('App\\Events\\pusherUserEdit', function(data) {
        
            $.toast({
                heading: 'Cập nhật mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    @if(!request()->is('admin/user'))
        var channel_user_delete = pusher.subscribe('user-delete');
        channel_user_delete.bind('App\\Events\\pusherUserDelete', function(data) {
        
            $.toast({
                heading: 'Xóa dữ liệu',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'Warning'
            })
        })
    @endif
    // store
    @if(!request()->is('admin/store/create'))
    var channel_store_add = pusher.subscribe('store-add');
    channel_store_add.bind('App\\Events\\pusherStore', function(data) {
      
        $.toast({
            heading: 'Thêm mới',
            text: data.message,
            position: 'bottom-right',
            showHideTransition: 'slide',
            icon: 'success'
        })
    })
    @endif
    @if(!request()->is('admin/store/*/edit'))
        var channel_store_edit = pusher.subscribe('store-edit');
        channel_store_edit.bind('App\\Events\\pusherStoreEdit', function(data) {
        
            $.toast({
                heading: 'Cập nhật mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    @if(!request()->is('admin/store/addproduct'))
    var channel_store_addproduct = pusher.subscribe('store-addproduct');
    channel_store_addproduct.bind('App\\Events\\pusherStoreAddproduct', function(data) {
      
        $.toast({
            heading: 'Cập nhật mới',
            text: data.message,
            position: 'bottom-right',
            showHideTransition: 'slide',
            icon: 'success'
        })
    })
    @endif

    @if(!request()->is('admin/store'))
        var channel_store_delete = pusher.subscribe('store-delete');
        channel_store_delete.bind('App\\Events\\pusherStoreDelete', function(data) {
        
            $.toast({
                heading: 'Xóa dữ liệu',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'Warning'
            })
        })
    @endif
     // cate
    @if(!request()->is('admin/category/create'))
        var channel_cate_add = pusher.subscribe('cate-add');
        channel_cate_add.bind('App\\Events\\pusherCate', function(data) {
        
            $.toast({
                heading: 'Thêm mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    @if(!request()->is('admin/category/*/edit'))
        var channel_cate_edit = pusher.subscribe('cate-edit');
        channel_cate_edit.bind('App\\Events\\pusherCateEdit', function(data) {
        
            $.toast({
                heading: 'Cập nhật mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    @if(!request()->is('admin/category'))
        var channel_cate_delete = pusher.subscribe('cate-delete');
        channel_cate_delete.bind('App\\Events\\pusherCateDelete', function(data) {
        
            $.toast({
                heading: 'Xóa dữ liệu',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'Warning'
            })
        })
    @endif
    //color 
    @if(!request()->is('admin/color/create'))
        var channel_color_add = pusher.subscribe('color-add');
        channel_color_add.bind('App\\Events\\pusherColor', function(data) {
        
            $.toast({
                heading: 'Thêm mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif

    @if(!request()->is('admin/color/*/edit'))
        var channel_color_edit = pusher.subscribe('color-edit');
        channel_color_edit.bind('App\\Events\\pusherColorEdit', function(data) {
        
            $.toast({
                heading: 'Cập nhật mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif

    @if(!request()->is('admin/color'))
        var channel_color_delete = pusher.subscribe('color-delete');
        channel_color_delete.bind('App\\Events\\pusherColorDelete', function(data) {
        
            $.toast({
                heading: 'Xóa dữ liệu',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'Warning'
            })
        })
    @endif
    //size
    @if(!request()->is('admin/size/create'))
      var channel_size_add = pusher.subscribe('size-add');
        channel_size_add.bind('App\\Events\\pusherSize', function(data) {
        
            $.toast({
                heading: 'Thêm mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif

    @if(!request()->is('admin/size/*/edit'))
    var channel_size_edit = pusher.subscribe('size-edit');
    channel_size_edit.bind('App\\Events\\pusherSizeEdit', function(data) {
      
        $.toast({
            heading: 'Cập nhật mới',
            text: data.message,
            position: 'bottom-right',
            showHideTransition: 'slide',
            icon: 'success'
        })
    })
    @endif

    @if(!request()->is('admin/size'))
        var channel_size_delete = pusher.subscribe('size-delete');
        channel_size_delete.bind('App\\Events\\pusherSizeDelete', function(data) {
        
            $.toast({
                heading: 'Xóa dữ liệu',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'Warning'
            })
        })
    @endif
    //comment 
    @if(!request()->is('admin/comment/create'))
        var channel_comment_add = pusher.subscribe('comment-add');
        channel_comment_add.bind('App\\Events\\pusherComment', function(data) {
        
            $.toast({
                heading: 'Thêm mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif

    @if(!request()->is('admin/comment/*/edit'))
        var channel_comment_edit = pusher.subscribe('comment-edit');
        channel_comment_edit.bind('App\\Events\\pusherCommentEdit', function(data) {
        
            $.toast({
                heading: 'Cập nhật mới',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success'
            })
        })
    @endif
    @if(!request()->is('admin/comment'))
        var channel_comment_delete = pusher.subscribe('comment-delete');
        channel_comment_delete.bind('App\\Events\\pusherCommentDelete', function(data) {
        
            $.toast({
                heading: 'Xóa dữ liệu',
                text: data.message,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'Warning'
            })
        })
    @endif
</script>
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
