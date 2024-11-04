@extends('admin.layout')
@section('title')
    Setting | ACAI
@endsection
@push('css')
    <link href="{{ asset('adminassets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Setting</h4>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->
        <div class="row">
            <div class="col-xs-12">
                @include('admin.partials.flash')
                <div class="card-box">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col-md-10 col-md-offset-1">
                            <div class="p-20">
                                <form action="{{ route('setting.save') }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    <div class="form-group">
                                        <label for="project_title">Project Title</label>
                                        <input type="text" name="project_title" placeholder="Enter Title" class="form-control" id="name" parsley-trigger="change" value="{{ $data['project_title'] ?? '' }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_email">Contact Email</label>
                                        <input type="text" name="contact_email" placeholder="Contact Email" class="form-control" id="contact_email" parsley-trigger="change" value="{{ $data['contact_email'] ?? '' }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_site">Contact Website</label>
                                        <input type="text" name="contact_site" placeholder="Contact Website" class="form-control" id="contact_site" parsley-trigger="change" value="{{ $data['contact_site'] ?? '' }}">
                                    </div>

                                    <label for="contact_site">Birthday Reward Amount</label>
                                    <div class="input-group m-b-10">
                                        <input type="number" name="birthday_reward" placeholder="Birthday Reward Amount" class="form-control" id="birthday_reward" value="{{ $data['birthday_reward'] ?? '' }}">
                                        <span class="input-group-addon">POINTS</span>
                                    </div>

                                    <label for="contact_site">Add Points Default Expiry(In days)</label>
                                    <div class="input-group m-b-10">
                                        <input type="number" name="default_exipry" placeholder="Expire In" class="form-control" id="default_exipry" value="{{ $data['default_exipry'] ?? '' }}">
                                        <span class="input-group-addon">DAYS</span>
                                    </div>

                                    <label for="gold_membership_points">Points Required to see Gold Membership Activator</label>
                                    <div class="input-group m-b-10">
                                        <input type="number" name="gold_membership_points" placeholder="Points Required to see Gold Membership Activator" class="form-control" id="gold_membership_points" value="{{ $data['gold_membership_points'] ?? '' }}">
                                        <span class="input-group-addon">POINTS</span>
                                    </div>

                                    <label for="gold_membership_charge">Points to deduct when Gold Membership activated</label>
                                    <div class="input-group m-b-10">
                                        <input type="number" name="gold_membership_charge" placeholder="Points to charge when Gold Membership activated" class="form-control" id="gold_membership_charge" value="{{ $data['gold_membership_charge'] ?? '' }}">
                                        <span class="input-group-addon">POINTS</span>
                                    </div>
                                    <label for="gold_membership_renew_charge">Points Required to renew Gold Membership</label>
                                    <div class="input-group m-b-10">
                                        <input type="number" name="gold_membership_renew_charge" placeholder="Points Required to renew Gold Membership" class="form-control" id="gold_membership_renew_charge" value="{{ $data['gold_membership_renew_charge'] ?? '' }}">
                                        <span class="input-group-addon">POINTS</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="gold_membership_tc">T&C for Gold Membership activation</label>
                                        <textarea name="gold_membership_tc" class="editor">{{ $data['gold_membership_tc'] ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Acai Menu</label>
                                        @isset ($data['acai_menu'])
                                           (<small class="text-primary"><a target="_blank" href="{{asset('storage/'.$data['acai_menu'])}}">View old</a></small>)
                                        @endisset
                                        <input type="file" id="acai_menu" name="acai_menu" class="filestyle" data-buttonname="btn-primary">
                                    </div>
                                    <input type="hidden" name="acai_menu" value="{{ $data['acai_menu'] ?? '' }}">
                                    <div class="form-group">
                                        <label class="control-label">Acai Reward Guide Pdf</label>
                                        @isset ($data['acai_reward_guide_pdf'])
                                           (<small class="text-primary"><a target="_blank" href="{{asset('storage/'.$data['acai_reward_guide_pdf'])}}">View old</a></small>)
                                        @endisset
                                        <input type="file" id="acai_reward_guide_pdf" name="acai_reward_guide_pdf" class="filestyle" data-buttonname="btn-primary">
                                    </div>
                                    <input type="hidden" name="acai_reward_guide_pdf" value="{{ $data['acai_reward_guide_pdf'] ?? '' }}">

                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Manage Conversion</h3>
                                        </div>
                                        <div class="panel-body">
                                            <label for="contact_site"><strong>S$1</strong> value in Points for Gold Member</label>
                                            <div class="input-group m-b-10">
                                                <input type="number" step=".01" name="gold_member_conversion" placeholder="Gold Member Conversion" class="form-control" id="gold_member_conversion" value="{{ $data['gold_member_conversion'] ?? '' }}">
                                                <span class="input-group-addon">POINTS</span>
                                            </div>
                                            <label for="contact_site"><strong>S$1</strong> value in Points for Purple Member</label>
                                            <div class="input-group m-b-10">
                                                <input type="number" name="purple_member_conversion" placeholder="Purple Member Conversion" class="form-control" id="purple_member_conversion" value="{{ $data['purple_member_conversion'] ?? '' }}">
                                                <span class="input-group-addon">POINTS</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Manage App HomePage</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="control-label">Sales Home Banner</label>
                                                @isset ($data['sales_home_banner'])
                                                   (<small class="text-primary"><a target="_blank" href="{{asset('storage/'.$data['sales_home_banner'])}}">View old</a></small>)
                                                @endisset
                                                <input type="file" id="sales_home_banner" name="sales_home_banner" class="filestyle" data-buttonname="btn-primary">
                                            </div>
                                            <input type="hidden" name="sales_home_banner" value="{{ $data['sales_home_banner'] ?? '' }}">
                                            <div class="form-group">
                                                <label for="sales_home_heading">Sales Home Heading</label>
                                                <input type="text" name="sales_home_heading" placeholder="Sales Home Heading" class="form-control" id="sales_home_heading" parsley-trigger="change" value="{{ $data['sales_home_heading'] ?? '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="sales_home_subheading">Sales Home SubHeading</label>
                                                <input type="text" name="sales_home_subheading" placeholder="Sales Home SubHeading" class="form-control" id="sales_home_subheading" parsley-trigger="change" value="{{ $data['sales_home_subheading'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Submit
                                        </button>
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
        jQuery('#expiring_on').datepicker({
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
