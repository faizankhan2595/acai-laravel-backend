@extends('admin.layout')
@section('title')
    Voucher | ACAI
@endsection
@push('css')
    <link href="{{ asset('adminassets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Add Voucher</h4>
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
                                <form action="{{ route('voucher.store') }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    <div class="form-group">
                                        <label for="status">Merchant<span class="text-danger">*</span></label>
                                        <select name="user_id" class="form-control" id="user_id" required>
                                            <option value hidden selected>Select Merchant</option>
                                            {{-- <option value="0">Project Acai</option> --}}
                                            @foreach ($merchants as $merchant)
                                                <option {{ (old('merchant_id') == $merchant->id) ? 'selected' : '' }} value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Voucher Discount type<span class="text-danger">*</span></label>
                                        <select name="voucher_type" class="form-control" id="voucher_type" required>
                                            <option value="1" {{ (old('voucher_type') == 1) ? 'selected' : '' }}>Amount Discount</option>
                                            <option value="2" {{ (old('voucher_type') == 2) ? 'selected' : '' }}>Percent Discount</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Valid For<span class="text-danger">*</span></label>
                                        <select name="valid_for" class="form-control" id="valid_for" required>
                                            @foreach ($validFor as $key => $valid_for)
                                                <option value="{{ $key }}" {{ (old('valid_for') == $key) ? 'selected' : '' }}>{{ $valid_for }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="name">Title<span class="text-danger">*</span></label>
                                        <input type="text" name="title" placeholder="Enter Title" class="form-control" id="name" parsley-trigger="change" required value="{{ old('title') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="discount_title">Discount Title</label>
                                        <input type="text" name="discount_title" placeholder="Discount Title" class="form-control" id="discount_title" value="{{ old('discount_title') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="discount_subtitle">Discount Sub-Title</label>
                                        <input type="text" name="discount_subtitle" placeholder="Discount Sub-Title" class="form-control" id="discount_subtitle" value="{{ old('discount_subtitle') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="name">Price (Points required)<span class="text-danger">*</span></label>
                                        <input type="number" name="price" placeholder="Price" class="form-control" id="price" parsley-trigger="change" value="{{ old('price') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="expiring_on">Expiry date<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="expiring_on" placeholder="Expiry date" id="expiring_on" value="{{ old('expiring_on') }}" readonly>
                                            <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Image</label>
                                        <input type="file" id="image" name="image" class="filestyle" data-buttonname="btn-primary">
                                    </div>

                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control" name="notes" placeholder="Notes" id="notes">{{ old('notes') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <div class="panel panel-default">
                                            <div class="panel-heading row">
                                                <div class="col-md-6"><h3 class="panel-title">Terms & Conditions</h3></div>
                                                <div class="col-md-6">
                                                    <button class="btn btn-success btn-sm pull-right add_field_button" style="margin-bottom: 5px;">Add More</button>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="input_fields_wrap">
                                                    @if (old('terms'))
                                                        @foreach (old('terms') as $term)
                                                            <div class="row colordivappend" id="timediv" style="margin-bottom: 5px;">
                                                                <div class="col-md-10">
                                                                    <input class="form-control" name="terms[]" placeholder="Terms & conditions" value="{{ $term }}" />
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button class="btn btn-danger remove_field" title="Delete" type="button">
                                                                        <i class="fa fa-trash">
                                                                        </i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <label for="name">Mark as Featured</label>
                                    <div class="form-group">
                                        <input type="checkbox" id="is_featured" name="is_featured" data-switch="primary"/>
                                        <label for="is_featured" data-on-label="Yes" data-off-label="No"></label>
                                    </div> --}}
                                    <input type="hidden" value="0" />
                                    <div class="form-group">
                                        <label for="status">Status<span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" id="link">
                                            <option value="1" {{ (old('status') == 1) ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ (old('status') == 0) ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Submit
                                        </button>
                                        <a href="{{ route('voucher.index') }}" type="reset" class="btn btn-default waves-effect m-l-5">
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

        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID

        var x = $(".colordivappend").length; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                x++; //text box increment
            var divdata = `<div class="row colordivappend" id="timediv" style="margin-bottom: 5px;">
                        <div class="col-md-10" >
                            <input name="terms[]" placeholder="Terms & conditions" class="form-control"/>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove_field" title="Delete"><i class="fa fa-trash"></i></button>
                        </div>
                        </div>`;
                        $(wrapper).append(divdata);
            }
        });

        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            // if ($(".remove_field").length == 1) {
            //     alert("Sorry atleat 1 item is required");
            //     return false;
            // }
            e.preventDefault(); $(this).closest('div.colordivappend').remove(); x--;
        });
    </script>
@endpush
