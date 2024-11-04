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
                        <h4 class="page-title">All Email Templates  </h4>
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
                                <th>S. No</th>
                                <th>Email Type</th>
                                <th>Subject</th>
                                <th>Created at</th>
                                <th>Updated On</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($templates))
                                @foreach ($templates as $key => $email)
                                    <tr>
                                        <td>{{ $key+1}}</td>
                                        <td>{{ ($email->email_type != '') ? $email->email_type : 'N/A' }}</td>
                                        <td>{{ ($email->subject != '') ? $email->subject : 'N/A' }}</td>
                                        <td>{{ $email->created_at->format('d M Y') }}</td>
                                        <td>{{ $email->updated_at->format('d M Y') }}</td>

                                        <td class="actions">
                                            <a href="{{ route('email.edit',$email->id) }}" class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>
                                            <form onsubmit="return confirm('Are you sure you want to delete this?')" action="{{ route('email.destroy',$email) }}" method="POST" class="inline-el" data-swal="1">
                                              @method('DELETE')
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
@endpush
