<div class="app-sidebar sidebar-shadow" id="scroll-menu" >
    <style>
        #scroll-menu:hover{
            overflow-y:scroll;
        }
        #scroll-menu::-webkit-scrollbar-track{
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            border-radius: 10px;	
            background-color: #F5F5F5;
        }
        #scroll-menu::-webkit-scrollbar
        {
            width: 12px;
            background-color: #F5F5F5;
        }

        #scroll-menu::-webkit-scrollbar-thumb
        {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #dedede;
        }
    </style>
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>    
    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">
                @if(is_admin())
                <li class="app-sidebar__heading">Dashboards</li>
                <li>
                    <a href="{{url('admin/dashboard')}}" class="{{request()->is('admin/dashboard')?'mm-active':''}}">
                        <i class="metismenu-icon pe-7s-rocket"></i>
                        Dashboard 
                    </a>
                </li>
                <li class="app-sidebar__heading">Vai trò người dùng</li>
                <li>
                    <a href="{{url('admin/role')}}" class="{{request()->is('admin/role')?'mm-active':''}}">
                        <i class="metismenu-icon pe-7s-rocket"></i>
                        Danh sách vai trò
                    </a>
                </li>
                @endif
                <!-- <li>
                    <a href="#">
                        <i class="metismenu-icon pe-7s-diamond"></i>
                        Elements
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        <li>
                            <a href="elements-buttons-standard.html">
                                <i class="metismenu-icon"></i>
                                Buttons
                            </a>
                        </li>
                        <li>
                            <a href="elements-dropdowns.html">
                                <i class="metismenu-icon">
                                </i>Dropdowns
                            </a>
                        </li>
                        <li>
                            <a href="elements-icons.html">
                                <i class="metismenu-icon">
                                </i>Icons
                            </a>
                        </li>
                        <li>
                            <a href="elements-badges-labels.html">
                                <i class="metismenu-icon">
                                </i>Badges
                            </a>
                        </li>
                        <li>
                            <a href="elements-cards.html">
                                <i class="metismenu-icon">
                                </i>Cards
                            </a>
                        </li>
                        <li>
                            <a href="elements-list-group.html">
                                <i class="metismenu-icon">
                                </i>List Groups
                            </a>
                        </li>
                        <li>
                            <a href="elements-navigation.html">
                                <i class="metismenu-icon">
                                </i>Navigation Menus
                            </a>
                        </li>
                        <li>
                            <a href="elements-utilities.html">
                                <i class="metismenu-icon">
                                </i>Utilities
                            </a>
                        </li>
                    </ul>
                </li> -->
                @if(p_author('view','tbl_order',false,true))
                <li class="app-sidebar__heading">Đơn hàng</li>
                    @if(p_author('view','tbl_order'))
                        <li>
                            <a href="{{url('admin/order')}}" class="{{request()->is('admin/order')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Danh sách đơn hàng
                            </a>
                        </li>
                    @endif
                    @if(p_author('add','tbl_order'))
                        <li>
                            <a href="{{url('admin/order/create')}}" class="{{request()->is('admin/order/create')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Thêm đơn hàng
                            </a>
                        </li>
                    @endif
                @endif

                @if(p_author('view','tbl_category',false,true))
                <li class="app-sidebar__heading">Danh mục</li>
                    @if(p_author('view','tbl_category'))
                        <li>
                            <a href="{{url('admin/category')}}" class="{{request()->is('admin/category')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-car"></i>
                                Danh sách danh mục 
                            </a>
                        </li>
                    @endif
                    @if(p_author('add','tbl_category'))
                        <li>
                            <a href="{{url('admin/category/create')}}" class="{{request()->is('admin/category/create')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Thêm danh mục
                            </a>
                        </li>
                    @endif
                @endif

                @if(p_author('view','tbl_user',false,true))
                <li class="app-sidebar__heading">Người dùng</li>
                    @if(p_author('view','tbl_user'))
                    <li>
                        <a href="{{url('admin/user')}}" class="{{request()->is('admin/user')?'mm-active':''}}">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            Danh sách người dùng
                        </a>
                    </li>
                    @endif
                    @if(p_author('add_role','tbl_user'))
                        <li>
                            <a href="{{url('admin/user/addrole')}}" class="{{request()->is('admin/user/addrole')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Thêm vai trò cho người dùng
                            </a>
                        </li>
                    @endif
                    @if(p_author('edit_role','tbl_user'))
                        <li>
                            <a href="{{url('admin/user/editrole')}}" class="{{request()->is('admin/user/editrole')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Thêm vai trò cho người dùng
                            </a>
                        </li>
                    @endif
                @endif

                @if(p_author('view','tbl_product',false,true))
                <li class="app-sidebar__heading">Sản phẩm</li>
                    @if(p_author('view','tbl_product'))
                    <li>
                        <a href="{{url('admin/product')}}" class="{{request()->is('admin/product')?'mm-active':''}}">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            Danh sách sản phẩm
                        </a>
                    </li>
                    @endif
                    @if(p_author('view','tbl_product'))
                    <li>
                        <a href="{{url('admin/product/create')}}" class="{{request()->is('admin/product/create')?'mm-active':''}}">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            Thêm sản phẩm
                        </a>
                    </li>
                    @endif
                @endif

                @if(p_author('view','tbl_comment',false,true))
                    @if(p_author('view','tbl_comment'))
                        <li class="app-sidebar__heading">Bình luận</li>
                        <li>
                            <a href="{{url('admin/comment')}}" class="{{request()->is('admin/comment')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Danh sách bình luận
                            </a>
                        </li>
                    @endif
                    @if(p_author('add','tbl_comment'))
                        <li>
                            <a href="{{url('admin/comment/create')}}" class="{{request()->is('admin/comment/create')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Thêm bình luận
                            </a>
                        </li>
                    @endif
                @endif

                @if(p_author('view','tbl_store',false,true))
                <li class="app-sidebar__heading">Cửa hàng</li>
                    @if(p_author('view','tbl_store'))
                        <li>
                            <a href="{{url('admin/store')}}" class="{{request()->is('admin/store')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Danh sách cửa hàng
                            </a>
                        </li>
                    @endif
                    @if(p_author('add_product','tbl_store'))
                        <li>
                            <a href="{{url('admin/store/addproduct')}}" class="{{request()->is('admin/store/addproduct')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Nhập sản phẩm
                            </a>
                        </li>
                    @endif
                    @if(p_author('add','tbl_store'))
                        <li>
                            <a href="{{url('admin/store/create')}}" class="{{request()->is('admin/store/create')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Thêm cửa hàng
                            </a>
                        </li>
                    @endif
                @endif

                @if(p_author('view','tbl_size',false,true))
                <li class="app-sidebar__heading">Kích thước sản phẩm</li>
                    @if(p_author('view','tbl_size'))
                        <li>
                            <a href="{{url('admin/size')}}" class="{{request()->is('admin/size')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Danh sách kích thước
                            </a>
                        </li>
                    @endif
                    @if(p_author('add','tbl_size'))
                        <li>
                            <a href="{{url('admin/size/create')}}" class="{{request()->is('admin/size/create')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                            Thêm kích thước
                            </a>
                        </li>
                    @endif
                @endif

                @if(p_author('view','tbl_color',false,true))
                <li class="app-sidebar__heading">Màu sản phẩm</li>
                    @if(p_author('view','tbl_color'))
                        <li>
                            <a href="{{url('admin/color')}}" class="{{request()->is('admin/color')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Danh sách màu 
                            </a>
                        </li>
                    @endif
                    @if(p_author('add','tbl_color'))
                        <li>
                            <a href="{{url('admin/color/create')}}" class="{{request()->is('admin/color/create')?'mm-active':''}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Thêm màu
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>
</div>   