@extends('admin.layout')
@push('css')
    @include('admin.includes.datatablescss')
    <link href="{{ asset('adminassets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
@endpush
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-title-box">
                        <h4 class="page-title">All Comments  </h4>
                        <div class="pull-right">
                            <a class="btn btn-success" href="{{ route('comment.export') }}"><span><i class="mdi mdi-export">
                                </i> Export to Excel</span></a>
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
                        <table id="comment_table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Post</th>
                                <th>Comment</th>
                                <th>Commented On</th>
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
function truncate(str, no_words) {
    return str.split(" ").splice(0,no_words).join(" ");
}
function WordCount(str) {
  return str.split(" ").length;
}
   $(document).ready( function () {
        $('#comment_table').DataTable({
            processing: true,
            // oLanguage: {
            //     sProcessing: "<img src='{{ asset('adminassets/assets/images/load.gif') }}'>"
            // },
            serverSide: true,
            ajax: {
            url: "{{ (isset($blog)) ? route('comment.index',$blog) : route('comment.index') }}",
            },
            columns: [
            {
            data : 'user',
                render : function(data, type, row, meta) {
                    return  data.name;
                }
		    },
            {
            data : 'blog',
                render : function(data, type, row, meta) {
                    if(WordCount(data.title) > 10){
                        return truncate(data.title,10)+"...";
                    }
                    else{
                        return data.title;
                    }
                }
		    },
            {
            data : 'comment_body',
                render : function(data, type, row, meta) {
                    if(WordCount(data) > 10){
                        return  truncate(data,10)+"...";
                    }
                    else{
                        return data;
                    }
                }
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
    <script src="{{ asset('adminassets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
<script>
    $(document).on('click', '.action_form', function (e) {
      e.preventDefault();
      var form = $(this).parent('form');
      swal({
          title: "Are you sure?",
          text: "You will not be able to recover this record!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: 'btn-warning',
          confirmButtonText: "Yes",
          closeOnConfirm: false
      }, function () {
          $(form).submit();
      });
    });
</script>
@endpush
