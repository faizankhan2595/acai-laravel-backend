@extends('admin.layout')
@push('css')
    @include('admin.includes.datatablescss')
@endpush
@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">
                        All Orders
                    </h4>
                    <div class="pull-right">

                        <a class="btn btn-danger" href="{{ route('order.trash') }}">
                            <i class="mdi mdi-delete">
                            </i>
                            Trash
                        </a>
                        <a class="btn btn-success" href="{{ route('order.export') }}"><span><i class="mdi mdi-export">
                            </i> Export to Excel</span></a>
                    </div>
                    <div class="clearfix">
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
        <div class="row">
            <div class="col-sm-12">
                @include('admin.partials.flash')
                <div class="card-box table-responsive">
                    <table class="table table-striped table-bordered" id="order_table">
                        <thead>
                            <tr>
                                <th>
                                    Order Id
                                </th>
                                <th>
                                    User
                                </th>
                                <th>
                                    Merchant(Voucher)
                                </th>
                                <th>
                                    Price
                                </th>
                                <th>
                                    Created At
                                </th>
                                <th>
                                    Redeemed
                                </th>
                                <th>
                                    Redeemed On
                                </th>
                                <th>
                                    Action
                                </th>
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
                    <h3 class="panel-title" id="modal_head">Order Details</h3>
                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <img src="" width="200" height="200" id="modal_qr_path">
                                </div>
                            </div>
                            <div class="col-md-6 col-md-offset-3">
                                <div class="well well-sm mt-10 text-center" id="modal_coupon_code">HGFRTPLMJHTYD</div>
                            </div>
                            <div class="col-md-12 well well-sm mt-10 text-center" id="scanned_by_div" hidden>
                                <div>QR code scanned by <span class="text-danger" id="scanned_by_name">Ravi Singh</span> On <span class="text-danger" id="scanned_by_date">24 May 2020</span></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
@push('scripts')
<script>
    $(document).ready( function () {
        //render table
        $('#order_table').DataTable({
            processing: true,
            // oLanguage: {
            //     sProcessing: "<img src='{{ asset('adminassets/assets/images/load.gif') }}'>"
            // },
            serverSide: true,
            ajax: {
            url: "{{ route('order.index') }}",
            },
            columns: [
            {
            data : 'order_id',
                render : function(data, type, row, meta) {
                    if(data){
                        return data;
                    }
                    else{
                        return 'N/A';
                    }
                }
            },
            {
            data : 'user',
            name : 'user.name',
                render : function(user, type, row, meta) {
                    if(user){
                        return user.name;
                    }
                    else{
                        return 'N/A';
                    }
                }
            },
            {
            data : 'voucher',
            name : 'voucher.title',
                render : function(voucher, type, row, meta) {
                    if(voucher){
                        return voucher.title+' ('+voucher.merchant.name+')';
                    }
                    else{
                        return 'N/A';
                    }
                }
            },
            {
            data : 'amount',
                render : function(data, type, row, meta) {
                    if(data){
                        return data+' Points';
                    }
                    else{
                        return 'N/A';
                    }
                }
            },
            {
               data: 'created_at',
               type: 'num',
               render: {
                  _: 'display',
                  sort: 'timestamp'
               }
            },
            {
            data : 'is_redeemed',
                render : function(data, type, row, meta) {
                    if(data == 1){
                        data ='<span class="badge badge-success">Redeemed</span>';
                    }
                    else{
                        data = '<span class="badge badge-danger">Not Redeemed</span>';
                    }
                    return data;
                }
            },
            {
               data: 'redeemed_on',
               type: 'num',
               render: {
                  _: 'display',
                  sort: 'timestamp'
               }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false
            }
            ]
        });
    });
</script>
<script>
    $(document).on('click','[data-order]',function() {
        var order = {
            order_id : $(this).attr('data-order'),
            is_redeemed : $(this).attr('data-is_redeemed'),
            redeemed_on : $(this).attr('data-redeemed_on'),
            coupon_code : $(this).attr('data-coupon_code'),
            qr_code_path : $(this).attr('data-qr_code_path'),
            scanned_by : $(this).attr('data-scanned_by'),
        }
        $("#modal_coupon_code").text(order.coupon_code);
        $("#modal_qr_path").attr('src',order.qr_code_path);

        if(order.is_redeemed == 1){
            $("#scanned_by_div").attr('hidden',false);
            $("#scanned_by_name").text(order.scanned_by);
            $("#scanned_by_date").text(order.redeemed_on);
        }
        else{
            $("#scanned_by_div").attr('hidden',true);
        }
        $('#panel-modal').modal('show');
    });
</script>
@include('admin.includes.datatablesjs')
@endpush
