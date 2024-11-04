@extends('admin.layout')
@push('css')
    @include('admin.includes.datatablescss')
@endpush
@section('content')
    <style>
        .dt-buttons.btn-group {float: right;margin-left: 8px;}a.btn.btn-default.buttons-excel.buttons-html5 {padding: 4px 14px;}
    </style>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Point Transaction</h4>
                        <div class="pull-right">
                            <h4 class="page-title">Balance : {{ $balance }} @if (!is_null($expiringnext))
                                | <small class="text-danger">{{ $expiringnext->points }} Points Expiring On {{ $expiringnext->expiring_on->format('d M Y') }}</small>
                            @endif</h4>
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
                        <table id="pointtransaction_table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Name</th>
                                <th>Transaction Value</th>
                                <th>Transaction Type</th>
                                <th>Message</th>
                                <th>Created At</th>
                                <th>Expire On</th>
                                <th>Balance</th>
                                <th>Expired Points</th>
                                <th>Points Available</th>
                                <th>Sales User</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
   $(document).ready( function () {
        $('#pointtransaction_table').DataTable({
            lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            processing: true,
            // oLanguage: {
            //     sProcessing: "<img src='{{ asset('adminassets/assets/images/load.gif') }}'>"
            // },
            serverSide: true,
            "order": [['5','desc']],
            ajax: {
            url: "{{ route('point-transaction.list',$id) }}",
            },
            dom: 'lBfrtip',
            buttons: [
                'excel'
            ],
            columns: [
            {
                data : 'id',
                render : function(data, type, row, meta) {
                    return data;
                }
		    },    
            {
                data : 'user',
                render : function(data, type, row, meta) {
                    if(data){
                        return data.name;
                    }
                    else{
                        return 'N/A';
                    }
                }
		    },
            {
                data : 'transaction_value',
                render : function(data, type, row, meta) {
                    if(data){
                        return data;
                    } else if(data==0) {
                        return data;
                    } else {
                        return 'N/A';
                    }
                }
		    },
            {
            data : 'transaction_type',
                render : function(data, type, row, meta) {
                    if(data == 1){
                        data ='<span class="badge badge-success">Credit</span>';
                    }
                    else{
                        data = '<span class="badge badge-danger">Debit</span>';
                    }
                    return data;
                }
            },
            {
               data: 'data',
               type: 'string',
               render: {
                  _: 'display',
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
                name: 'expiring_on',
                data: {
                    _: 'expiring_on.display',
                    sort: 'expiring_on.timestamp'
                }
            },
            {
                data: 'balance',
                name: 'balance',
                orderable: false
            },
            {
                data: 'expired_points',
                name: 'expired_points',
                orderable: false
            },
            {
                data: 'points_a',
                name: 'points_a',
                orderable: false
            },
            {
                data: 'sales_user',
                name: 'sales_user',
                orderable: false
            }
            ]
        });
    });
  </script>
    @include('admin.includes.datatablesjs')
@endpush
