@extends('admin.layout')
@section('title')
    Voucher Beneficiaries | ACAI
@endsection
@push('css')
    @include('admin.includes.datatablescss')
    <link href="{{ asset('adminassets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('adminassets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Voucher Beneficiaries</h4>
                        <div class="pull-right">
                            <a href="{{ route('special-voucher.index') }}" class="btn btn-info"><i class="mdi mdi-format-list-numbers"></i> All Special Vouchers</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="row">
                <div class="col-sm-12">
                    @include('admin.partials.flash')
                    <div class="card-box table-responsive">
                        <table id="datatable-assigned" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date Of birth</th>
                                <th>Registered On</th>
                                <th>Valid From</th>
                                <th>Valid Till</th>
                                <th>Is Redeemed</th>
                                <th>Last Redeemed</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <!-- <tbody>
                            @if (count($assignedUsers))
                                @foreach ($assignedUsers as $assignedUser)
                                    <tr>
                                        <td>{{ $assignedUser->name }}</td>
                                        <td>{{ ($assignedUser->email != '') ? $assignedUser->email : 'N/A' }}</td>
                                        <td>{{ ($assignedUser->dob != '') ? $assignedUser->dob->format('d M Y') : 'N/A' }}</td>
                                        <td>{{ $assignedUser->created_at->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($assignedUser->pivot->valid_from)->format('d M Y') }}</td>
                                        <td id="valid_till{{$assignedUser->pivot->id}}">{{ \Carbon\Carbon::parse($assignedUser->pivot->valid_till)->format('d M Y') }}</td>
                                        <td id="is_redeemed_td{{$assignedUser->pivot->id}}">
                                            @if ($assignedUser->pivot->is_redeemed == 1)
                                                <span class="label label-success">Redeemed <span class="badge badge-danger">{{ $assignedUser->pivot->redemption_count }}</span></span>
                                            @else
                                            <span class="label label-danger">Not Redeemed </span>
                                            @endif
                                        </td>
                                        <td>{{ (!is_null($assignedUser->pivot->redeemed_on)) ? $assignedUser->pivot->redeemed_on->format('d M Y') : 'N/A' }}</td>
                                        <td><button class="btn btn-xs btn-success" data-sp_voucher_pivot_id="{{ $assignedUser->pivot->id }}">Change/Edit</button></td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody> -->
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    @include('admin.partials.flash')
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Search And Assign Voucher To Users
                            <button id="add_all_customers" class="btn btn-sm btn-info ml-2">Add All customers</button>

                            <div class="pull-right">
                                <button id="add_customers" class="btn btn-sm btn-info">Add Selected customers</button>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>DOB Range</label>
                                                <div>
                                                    <div class="input-daterange input-group" id="date-range">
                                                        <input type="text" class="form-control" id="dob_from" />
                                                        <span class="input-group-addon bg-custom text-white b-0">to</span>
                                                        <input type="text" class="form-control" id="dob_to" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Registered date Range</label>
                                            <div id="signup_date_range" class="form-control">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                <span></span>
                                                <input type="hidden" name="from_date" id="from_date">
                                                <input type="hidden" name="to_date" id="to_date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Membership Expiry Date Range </label>
                                            <div id="membership_date_range" class="form-control">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                <span></span>
                                                <input type="hidden" name="membership_from_date" id="membership_from_date">
                                                <input type="hidden" name="membership_to_date" id="membership_to_date">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Membership Tier</label>
                                            <div class="form-group">
                                                <select class="form-control" id="membership_tier">
                                                    <option value="" selected hidden>Membership Tier</option>
                                                    <option value="2">Gold Members</option>
                                                    <option value="1">Purple Members</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="col-md-12">
                                        <br>
                                        <br>
                                        <div class="form-group">
                                            <button class="btn btn-primary" id="apply_filters">Apply Filters</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button class="btn btn-danger" id="reset_filters">Reset Filters</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="portlet portlet-primary">
                                <div class="portlet-heading portlet-primary">
                                    <h5 class="portlet-title text-dark">Filter By Spendings</h5>
                                </div>
                                <div class="clearfix"></div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Spending Date Range </label>
                                                    <div id="spending_date_range" class="form-control">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                        <span></span>
                                                        <input type="hidden" name="spending_from_date" id="spending_from_date">
                                                        <input type="hidden" name="spending_to_date" id="spending_to_date">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Sort By Spendings</label>
                                                    <div class="form-group">
                                                        <select class="form-control" id="spending_sort">
                                                            <option value="" selected hidden>Sort By</option>
                                                            <option value="asc">Low to High</option>
                                                            <option value="desc">High to Low</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <br>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button class="btn btn-primary" id="apply_spending_filters">Apply Filters</button>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button class="btn btn-danger" id="reset_spending_filters">Reset Filters</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <table id="user_table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th><div class="checkbox checkbox-primary">
                                            <input id="selectcheckboxmaster" type="checkbox">
                                            <label for="selectcheckboxmaster">
                                            </label>
                                        </div></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>DOB</th>
                                        <th>Membership</th>
                                        <th>Total Points Earned</th>
                                        <th>Gold Membership Expiry Date</th>
                                        <th>Created at</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="panel-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content p-0 b-0">
                <div class="panel panel-color panel-primary">
                    <div class="panel-heading">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="panel-title" id="modal_head">Change Status/Validity</h3>
                    </div>
                    <form id="sp_voucher_submit">
                        @csrf
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="text" hidden name="sp_voucher_pivot_id" id="sp_voucher_pivot_id" value="">
                                    <div class="form-group">
                                        <label class="control-label">Status <span class="text-danger">*</span></label>
                                        <select  class="form-control" id="is_redeemed" name="is_redeemed">
                                            <option value="1">Redeemed</option>
                                            <option value="0">Not Redeemed</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exp_date">Valid Till</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="valid_till" name="valid_till" placeholder="mm/dd/yyyy">
                                            <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->

    <!-- create a full screen loader -->


    </div><!-- /.modal -->

    <div class="loading_overlay_custom">
        <div class="lds-ripple"><div></div><div></div></div>
    </div>

    <style>
        .loading_overlay_custom {
            position: fixed;
            z-index: 999999;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: rgba(255,255,255,0.8);
            display: none;
            align-items: center;
            justify-content: center;
        }
        .loading_overlay_custom .lds-ripple {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }
        .loading_overlay_custom .lds-ripple div {
            position: absolute;
            border: 4px solid #000;
            opacity: 1;
            border-radius: 50%;
            animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
        }
        .loading_overlay_custom .lds-ripple div:nth-child(2) {
            animation-delay: -0.5s;
        }
        @keyframes lds-ripple {
            0% {
                top: 36px;
                left: 36px;
                width: 0;
                height: 0;
                opacity: 1;
            }
            100% {
                top: 0px;
                left: 0px;
                width: 72px;
                height: 72px;
                opacity: 0;
            }
        }
    </style>

