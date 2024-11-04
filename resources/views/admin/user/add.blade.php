@extends('admin.layout')
@section('title')
    User | ACAI
@endsection
@push('css')
    <link href="{{ asset('adminassets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Add User</h4>
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
                                <form action="{{ route('user.store') }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Full Name<span class="text-danger">*</span></label>
                                        <input type="text" name="name" placeholder="Enter name" class="form-control" id="name" parsley-trigger="change" required value="{{ old('name') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email<span class="text-danger">*</span></label>
                                        <input type="text" name="email" placeholder="Enter email" class="form-control" id="email" parsley-trigger="change" required value="{{ old('email') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="mobile_number">Mobile Number<span class="text-danger">*</span></label>
                                        <input type="text" name="mobile_number" placeholder="Enter Mobile Number" class="form-control" id="mobile_number" parsley-trigger="change" required value="{{ old('mobile_number') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="dob">Birthday</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="dob" placeholder="mm/dd/yyyy" id="dob" value="{{ old('dob') }}">
                                            <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="gender">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control" id="link">
                                            <option value="1" {{ (old('gender') == 1) ? 'selected' : '' }}>Male</option>
                                            <option value="2" {{ (old('gender') == 2) ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Avatar</label>
                                        <input type="file" id="avatar" name="avatar" class="filestyle" data-buttonname="btn-primary">
                                    </div>

                                    <div class="form-group">
                                        <label for="password">Password<span class="text-danger">*</span></label>
                                        <input type="text" name="password" placeholder="Enter Password" class="form-control" id="password" parsley-trigger="change" required value="{{ old('password') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm Password_confirmation<span class="text-danger">*</span></label>
                                        <input type="text" name="password_confirmation" placeholder="Enter Password" class="form-control" id="password_confirmation" parsley-trigger="change" required value="{{ old('password_confirmation') }}">
                                    </div>
                                    <input type="hidden" name="role" value="user">
                                    <input type="hidden" name="sort_order" value="">
                                    <div class="form-group">
                                        <label for="membership_type">Membership <span class="text-danger">*</span></label>
                                        <select name="membership_type" class="form-control" id="membership_type">
                                            <option value="1" {{ (old('membership_type') == 1) ? 'selected' : '' }}>Purple Member</option>
                                            <option value="2" {{ (old('membership_type') == 2) ? 'selected' : '' }}>Gold Member</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="account_status">Account Status<span class="text-danger">*</span></label>
                                        <select name="account_status" class="form-control" id="link">
                                            <option value="1" {{ (old('account_status') == 1) ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ (old('account_status') == 0) ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Submit
                                        </button>
                                        <a href="{{ route('user.index') }}" type="reset" class="btn btn-default waves-effect m-l-5">
                                            Cancel
                                        </a>
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
    <script src="{{ asset('adminassets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('adminassets/plugins/moment/moment.js') }}"></script>
    <script type="text/javascript">
        jQuery('#dob').datepicker({
               setDate: new Date(),
               autoclose: true,
               todayHighlight: true,
               format: {
                   toDisplay: function (date, format, language) {
                       var d = new Date(date);
                       return moment(d).format('DD-MMM-YYYY');
                   },
                   toValue: function (date, format, language) {
                       var d = new Date(date);
                       return moment(d).format('YYYY-MM-DD HH:mm:ss');
                   }
               }
           });
    </script>
@endpush
