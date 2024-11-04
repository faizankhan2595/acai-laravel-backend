@extends('admin.layout')
@section('title')
    User | ACAI
@endsection
@push('css')
    <link href="{{ asset('adminassets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- Custom box css -->
    <link href="{{ asset('adminassets/plugins/custombox/css/custombox.min.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Edit User</h4>
                    <div class="pull-right">
                        <a href="#change-password-modal" class="btn btn-warning waves-effect waves-light m-r-5 m-b-10" data-animation="fadein" data-plugin="custommodal" data-overlaySpeed="200" data-overlayColor="#36404a" ><i class=" mdi mdi-lock"></i> Change Password</a>
                    </div>
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
                                <form action="{{ route('user.update',$user) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="name">Full Name<span class="text-danger">*</span></label>
                                        <input type="text" name="name" placeholder="Enter name" class="form-control" id="name" parsley-trigger="change" required value="{{ $user->name }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email<span class="text-danger">*</span></label>
                                        <input type="text" name="email" placeholder="Enter email" class="form-control" id="email" parsley-trigger="change" required value="{{ $user->email }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="mobile_number">Mobile Number<span class="text-danger">*</span></label>
                                        <input type="text" name="mobile_number" placeholder="Enter Mobile Number" class="form-control" id="mobile_number" parsley-trigger="change" required value="{{ $user->mobile_number }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="dob">Birthday</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="dob" placeholder="mm/dd/yyyy" id="dob" value="{{ (!is_null($user->dob)) ? $user->dob->format('d-M-Y') : '' }}">
                                            <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="gender">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control" id="link">
                                            <option value="1" {{ ($user->gender == 1) ? 'selected' : '' }}>Male</option>
                                            <option value="2" {{ ($user->gender == 2) ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Avatar</label>
                                        @if ($user->avatar != '')
                                            (<small class="text-primary"><a target="_blank" href="{{asset('storage/'.$user->avatar)}}">View old Image</a></small>)
                                        @endif
                                        <input type="file" id="avatar" name="avatar" class="filestyle" data-buttonname="btn-primary">
                                    </div>
                                    <input type="hidden" name="old_avatar" value="{{ ($user->avatar != '') ? $user->avatar : '' }}">
                                    <input type="hidden" name="role" value="user">
                                    <div class="form-group">
                                        <label for="membership_type">Membership <span class="text-danger">*</span></label>
                                        <select name="membership_type" class="form-control" id="membership_type">
                                            <option value="1" {{ ($user->membership_type == 1) ? 'selected' : '' }}>Purple Member</option>
                                            <option value="2" {{ ($user->membership_type == 2) ? 'selected' : '' }}>Gold Member</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="sort_order" value="{{ $user->sort_order }}">
                                    <div class="form-group">
                                        <label for="account_status">Account Status<span class="text-danger">*</span></label>
                                        <select name="account_status" class="form-control" id="account_status">
                                            <option value="1" {{ ($user->account_status == 1) ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ ($user->account_status == 0) ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Update
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

    {{-- Chage Password --}}
    <div id="change-password-modal" class="modal-demo">
        <button type="button" class="close" onclick="Custombox.close();">
            <span>&times;</span><span class="sr-only">Close</span>
        </button>
        <h4 class="custom-modal-title">Change Password</h4>
        <form action="{{ route('user.change',$user) }}" method="post" data-parsley-validate novalidate id="change-password">
            @csrf
            @method('PUT')
            <div class="custom-modal-text text-left">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="Password" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Password Confirmation" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" onclick="Custombox.close();">Close</button>
                <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    @include('admin.includes.form')
    <script src="{{ asset('adminassets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('adminassets/plugins/moment/moment.js') }}"></script>
    <!-- Modal-Effect -->
    <script src="{{ asset('adminassets/plugins/custombox/js/custombox.min.js') }}"></script>
    <script src="{{ asset('adminassets/plugins/custombox/js/legacy.min.js') }}"></script>
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

        //change password
        $("#change-password").on('submit',function(e) {
            e.preventDefault();
            $.ajax({
                url : '{{ route('user.change',$user) }}',
                type: 'POST',
                data: $("#change-password").serialize(),
                beforeSend : function() {
                    $("#change-password").find('button').attr('disabled',true);
                },
                complete : function() {
                    $("#change-password").find('button').attr('disabled',false);
                },
                success : function(data) {
                    toastr["success"](data.message);
                    Custombox.close();
                },
                error : function(data) {
                    toastr["error"](data.responseJSON.errors[Object.keys(data.responseJSON.errors)]);
                }
            })
        })
    </script>
@endpush
