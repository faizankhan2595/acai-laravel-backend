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
                        <h4 class="page-title">Deleted Merchants  </h4>
                        <div class="pull-right">
                            <a href="{{ route('merchant.index') }}" class="btn btn-info"><i class="mdi mdi-format-list-numbers"></i> All Merchants</a>
                            <a href="{{ route('merchant.create') }}" class="btn btn-success">Add New</a>
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
                        <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>DOB</th>
                                <th>Created at</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($trashed))
                                @foreach ($trashed as $merchant)
                                    <tr>
                                        <td>{{ ($merchant->name != '') ? $merchant->name : 'N/A' }}</td>
                                        <td>{{$merchant->email}}</td>
                                        <td>{{ ($merchant->dob) ? $merchant->dob->format('d-M-Y') : 'N/A'}}</td>
                                        <td>{{ $merchant->created_at->diffForHumans() }}</td>
                                        <td>
                                            @if ($merchant->account_status == 1)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                            <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="actions">
                                                <a href="{{ route('merchant.restore',$merchant->id) }}" onclick="return confirm('Are you sure you want to restore this?')" class="on-default text-primary btn btn-xs btn-default"><i class="fa fa-reply"></i> Restore</a>
                                              <a href="{{ route('merchant.forcedelete',$merchant->id) }}" onclick="return confirm('Are you sure you want to delete this?')" class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Destroy</a>
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
