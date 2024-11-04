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
                        <h4 class="page-title">All Categories  </h4>
                        <div class="pull-right">
                            <a href="{{ route('category.create') }}" class="btn btn-success">Add New</a>
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
                        <table id="blogcat_table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Category Name</th>
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
@endsection
@push('scripts')
<!-- <style>div.dataTables_wrapper div.dataTables_processing { top : 25%;background: none; }</style> -->
<script>
   $(document).ready( function () {
        $('#blogcat_table').DataTable({
            processing: true,
            // oLanguage: {
            //     sProcessing: "<img src='{{ asset('adminassets/assets/images/load.gif') }}'>"
            // },
            serverSide: true,
            ajax: {
            url: "{{ route('category.index') }}",
            },
            columns: [
            {
                data: 'category_name',
                name: 'category_name'
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
@endpush
