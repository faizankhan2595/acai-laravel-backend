@extends('admin.layout')
@section('title')
    Dashboard | ACAI
@endsection
@push('css')
    <link href="{{ asset('adminassets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <style type="text/css">
        .spinner{
            position: absolute;
            left: 50%;
            top: 50%;
            font-size: 25px;
        }
        .op-5{
            opacity: 0.5;
            pointer-events: none;
        }
        .op-f{
            opacity: 1;
            pointer-events: all;
        }
    </style>
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Dashboard</h4>

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('user.index') }}">
                    <div class="card-box widget-box-two widget-two-primary">
                        <i class="mdi mdi-account-multiple widget-two-icon"></i>
                        <div class="wigdet-two-content">
                            <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Customers">Total Customers</p>
                            <h2><span data-plugin="counterup">{{$data['usercount']}}</span></h2>
                        </div>
                    </div>
                </a>
            </div><!-- end col -->

            <div class="col-lg-3 col-md-6">
                <a href="{{ route('merchant.index') }}">
                    <div class="card-box widget-box-two widget-two-warning">
                        <i class="mdi mdi-account-convert widget-two-icon"></i>
                        <div class="wigdet-two-content">
                            <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Merchants">Total Merchants</p>
                            <h2><span data-plugin="counterup">{{$data['merchantcount']}}</span> </h2>
                        </div>
                    </div>
                </a>
            </div><!-- end col -->

            <div class="col-lg-3 col-md-6">
                <a href="{{ route('salesperson.index') }}">
                    <div class="card-box widget-box-two widget-two-success">
                        <i class="mdi mdi-account-check widget-two-icon"></i>
                        <div class="wigdet-two-content">
                            <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Sales Persons">Total Sales Persons</p>
                            <h2><span data-plugin="counterup">{{$data['salescount']}}</span></h2>
                        </div>
                    </div>
                </a>
            </div><!-- end col -->

            <div class="col-lg-3 col-md-6">
                <a href="{{ route('blog.index') }}">
                    <div class="card-box widget-box-two widget-two-info">
                        <i class="mdi mdi-tooltip-edit widget-two-icon"></i>
                        <div class="wigdet-two-content">
                            <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="User Today">Total Blogs</p>
                            <h2><span data-plugin="counterup">{{ $data['blogcount'] }}</span></h2>
                        </div>
                    </div>
                </a>
            </div><!-- end col -->

        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-md-6">
                <div class="portlet portlet-default chart1 op-f">
                    <div class="portlet-heading">
                        <h3 class="portlet-title ">
                            New customer signup trend
                        </h3>
                        <div class="portlet-widgets">
                            <div id="signup_date_range" class="form-control">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="bg-inverse" class="panel-collapse collapse in">
                        <div class="portlet-body">
                            <i class="fa fa-refresh fa-spin spinner" id="spin_1"></i>
                            <canvas id="signup_trend_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="portlet portlet-default chart2 op-f">
                    <div class="portlet-heading">
                        <h3 class="portlet-title ">
                            New orders trend
                        </h3>
                        <div class="portlet-widgets">
                            <div id="order_trend_daterange" class="form-control">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="bg-inverse" class="panel-collapse collapse in">
                        <div class="portlet-body">
                            <i class="fa fa-refresh fa-spin spinner" id="spin_2"></i>
                            <canvas id="order_trend_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-md-6">
                <div class="portlet portlet-default chart3 op-f">
                    <div class="portlet-heading">
                        <h3 class="portlet-title ">
                            Sales trend
                        </h3>
                        <div class="portlet-widgets">
                            <div id="sales_trend_date_range" class="form-control">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="bg-inverse" class="panel-collapse collapse in">
                        <div class="portlet-body">
                            <i class="fa fa-refresh fa-spin spinner" id="spin_3"></i>
                            <canvas id="sales_trend_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="portlet portlet-default chart4 op-f">
                    <div class="portlet-heading">
                        <h3 class="portlet-title ">
                            Redemption trend
                        </h3>
                        <div class="portlet-widgets">
                            <div id="redemption_trend_daterange" class="form-control">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="bg-inverse" class="panel-collapse collapse in">
                        <div class="portlet-body">
                            <i class="fa fa-refresh fa-spin spinner" id="spin_4"></i>
                            <canvas id="redemption_trend_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->


        <div class="row">
            <div class="col-md-8">
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-6"><h4 class="header-title m-t-0 m-b-30">Recent Users</h4></div>
                        <div class="col-md-6"><a href="{{ route('user.index') }}" class="pull-right">View All</a></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table table-hover m-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Verified</th>
                                    <th>Dob</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentusers as $user)
                                    <tr>
                                        <th>
                                            <img src="{{ ($user->avatar) ? url('/') . Storage::url($user->avatar) : url('/') . Storage::url('/user/default/' . strtoupper(substr($user->name,0,1)) . '.png') }}" alt="{{$user->name}}" class="thumb-sm img-circle" />
                                        </th>
                                        <td>
                                            <h5 class="m-0">{{ucfirst($user->name)}}</h5>
                                            <p class="m-0 text-muted font-13"><small>{{$user->email}}</small></p>
                                        </td>
                                        <td>{{(!is_null($user->mobile_number) ? $user->mobile_number : 'N/A')}}</td>
                                        @if (!is_null($user->email_verified_at))
                                            <td><span class="badge badge-success">Verified</span></td>
                                        @else
                                        <td><span class="badge badge-danger">Not Verified</span></td>
                                        @endif
                                        <td>{{(!is_null($user->dob)) ? $user->dob->format('d M Y') : 'N/A'}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div> <!-- table-responsive -->
                </div> <!-- end card -->
            </div>
            <!-- end col -->

            <div class="col-md-4">
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-6"><h4 class="header-title m-t-0 m-b-30">Latest Comments</h4></div>
                        <div class="col-md-6"><a href="{{ route('comment.index') }}" class="pull-right">View All</a></div>
                    </div>
                    <div class="inbox-widget slimscroll-alt" style="min-height: 302px;">
                        @foreach ($comments as $comment)
                            <a href="javascript:void(0)" data-comment="" data-blog_title="{{ $comment->blog->title }}" data-comment_body="{{ $comment->comment_body }}" data-comment_id="{{ $comment->id }}">
                                <div class="inbox-item">
                                    <div class="inbox-item-img"><img src="{{ ($comment->user->avatar) ? url('/') . Storage::url($comment->user->avatar) : url('/') . Storage::url('/user/default/' . strtoupper(substr($comment->user->name,0,1)) . '.png') }}" alt="{{$comment->user->name}}"></div>
                                    <p class="inbox-item-author">{{ $comment->user->name }}</p>
                                    <p class="inbox-item-text">{{str_limit($comment->comment_body, 28)}} </p>
                                    <p class="inbox-item-date">{{ $comment->created_at->format('d M Y g:i A') }} <span {{($comment->status == 1) ? 'hidden' : ''}} id="comment_{{$comment->id}}"><i class="fa fa-ban text-danger"></i></span></p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                </div> <!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-6"><h4 class="header-title m-t-0 m-b-30">Top 10 customers</h4></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table table-hover m-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Point Balance</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Dob</th>
                                    <th>Member Since</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($topcustomers) > 0)
                                    @foreach ($topcustomers as $topuser)
                                        <tr>
                                            <th>
                                                <img src="{{ ($topuser->avatar) ? url('/') . Storage::url($topuser->avatar) : url('/') . Storage::url('/user/default/' . strtoupper(substr($topuser->name,0,1)) . '.png') }}" alt="{{$topuser->name}}" class="thumb-sm img-circle" />
                                            </th>
                                            <td>{{(!is_null($topuser->balance) ? $topuser->balance : 0)}}</td>
                                            <td>
                                                <h5 class="m-0">{{ucfirst($topuser->name)}}</h5>
                                                <p class="m-0 text-muted font-13"><small>{{ $topuser->email }}</small></p>
                                            </td>
                                            <td>{{(!is_null($topuser->mobile_number) ? $topuser->mobile_number : 'N/A')}}</td>
                                            <td>{{(!is_null($topuser->dob)) ? date('d M Y',strtotime($topuser->dob)) : 'N/A'}}</td>
                                            <td>{{ date('d M Y',strtotime($topuser->created_at)) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center"><td colspan="6">No records found!</td></tr>
                                @endif
                            </tbody>
                        </table>

                    </div> <!-- table-responsive -->
                </div> <!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-6"><h4 class="header-title m-t-0 m-b-30">Top 10 Merchants</h4></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table table-hover m-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Merchant Name</th>
                                    <th>Email</th>
                                    <th>Rewards scanned</th>
                                    <th>Total Value</th>
                                    <th>Member Since</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($topmerchants->count() > 0)
                                    @foreach ($topmerchants as $topmerchnatorder)
                                        <tr>
                                            <th>
                                                <img src="{{ ($topmerchnatorder->scannedBy->avatar) ? url('/') . Storage::url($topmerchnatorder->scannedBy->avatar) : url('/') . Storage::url('/user/default/' . strtoupper(substr($topmerchnatorder->scannedBy->name,0,1)) . '.png') }}" alt="{{$topmerchnatorder->scannedBy->name}}" class="thumb-sm img-circle" />
                                            </th>
                                            <td>{{ucfirst($topmerchnatorder->scannedBy->name)}}</td>
                                            <td>{{(!is_null($topmerchnatorder->scannedBy->email) ? $topmerchnatorder->scannedBy->email : 'N/A')}}</td>
                                            <td>{{ $topmerchnatorder->totalorder }}</td>
                                            <td>{{ $topmerchnatorder->totalamount }}</td>
                                            <td>{{ $topmerchnatorder->scannedBy->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center"><td colspan="6">No records found!</td></tr>
                                @endif
                            </tbody>
                        </table>

                    </div> <!-- table-responsive -->
                </div> <!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-6"><h4 class="header-title m-t-0 m-b-30">Inactive Users</h4></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table table-hover m-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Customer Name</th>
                                    <th>Current Balance</th>
                                    <th>Last Expiry Date</th>
                                    <th>DOB</th>
                                    <th>Member Since</th>
                                    <th>All Transaction</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($inactivecustomers->count() > 0)
                                    @foreach ($inactivecustomers as $inactiveuser)
                                        @if($inactiveuser->user)
                                            <tr>
                                                <th>
                                                    <img src="{{ ($inactiveuser->user->avatar) ? url('/') . Storage::url($inactiveuser->user->avatar) : url('/') . Storage::url('/user/default/' . strtoupper(substr($inactiveuser->user->name,0,1)) . '.png') }}" alt="{{$inactiveuser->user->name}}" class="thumb-sm img-circle" />
                                                </th>
                                                <td>
                                                    <h5 class="m-0">{{ucfirst($inactiveuser->user->name)}}</h5>
                                                    <p class="m-0 text-muted font-13"><small>{{ $inactiveuser->user->email }}</small></p>
                                                </td>
                                                <td>{{ $inactiveuser->user->balance() }}</td>
                                                <td>{{ $inactiveuser->user->lastExpired() }}</td>
                                                <td>{{ (!is_null($inactiveuser->user->dob)) ? $inactiveuser->user->dob->format('d M Y') : 'NA' }}</td>
                                                <td>{{ $inactiveuser->user->created_at->format('d M Y') }}</td>
                                                <td><a href="{{ route('point-transaction.list',$inactiveuser->user->id) }}">All transactions</a></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr class="text-center"><td colspan="7">No records found!</td></tr>
                                @endif
                            </tbody>
                        </table>

                    </div> <!-- table-responsive -->
                </div> <!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

    </div> <!-- container -->

    <div id="panel-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content p-0 b-0">
                <div class="panel panel-color panel-primary">
                    <div class="panel-heading">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="panel-title" id="modal_blog_title"></h3>
                    </div>
                    <div class="panel-body">
                        <div><p id="modal_comment_body"></p></div>
                        <div class="modal-footer">
                            <button data-commentid="" id="commentid" type="button" class="btn btn-default waves-effect" data-dismiss="modal">Disable Comment</button>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@push('scripts')
<script src="{{ asset('adminassets/plugins/moment/moment.js') }}"></script>
<script src="{{ asset('adminassets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- Chart JS -->
<script src="{{ asset('adminassets/plugins/chart.js/chart.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            var today = moment();
            var d1 = today.startOf('month').format("YYYY-MM-DD");
            var d2 = today.endOf('month').format("YYYY-MM-DD");
            signupTrend(d1,d2);
            orderTrend(d1,d2);
            salesTrend(d1,d2);
            redemptionTrend(d1,d2);
        });
        $(document).on('click','[data-comment]',function() {
            $("#modal_blog_title").text($(this).data('blog_title'));
            $("#modal_comment_body").text($(this).data('comment_body'));
            $("#commentid").attr('data-commentid',$(this).data('comment_id'));
            $('#panel-modal').modal('show');
        });
        $("#commentid").on('click',function(){
            var commentId = $(this).attr('data-commentid');
            $.ajax({
                url:'{{ route('comment.disable') }}',
                type:'POST',
                dataType:'JSON',
                data:{_token:'{{ csrf_token() }}',comment_id:commentId},
                success : function(data) {
                    toastr["success"](data.message);
                    $("#comment_"+commentId).prop('hidden',false);
                    $('#panel-modal').modal('hide');
                },
                error : function(data) {
                    toastr["error"](data.responseJSON.message);
                }
            })
        })
        /**
         * [Signup Trend Chart Start]
         * @type {Boolean}
         */

        $('#signup_date_range span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
        $('#signup_date_range').daterangepicker({
            format: 'MM/DD/YYYY',
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            minDate: '01/01/2020',
            maxDate: '12/31/2050',
            dateLimit: {
                days: 1860
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-success',
            cancelClass: 'btn-default',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Cancel',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        }, function (start, end, label) {
            var d1 = start.format("YYYY-MM-DD HH:mm:ss");
            var d2 = end.format("YYYY-MM-DD HH:mm:ss");
            signupTrend(d1,d2);
            $('#signup_date_range span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
        });

        var ctx = document.getElementById('signup_trend_chart').getContext('2d');
        var linechart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'line',
            options: {
                 legend: {
                    display: false
                 },
            },
            // The data for our dataset
            data: {
                labels: [],
                datasets: [{
                    label: 'New Signups',
                    borderColor: '#9966ff',
                    backgroundColor:'transparent',
                    data: [],
                    responsive: true,
                    maintainAspectRatio: false,
                }],
            },
        });
        //refresh chart
        function signupTrend(d1,d2){
            var datasend = {
                'from' : d1,
                'to' : d2,
                '_token' : "{{ csrf_token() }}",
            }

            $.ajax({
                url: '{{ route('signup-trend') }}',
                type:'POST',
                data:datasend,
                dataType :'JSON',
                beforeSend:function(){
                    $('.chart1').removeClass('op-f');
                    $('.chart1').addClass('op-5');
                    $('#spin_1').css('display','inline-block');
                },
                complete:function(){
                    $('.chart1').removeClass('op-5');
                    $('.chart1').addClass('op-f');
                    $('#spin_1').css('display','none');
                },
                success:function(ret){
                    linechart.data.labels = ret['labels'];
                    linechart.update();
                    linechart.data.datasets[0].data = ret['count'];
                    linechart.update();
                }
            });
        }
        //Signup trend chart end

        /**
         * Order Trend chart start
         */
        $('#order_trend_daterange span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
        $('#order_trend_daterange').daterangepicker({
            format: 'MM/DD/YYYY',
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            minDate: '01/01/2020',
            maxDate: '12/31/2050',
            dateLimit: {
                days: 1860
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-success',
            cancelClass: 'btn-default',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Cancel',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        }, function (start, end, label) {
            var d1 = start.format("YYYY-MM-DD HH:mm:ss");
            var d2 = end.format("YYYY-MM-DD HH:mm:ss");
            $('#order_trend_daterange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            orderTrend(d1,d2);
        });

        var order_ctx = document.getElementById('order_trend_chart').getContext('2d');
        var order_trend_chart = new Chart(order_ctx, {
            // The type of chart we want to create
            type: 'line',
            options: {
                 legend: {
                    display: false
                 },
            },
            // The data for our dataset
            data: {
                labels: [],
                datasets: [{
                    label: 'New Orders',
                    borderColor: '#8BC34A',
                    backgroundColor:'transparent',
                    data: [],
                    responsive: true,
                    maintainAspectRatio: false,
                }],
            },
        });
        //refresh chart
        function orderTrend(d1,d2){
            var datasend = {
                'from' : d1,
                'to' : d2,
                '_token' : "{{ csrf_token() }}",
            }

            $.ajax({
                url: '{{ route('order-trend') }}',
                type:'POST',
                data:datasend,
                dataType :'JSON',
                beforeSend:function(){
                    $('.chart2').removeClass('op-f');
                    $('.chart2').addClass('op-5');
                    $('#spin_2').css('display','inline-block');
                },
                complete:function(){
                    $('.chart2').removeClass('op-5');
                    $('.chart2').addClass('op-f');
                    $('#spin_2').css('display','none');
                },
                success:function(ret){
                    order_trend_chart.data.labels = ret['labels'];
                    order_trend_chart.update();
                    order_trend_chart.data.datasets[0].data = ret['count'];
                    order_trend_chart.update();
                }
            });
        }
        //Order trend chart end

        /**
         * Sales Trend chart start
         */
        $('#sales_trend_date_range span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
        $('#sales_trend_date_range').daterangepicker({
            format: 'MM/DD/YYYY',
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            minDate: '01/01/2020',
            maxDate: '12/31/2050',
            dateLimit: {
                days: 1860
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-success',
            cancelClass: 'btn-default',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Cancel',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        }, function (start, end, label) {
            var d1 = start.format("YYYY-MM-DD HH:mm:ss");
            var d2 = end.format("YYYY-MM-DD HH:mm:ss");
            $('#sales_trend_date_range span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            salesTrend(d1,d2);
        });

        var sales_ctx = document.getElementById('sales_trend_chart').getContext('2d');
        var sales_trend_chart = new Chart(sales_ctx, {
            // The type of chart we want to create
            type: 'line',
            options: {
                 legend: {
                    display: false
                 },
            },
            // The data for our dataset
            data: {
                labels: [],
                datasets: [{
                    label: 'Total Order Amount',
                    borderColor: '#E64A19',
                    backgroundColor:'transparent',
                    data: [],
                    responsive: true,
                    maintainAspectRatio: false,
                }],
            },
        });
        //refresh chart
        function salesTrend(d1,d2){
            var datasend = {
                'from' : d1,
                'to' : d2,
                '_token' : "{{ csrf_token() }}",
            }

            $.ajax({
                url: '{{ route('sales-trend') }}',
                type:'POST',
                data:datasend,
                dataType :'JSON',
                beforeSend:function(){
                    $('.chart3').removeClass('op-f');
                    $('.chart3').addClass('op-5');
                    $('#spin_3').css('display','inline-block');
                },
                complete:function(){
                    $('.chart3').removeClass('op-5');
                    $('.chart3').addClass('op-f');
                    $('#spin_3').css('display','none');
                },
                success:function(ret){
                    sales_trend_chart.data.labels = ret['labels'];
                    sales_trend_chart.update();
                    sales_trend_chart.data.datasets[0].data = ret['count'];
                    sales_trend_chart.update();
                }
            });
        }
        //Sales trend chart end


        /**
         * redemptio Trend chart start
         */
        $('#redemption_trend_daterange span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
        $('#redemption_trend_daterange').daterangepicker({
            format: 'MM/DD/YYYY',
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            minDate: '01/01/2020',
            maxDate: '12/31/2050',
            dateLimit: {
                days: 1860
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-success',
            cancelClass: 'btn-default',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Cancel',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        }, function (start, end, label) {
            var d1 = start.format("YYYY-MM-DD HH:mm:ss");
            var d2 = end.format("YYYY-MM-DD HH:mm:ss");
            $('#redemption_trend_daterange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            redemptionTrend(d1,d2);
        });

        var rememption_ctx = document.getElementById('redemption_trend_chart').getContext('2d');
        var redemption_trend_chart = new Chart(rememption_ctx, {
            // The type of chart we want to create
            type: 'line',
            options: {
                 legend: {
                    display: true,
                    position:'bottom',
                    labels: {
                        boxWidth: 12,
                       usePointStyle: true,
                    },
                 },
            },
            // The data for our dataset
            data: {
                labels: [],
                datasets: [{
                    label: 'Total Order Amount',
                    borderColor: '#E64A19',
                    backgroundColor:'transparent',
                    data: [],
                    responsive: true,
                    maintainAspectRatio: false,
                },{
                    label: 'Merchant 1',
                    borderColor: '#FF9800',
                    backgroundColor:'transparent',
                    data: [],
                    responsive: true,
                    maintainAspectRatio: false,
                },{
                    label: 'Merchant 2',
                    borderColor: '#0288D1',
                    backgroundColor:'transparent',
                    data: [],
                    responsive: true,
                    maintainAspectRatio: false,
                },{
                    label: 'Merchant 3',
                    borderColor: '#795548',
                    backgroundColor:'transparent',
                    data: [],
                    responsive: true,
                    maintainAspectRatio: false,
                },{
                    label: 'Merchant 4',
                    borderColor: '#009688',
                    backgroundColor:'transparent',
                    data: [],
                    responsive: true,
                    maintainAspectRatio: false,
                },{
                    label: 'Merchant 5',
                    borderColor: '#455A64',
                    backgroundColor:'transparent',
                    data: [],
                    responsive: true,
                    maintainAspectRatio: false,
                }],
            },
        });
        //refresh chart
        function redemptionTrend(d1,d2){
            var datasend = {
                'from' : d1,
                'to' : d2,
                '_token' : "{{ csrf_token() }}",
            }

            $.ajax({
                url: '{{ route('rememption-trend') }}',
                type:'POST',
                data:datasend,
                dataType :'JSON',
                beforeSend:function(){
                    $('.chart4').removeClass('op-f');
                    $('.chart4').addClass('op-5');
                    $('#spin_4').css('display','inline-block');
                },
                complete:function(){
                    $('.chart4').removeClass('op-5');
                    $('.chart4').addClass('op-f');
                    $('#spin_4').css('display','none');
                },
                success:function(ret){
                    redemption_trend_chart.data.labels = ret['labels'];
                    redemption_trend_chart.update();

                    var i = 0;
                    $.each(ret['data'],function(index,item){
                        redemption_trend_chart.data.datasets[i].label = index;
                        redemption_trend_chart.data.datasets[i].data = item;
                        i++;
                    });
                        redemption_trend_chart.update();
                }
            });
        }
        //redemptio trend chart end

    </script>
@endpush
