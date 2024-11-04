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
                        <h4 class="page-title">Post Likes </h4>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-sm-12">
                    @include('admin.partials.flash')
                    <div class="card-box table-responsive">
                        <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Post</th>
                                <th>Liked On</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($likes))
                                @foreach ($likes as $like)
                                    <tr>
                                        <td>{{ ($like->name != '') ? $like->name : 'N/A' }}</td>
                                        <td>
                                            {!! str_limit($blog->title,25,'...<a href="#" data-toggle="popover" data-trigger="hover" title="Blog Title" data-content="'.$blog->title.'"><i class="fa fa-exclamation-circle"></i></a>') !!}
                                        </td>
                                        <td>{{ $like->created_at->diffForHumans() }}</td>

                                        <td class="actions">
                                            <form onsubmit="return confirm('Are you sure you want to delete this?')" action="{{ route('blog.deletelike',['blog'=>$blog,'user_id'=>$like->id]) }}" method="POST" class="inline-el" data-swal="1">
                                              @method('POST')
                                              @csrf
                                              <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('admin.includes.datatablesjs')
    <script src="{{ asset('adminassets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
<script>
    $(document).on('click', '.action_form', function (e) {
      e.preventDefault();
      var form = $(this).parent('form');
      swal({
          title: "Are you sure?",
          text: "You will not be able to recover this imaginary file!",
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
