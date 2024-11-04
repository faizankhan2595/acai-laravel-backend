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
                        <h4 class="page-title">All Pages  </h4>
                        <div class="pull-right">
                            {{-- <a href="{{ route('page.trash') }}" class="btn btn-danger"><i class="mdi mdi-delete"></i> Trash</a> --}}
                            {{-- <a href="{{ route('page.create') }}" class="btn btn-success">Add New</a> --}}
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
                        <table id="cms_table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Page title</th>
                                <th>Created at</th>
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
<script>
   $(document).ready( function () {
        $('#cms_table').DataTable({
            processing: true,
            // oLanguage: {
            //     sProcessing: "<img src='{{ asset('adminassets/assets/images/load.gif') }}'>"
            // },
            serverSide: true,
            ajax: {
            url: "{{ route('page.index') }}",
            },
            columns: [
            { 
            data : 'title',
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
            data : 'created_at',
                render : function(data, type, row, meta) {
                    if(data){
                        const date = new Date(data)
                        const dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: 'short', day: '2-digit' }) 
                        const [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(date ) 
                        data = `${day}-${month}-${year }`;
                    }
                    else{
                        data = 'N/A';
                    }
                    return data;
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
@endpush
