<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul>
                <li class="menu-title">Navigation</li>

                <li class="has_sub">
                    <a href="{{ route('admin.dashboard') }}" class="waves-effect"><i class="mdi mdi-view-dashboard"></i> <span> Dashboard </span> </a>
                </li>
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-apps"></i> <span> Manage Blogs </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('category.index') }}">Blog Categories</a></li>
                        <li><a href="{{ route('blog.index') }}">Manage Blog</a></li>
                        <li><a href="{{ route('comment.index') }}">Manage Comments</a></li>
                    </ul>
                </li>
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-account-multiple"></i> <span> Manage Users </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('user.index') }}">Users</a></li>
                        <li><a href="{{ route('user.gold') }}">Gold Users</a></li>
                        <li><a href="{{ route('merchant.index') }}">Merchants</a></li>
                        <li><a href="{{ route('salesperson.index') }}">Sales Person</a></li>
                    </ul>
                </li>
                <li class="">
                    <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-qrcode-scan"></i> <span> Manage Vouchers </span><span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('voucher.index') }}">Regular Vouchers</a></li>
                        <li><a href="{{ route('special-voucher.index') }}">Special Vouchers</a></li>
                    </ul>
                </li>
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-comment-question-outline"></i> <span> Manage FAQs </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('faq-category.index') }}">FAQs Categories</a></li>
                        <li><a href="{{ route('faq.index') }}">FAQs</a></li>
                    </ul>
                </li>
                <li class="">
                    <a href="{{ route('order.index') }}" class="waves-effect"><i class="mdi mdi-cart"></i> <span> Manage Orders </span></a>
                </li>
                <li class="">
                    <a href="{{ route('qrcode.index') }}" class="waves-effect"><i class=" mdi mdi-qrcode-scan"></i> <span> QR codes </span></a>
                </li>
                <li class="">
                    <a href="{{ route('page.index') }}" class="waves-effect"><i class="mdi mdi-file-document"></i> <span> Manage CMS </span></a>
                </li>
                <li class="">
                    <a href="{{ route('notification.create') }}" class="waves-effect"><i class="mdi mdi-bell"></i> <span> Notifications </span></a>
                </li>
                <li class="">
                    <a href="{{ route('email.index') }}" class="waves-effect"><i class="mdi mdi-email"></i> <span> Email Templates </span></a>
                </li>
                <li class="">
                    <a href="{{ route('setting.show') }}" class="waves-effect"><i class="mdi mdi-settings"></i> <span> Manage Setting </span></a>
                </li>
                <li class="">
                    <a href="{{ route('locations.show') }}" class="waves-effect"><i class="mdi mdi-map"></i> <span> Manage Locations </span></a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

        {{-- <div class="help-box">
            <h5 class="text-muted m-t-0">For Help ?</h5>
            <p class=""><span class="text-custom">Email:</span> <br/> support@support.com</p>
            <p class="m-b-0"><span class="text-custom">Call:</span> <br/> (+123) 123 456 789</p>
        </div> --}}

    </div>
    <!-- Sidebar -left -->

</div>
