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
                        <h4 class="page-title">Special Vouchers  </h4>
                        <div class="pull-right">
                            <a href="{{ route('special-voucher.create') }}" class="btn btn-success">Add New</a>
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
                                <th>Owner</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Valid For</th>
                                <th>Created at</th>
                                <th>Expiring On</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($vouchers))
                                @foreach ($vouchers as $voucher)
                                    <tr>
                                        <td>{{ $voucher->merchant->name}}</td>
                                        <td>{{ ($voucher->title != '') ? $voucher->title : 'N/A' }}</td>
                                        <td>{{ $voucher->price.' Points'}}</td>
                                        <td>{{ $voucher->valid_for }}</td>
                                        <td>{{ $voucher->created_at->diffForHumans() }}</td>
                                        <td>{{ $voucher->expiring_on->diffForHumans() }}</td>
                                        <td>
                                            @if ($voucher->status == 1)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                            <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>

                                        <td class="actions">
                                            <a href="{{ route('special-voucher.assignedusers',$voucher) }}" class="on-default text-info btn btn-xs btn-default"><i class="fa fa-user"></i> Add Customers</a>
                                            <a href="{{ route('special-voucher.edit',$voucher) }}" class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>
                                            <form onsubmit="return confirm('Are you sure you want to delete this?')" action="{{ route('special-voucher.destroy',$voucher->id) }}" method="POST" class="inline-el" data-swal="1">
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
