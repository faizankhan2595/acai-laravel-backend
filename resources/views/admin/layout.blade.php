<!DOCTYPE html>
<html lang="en">
    @include('admin.partials.head')

    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            @include('admin.partials.topbar')


            <!-- ========== Left Sidebar Start ========== -->
            @include('admin.partials.leftsidebar')
            <!-- Left Sidebar End -->



            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">

                    @yield('content')

                </div> <!-- content -->

                @include('admin.partials.footer')

            </div>


            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


            <!-- Right Sidebar -->
            {{-- @include('admin.partials.rightsidebar') --}}
            <!-- /Right-bar -->

        </div>
        <!-- END wrapper -->
        @include('admin.partials.scripts')
        @stack('scripts')
    </body>
</html>
