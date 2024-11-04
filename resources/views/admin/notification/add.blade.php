@extends('admin.layout')
@section('title')
    Notification | ACAI
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Send Notification</h4>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->
        <div class="row">
            <div class="col-xs-12">
                <div class="card-box">
                    <div class="row">
                        @include('admin.partials.flash')
                        <div class="col-sm-12 col-xs-12 col-md-10 col-md-offset-1">
                            <div class="p-20">
                                <form action="{{ route('notification.store') }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Notification Title<span class="text-danger">*</span></label>
                                        <input type="text" name="notification_title" placeholder="Enter name" class="form-control" id="name" parsley-trigger="change" required value="{{ old('notification_title') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Notification Description<span class="text-danger">*</span></label>
                                        <textarea name="notification_desc" placeholder="Description" class="form-control" id="notification_desc" parsley-trigger="change" required>{{ old('notification_desc') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Image</label>
                                        <input type="file" id="image" name="image" class="filestyle" data-buttonname="btn-primary">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Send to</label>
                                        <div class="form-inline">
                                            <div class="form-group m-l-10">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="all_customers" name="purple_customers" type="checkbox" value="1">
                                                    <label for="all_customers">
                                                        Purple Customers
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group m-l-10">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="all_customers" name="gold_customers" type="checkbox" value="1">
                                                    <label for="all_customers">
                                                        Gold Customers
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group m-l-10">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="all_merchnats" name="all_merchnats" type="checkbox" value="2">
                                                    <label for="all_merchnats">
                                                        All Merchnats
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group m-l-10">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="all_sales_persons" name="all_sales_persons" type="checkbox" value="3">
                                                    <label for="all_sales_persons">
                                                        All Sales Persons
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Submit
                                        </button>
                                        {{-- <a href="{{ route('notification.index') }}" type="reset" class="btn btn-default waves-effect m-l-5">
                                            Cancel
                                        </a> --}}
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>
                    <!-- end row -->
                </div> <!-- end ard-box -->
            </div><!-- end col-->
        </div>
    </div> <!-- container -->
@endsection
@push('scripts')
    @include('admin.includes.form')
@endpush