@endsection
@push('scripts')
    @include('admin.includes.datatablesjs')
    <script src="{{ asset('adminassets/plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('adminassets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('adminassets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
       $(document).ready( function () {
        $.fn.dataTable.ext.errMode = 'throw';
        //datatable init
        // $('#datatable-assigned').DataTable( {
        //     "scrollX": true,
        // });

        //dob date picker
        jQuery('#dob').datepicker({

           });
        jQuery('#date-range').datepicker({
            toggleActive: true,
            format: "dd-mm",
                startView: 1,
                maxViewMode: 1,
                autoclose: true,
                todayHighlight: true,
                beforeShowYear: function(date){
                    return false;
                }
        });
        //valid till date
        jQuery('#valid_till').datepicker({
               setDate: new Date(),
               startDate: new Date(),
               autoclose: true,
               todayHighlight: true,
               format: {
                   toDisplay: function (date, format, language) {
                       var d = new Date(date);
                       return moment(d).format('DD-MMM-YYYY');
                   },
                   toValue: function (date, format, language) {
                       var d = new Date(date);
                       return moment(d).format('YYYY-MM-DD HH:mm:ss');
                   }
               }
           });
            $("#selectcheckboxmaster").on("change",function(){
                $.each($(".selectcheckbox"),function(i,itm){
                    if($("#selectcheckboxmaster").is(":checked")){
                        $(itm).prop("checked",true);
                    }else{
                        $(itm).prop("checked",false);
                    }
                });
            });

            //open modal
            $(document).on('click','[data-sp_voucher_pivot_id]',function() {
                $("#sp_voucher_pivot_id").val($(this).attr('data-sp_voucher_pivot_id'));
                $('#panel-modal').modal('show');
            });

            //submit modal
            $("#sp_voucher_submit").on('submit',function(e) {
                e.preventDefault();
                $.ajax({
                    url : '{{ route('special-voucher.updatepivote') }}',
                    type: 'POST',
                    data: $("#sp_voucher_submit").serialize(),
                    beforeSend : function() {
                        $("#sp_voucher_submit").find('button').attr('disabled',true);
                    },
                    complete : function() {
                        $("#sp_voucher_submit").find('button').attr('disabled',false);
                    },
                    success : function(data) {
                        toastr["success"](data.message);
                        $("#valid_till"+$("#sp_voucher_pivot_id").val()).text(data.valid_till);
                        $("#is_redeemed_td"+$("#sp_voucher_pivot_id").val()).html((data.is_redeemed == 1) ? '<span class="badge badge-success">Redeemed</span>' : '<span class="badge badge-warning">Not Redeemed</span>');
                        $("#sp_voucher_submit")[0].reset();
                        $('#panel-modal').modal('hide');
                    },
                    error : function(data) {
                        toastr["error"](data.responseJSON.message);
                    }
                })
            });

            //submit selected
            $("#add_customers").on('click',function(){
                if($('.selectcheckbox:checked').length === 0){
                    alert("Please select atleast one customer");
                } else {
                    var selected_customers = [];
                    $.each($('.selectcheckbox:checked'),function(i,itm){
                        selected_customers.push($(itm).val());
                    });

                    var datatosend = {
                        customers : selected_customers,
                        _token : '{{ csrf_token() }}',
                        voucher_id:{{ request('special_voucher')->id }}
                    }

                    $('.loading_overlay_custom').css('display','flex');

                    $.ajax({
                        url:'{{ route('special-voucher.assigntousers') }}',
                        type:'POST',
                        data: datatosend,
                        success : function(data){
                            window.location.reload();
                        }
                    })
                }
            })

            // add_all_customers
            $("#add_all_customers").on('click',function(){
                var datatosend = {
                    _token : '{{ csrf_token() }}',
                    voucher_id:{{ request('special_voucher')->id }}
                }

                $('.loading_overlay_custom').css('display','flex');

                $.ajax({
                    url:'{{ route('special-voucher.assignToAllUsers') }}',
                    type:'POST',
                    data: datatosend,
                    success : function(data){
                        alert("Voucher assigning started, please wait for the process to complete, it might take some time depending on the number of users");
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }
                })
            })

            var users_table = $('#user_table').DataTable({
                lengthMenu: [ [10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
                processing: true,
                oLanguage: {sProcessing: '<div class="loading-overlay is-active"><span class="fa fa-spinner fa-3x fa-spin"></span></div>'},
                serverSide: true,
                order: [['7','desc']],
                searchDelay: 350,
                ajax: {
                url: "{{ route('special-voucher.searchusers') }}",
                'data': function(data){
                          // Append to data
                          data.dob_from = $("#dob_from").val();
                          data.dob_to = $("#dob_to").val();
                          data.from_date = $("#from_date").val();
                          data.to_date = $("#to_date").val();
                          data.membership_tier = $("#membership_tier").val();
                          data.spending_sort = $("#spending_sort").val();
                          data.membership_from_date = $("#membership_from_date").val();
                          data.membership_to_date = $("#membership_to_date").val();
                          data.spending_from_date = $("#spending_from_date").val();
                          data.spending_to_date = $("#spending_to_date").val();
                          data.voucher_id = {{ request('special_voucher')->id }};
                       }
                },
                'columnDefs': [ {
                        'targets': [0], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                     }],
                columns: [
                {
                    data: 'selectcheckbox',
                    name: 'selectcheckbox'
                },{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    name: 'dob',
                    data: {
                        _: 'dob.display',
                        sort: 'dob.timestamp'
                    }
                },
                {
                data : 'membership_type',
                    render : function(data, type, row, meta) {
                        if(data == 1){
                            data ='<span class="badge badge-purple">Purple Member</span>';
                        }
                        else{
                            data = '<span class="badge badge-gold">Gold Member</span>';
                        }
                        return data;
                    }
                },
                {data : 'total_points_earned',name:'total_points_earned'},
                {
                    name: 'gold_expiring_date',
                    data: {
                        _: 'gold_expiring_date.display',
                        sort: 'gold_expiring_date.timestamp'
                    }
                },

                {
                    name: 'created_at',
                    data: {
                        _: 'created_at.display',
                        sort: 'created_at.timestamp'
                    }
                },
                {
                data : 'account_status',
                    render : function(data, type, row, meta) {
                        if(data == 1){
                            data ='<span class="badge badge-success">Active</span>';
                        }
                        else{
                            data = '<span class="badge badge-danger">Inactive</span>';
                        }
                        return data;
                    }
                },
                ]
            });

            $('#user_table').on('order.dt',  function () {
                let order = users_table.order();
                if(order[0][0] === 5){
                    $("#spending_sort").val(order[0][1]);
                }
              })

            var assigned_table = $('#datatable-assigned').DataTable({
                lengthMenu: [ [10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
                processing: true,
                serverSide: true,
                order: [[3,'desc']],
                searchDelay: 350,
                ajax: {
                url: "{{ route('special-voucher.searchUserWhoAreAssignedVoucher') }}",
                'data': function(data){
                          // Append to data
                        //   data.dob_from = $("#dob_from").val();
                        //   data.dob_to = $("#dob_to").val();
                        //   data.from_date = $("#from_date").val();
                        //   data.to_date = $("#to_date").val();
                        //   data.membership_tier = $("#membership_tier").val();
                        //   data.spending_sort = $("#spending_sort").val();
                        //   data.membership_from_date = $("#membership_from_date").val();
                        //   data.membership_to_date = $("#membership_to_date").val();
                        //   data.spending_from_date = $("#spending_from_date").val();
                        //   data.spending_to_date = $("#spending_to_date").val();
                          data.voucher_id = {{ request('special_voucher')->id }};
                       }
                },
                columns: [
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    name: 'dob',
                    data: {
                        _: 'dob.display',
                        sort: 'dob.timestamp'
                    }
                },
                {
                    name: 'created_at',
                    data: {
                        _: 'created_at.display',
                        sort: 'created_at.timestamp'
                    }
                },
                {
                    name: 'valid_from',
                    data: "valid_from"
                },
                {
                    name: 'valid_till',
                    data: "valid_till"
                },
                {
                    name: 'is_redeemed',
                    data: "is_redeemed",
                    render: function (data, type, row) {
                        return type === 'display' ? data : data;
                    } 
                },
                {
                    name: 'redeemed_on',
                    data: "redeemed_on"
                },
                {
                    data: 'action',
                    name: 'action'
                }
                ],
                columnDefs: [
                    { targets: [0, 1], searchable: true },
                    { targets: '_all', searchable: false }
                ],
            })
            

            // $('#datatable-assigned').on('order.dt',  function () {
            //     let order = assigned_table.order();
            //     if(order[0][0] === 5){
            //         $("#spending_sort").val(order[0][1]);
            //     }
            //   })
            

            $('#apply_filters').click(function(){
                users_table.draw();
              });
            $('#reset_filters').click(function(){
                $("#membership_tier").val('');
                $("#from_date").val('');
                $("#to_date").val('');
                $("#dob").val('');
                $("#membership_from_date").val('');
                $("#membership_to_date").val('');
                $('#signup_date_range span').html('');
                $('#membership_date_range span').html('');
                users_table.draw();
            });

            $('#apply_spending_filters').click(function(){
                users_table.settings()[0].jqXHR.abort();
                $('#reset_filters').trigger('click');
                users_table.draw();
              });
            $('#reset_spending_filters').click(function(){
                $("#spending_sort").val('');
                $("#spending_from_date").val('');
                $("#spending_to_date").val('');
                $('#spending_date_range span').html('');
                users_table.draw();
            });
        });
       $('#signup_date_range span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
       $('#signup_date_range').daterangepicker({
           format: 'MM/DD/YYYY',
           startDate: moment().subtract(29, 'days'),
           endDate: moment(),
           minDate: '01/01/2020',
           maxDate: '12/31/2050',
           dateLimit: {
               days: 365
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
           var d1 = start.format("YYYY-MM-DD");
           var d2 = end.format("YYYY-MM-DD");
           $("#from_date").val(d1);
           $("#to_date").val(d2);
           $('#signup_date_range span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
       });

       //membership range
       $('#membership_date_range span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
       $('#membership_date_range').daterangepicker({
           format: 'MM/DD/YYYY',
           startDate: moment().subtract(29, 'days'),
           endDate: moment(),
           minDate: '01/01/2020',
           maxDate: '12/31/2050',
           dateLimit: {
               days: 365
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
           var d1 = start.format("YYYY-MM-DD");
           var d2 = end.format("YYYY-MM-DD");
           $("#membership_from_date").val(d1);
           $("#membership_to_date").val(d2);
           $('#membership_date_range span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
       });


       //Spending range
       $('#spending_date_range span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
       $('#spending_date_range').daterangepicker({
           format: 'MM/DD/YYYY',
           startDate: moment().subtract(29, 'days'),
           endDate: moment(),
           // minDate: '01/01/2020',
           maxDate: '12/31/2050',
           dateLimit: {
               days: 365
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
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
               'This Year': [moment().startOf('year'), moment()],
               'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
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
           var d1 = start.format("YYYY-MM-DD");
           var d2 = end.format("YYYY-MM-DD");
           $("#spending_from_date").val(d1);
           $("#spending_to_date").val(d2);
           $('#spending_date_range span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
       });

       setTimeout(function(){
            $('.loading-overlay').removeClass('is-active');
       }, 10000);
      </script>
@endpush
