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
                        <h4 class="page-title">Faqs  </h4>
                        <div class="pull-right">
                            <a href="{{ route('faq.create') }}" class="btn btn-success">Add New</a>
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
                        <table id="faq_table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Faq Category</th>
                                <th>Question</th>
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
                        <h3 class="panel-title" id="modal_question"></h3>
                    </div>
                    <div class="panel-body">
                        <p id="modal_answer"></p>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@push('scripts')
<script>
   $(document).ready( function () {
        $('#faq_table').DataTable({
            processing: true,
            // oLanguage: {
            //     sProcessing: "<img src='{{ asset('adminassets/assets/images/load.gif') }}'>"
            // },
            serverSide: true,
            ajax: {
            url: "{{ route('faq.index') }}",
            },
            columns: [
            {
                data : 'faqcategory',
                render : function(data, type, row, meta) {
                    if(data.category_name){
                        return data.category_name;
                    }
                    else{
                        return 'N/A';
                    }
                }
		    },
            {
                data : 'question',
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
            data : 'status',
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
    });
  </script>
    @include('admin.includes.datatablesjs')
    <script>
        $(document).on('click','[data-question]',function() {
            $("#modal_question").text($(this).data('question'));
            $("#modal_answer").text($(this).data('answer'));
            $('#panel-modal').modal('show');
        })
    </script>
@endpush
