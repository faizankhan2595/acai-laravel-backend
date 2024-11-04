@extends('admin.layout')
@push('css')
    @include('admin.includes.datatablescss')
    <link href="{{ asset('adminassets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('adminassets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Users  </h4>
                        <div class="pull-right">
                            <a href="{{ route('user.trash') }}" class="btn btn-danger"><i class=" mdi mdi-delete"></i> Deleted Users</a>
                            <a href="{{ route('user.create') }}" class="btn btn-success">Add New</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-sm-12">
                    @include('admin.partials.flash')
                    @if (!is_null(Session::get('fail_errors')))
                    @foreach (Session::get('fail_errors') as $i => $e)
                        <div class="alert alert-danger alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Row {{ $i }}</strong>
                          <ul>
                            @foreach ($e as $single_err)
                                 <li>{{ $single_err }}</li>
                             @endforeach
                          </ul>
                        </div>
                    @endforeach
                    @endif
                    <div class="card-box table-responsive">
                    <div class="portlet portlet-default">
                        <div class="portlet-heading portlet-default">
                            <h5 class="portlet-title text-dark">Filters</h5>
                        </div>
                        <div class="clearfix"></div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-12">
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
                                                <input type="hidden" name="reg_from_date" id="reg_from_date">
                                                <input type="hidden" name="reg_to_date" id="reg_to_date">
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
                                        <div class="col-md-3">
                                            <label></label>
                                            <div class="form-group">
                                                <button class="btn btn-primary form-control" id="apply_filters">Apply Filters</button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label></label>
                                            <div class="form-group">
                                                <button class="btn btn-danger form-control" id="reset_filters">Reset Filters</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="user_table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>DOB</th>
                                <th>Membership</th>
                                <th>Gold Member Since</th>
                                <th>Gold Membership Expiry Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
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
                        <h3 class="panel-title" id="modal_head">Credit/Debit Points</h3>
                    </div>
                    <form id="transaction_submit">
                        @csrf
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="text" hidden name="transaction_user" id="transaction_user" value="">
                                    <div class="form-group">
                                        <label class="control-label">Transaction Value <span class="text-danger">*</span></label>
                                        <input type="number" id="transaction_value" required parsley-trigger="change" class="form-control" name="transaction_value" placeholder="Transaction Value">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Transaction Type <span class="text-danger">*</span></label>
                                        <select  class="form-control" id="transaction_type" name="transaction_type">
                                            <option value="1">Credit</option>
                                            <option value="2">Debit</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exp_date">Expiry date (For Credit only)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="exp_date" name="exp_date" placeholder="mm/dd/yyyy">
                                            <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Transaction Message <span class="text-danger">*</span></label>
                                        <textarea class="form-control" required parsley-trigger="change" id="transaction_message" name="transaction_message" placeholder="Transaction Transaction"></textarea>
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

    </div><!-- /.modal -->

@endsection
@push('scripts')
@include('admin.includes.datatablesjs')
<script src="{{ asset('adminassets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/moment/moment.js') }}"></script>
<script src="{{ asset('adminassets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
    jQuery('#exp_date').datepicker({
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
</script>
<script>
   $(document).ready( function () {
        var users_table = $('#user_table').DataTable({
            processing: true,
            oLanguage: {sProcessing: '<div class="loading-overlay is-active"><span class="fa fa-spinner fa-3x fa-spin"></span></div>'},
            "order": [['5','desc']],
            // oLanguage: {
            //     sProcessing: "<img src='{{ asset('adminassets/assets/images/load.gif') }}'>"
            // },
            serverSide: true,
            ajax: {
            url: "{{ route('user.gold') }}",
             'data': function(data){
               // Append to data
               data.dob_from = $("#dob_from").val();
               data.dob_to = $("#dob_to").val();
               data.reg_from_date = $("#reg_from_date").val();
               data.reg_to_date = $("#reg_to_date").val();
               data.membership_from_date = $("#membership_from_date").val();
               data.membership_to_date = $("#membership_to_date").val();
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
                data: 'mobile_number',
                name: 'mobile_number'
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
            {
                name: 'gold_activation_date',
                data: {
                    _: 'gold_activation_date.display',
                    sort: 'gold_activation_date.timestamp'
                }
            },{
                name: 'gold_expiring_date',
                data: {
                    _: 'gold_expiring_date.display',
                    sort: 'gold_expiring_date.timestamp'
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
            {
                data: 'action',
                name: 'action',
                orderable: false
            }
            ]
        });

        //filters stuff
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

                $('#apply_filters').click(function(){
                    users_table.draw();
                  });
                $('#reset_filters').click(function(){
                    $("#reg_from_date").val('');
                    $("#reg_to_date").val('');
                    $("#membership_from_date").val('');
                    $("#membership_to_date").val('');
                    $("#dob_from").val('');
                    $("#dob_to").val('');
                    $('#signup_date_range span').html('');
                    $('#membership_date_range span').html('');
                    users_table.draw();
                });

           //$('#signup_date_range span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
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
               $("#reg_from_date").val(d1);
               $("#reg_to_date").val(d2);
               $('#signup_date_range span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
           });

           //membership exp date range
           //$('#membership_date_range span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
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
        });
  </script>


    <script>
        $(document).on('click','[data-transactionuser]',function() {
            $("#transaction_user").val($(this).attr('data-transactionuser'));
            $('#panel-modal').modal('show');
        });

        $("#transaction_submit").on('submit',function(e) {
            e.preventDefault();
            $.ajax({
                url : '{{ route('user.dotransaction') }}',
                type: 'POST',
                data: $("#transaction_submit").serialize(),
                beforeSend : function() {
                    $("#transaction_submit").find('button').attr('disabled',true);
                },
                complete : function() {
                    $("#transaction_submit").find('button').attr('disabled',false);
                },
                success : function(data) {
                    toastr["success"](data.message);
                    $("#transaction_submit")[0].reset();
                    $('#panel-modal').modal('hide');
                },
                error : function(data) {
                    toastr["error"](data.responseJSON.message);
                }
            })
        });
    </script>
@endpush
