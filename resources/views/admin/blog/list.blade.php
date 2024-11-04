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
                        <h4 class="page-title">All Blogs  </h4>
                        <div class="pull-right">
                            <a href="{{ route('blog.trash') }}" class="btn btn-danger"><i class="mdi mdi-delete"></i> Trash</a>
                            <a href="{{ route('blog.create') }}" class="btn btn-success">Add New</a>
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
                        <table id="blog_table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Blog title</th>
                                <th>Blog Category</th>
                                <th>Blog Views</th>
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
<!-- <style>div.dataTables_wrapper div.dataTables_processing { top : 25%;background: none; }</style> -->
<script>
function truncate(str, no_words) {
    return str.split(" ").splice(0,no_words).join(" ");
}
function WordCount(str) {
  return str.split(" ").length;
}
   $(document).ready( function () {
        $('#blog_table').DataTable({
            processing: true,
            "aaSorting" : [],
            // oLanguage: {
            //     sProcessing: "<img src='{{ asset('adminassets/assets/images/load.gif') }}'>"
            // },
            serverSide: true,
            ajax: {
            url: "{{ route('blog.index') }}",
            },
            columns: [
            {
            data : 'title',
                render : function(data, type, row, meta) {
                    if(WordCount(data) > 10){
                        return  truncate(data,10)+"...";
                    }
                    else{
                        return  data;
                    }
                }
		    },
            {
            data : 'category',
                render : function(data, type, row, meta) {
                    return  data.category_name;
                }
		    },
            {
                data: 'blog_views',
                name: 'blog_views',
                orderable: false
            },
            {
                name: 'created_at.timestamp',
                data: {
                    _: 'created_at.display',
                    sort: 'created_at.timestamp'
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
