@extends('admin.layout')
@section('title')
    Locations | ACAI
@endsection
@push('css')
    <link href="{{ asset('adminassets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Locations</h4>
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
                                <form action="{{ route('locations.save') }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    <div class="panel-body">
                                        <div class="input_fields_wrap">
                                            <button class="btn btn-success btn-sm add_field_button" style="margin-bottom: 5px;">Add More</button>
                                            @if ($locations)
                                                @foreach ($locations as $location)
                                                    <div class="row colordivappend" id="timediv" style="margin-bottom: 5px;">
                                                        <div class="col-md-3" >
                                                            <textarea name="location_title[]" cols="5" placeholder="Location Title" class="form-control">{{ $location->location_title ?? '' }}</textarea>
                                                        </div>
                                                        <div class="col-md-4" >
                                                            <textarea name="location_address[]" cols="5" placeholder="Location Address" class="form-control">{{ $location->location_address ?? '' }}</textarea>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <textarea name="location_link[]" cols="5" placeholder="Location Link" class="form-control">{{ $location->location_link ?? '' }}</textarea>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger remove_field" title="Delete"><i class="fa fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Save
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

    <script>
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID

        var x = $(".colordivappend").length; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                x++; //text box increment
            var divdata = `<div class="row colordivappend" id="timediv" style="margin-bottom: 5px;">
                        <div class="col-md-3" >
                            <textarea name="location_title[]" cols="5" placeholder="Location Title" class="form-control"></textarea>
                        </div>
                        <div class="col-md-4">
                            <textarea name="location_address[]" cols="5" placeholder="Location Address" class="form-control"></textarea>
                        </div>
                        <div class="col-md-3">
                            <textarea name="location_link[]" cols="5" placeholder="Location Link" class="form-control"></textarea>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove_field" title="Delete"><i class="fa fa-trash"></i></button>
                        </div>
                        </div>`;
                        $(wrapper).append(divdata);

            }
        });

        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            if ($(".remove_field").length == 1) {
                alert("Sorry atleat 1 item is required");
                return false;
            }
            e.preventDefault(); $(this).closest('div.colordivappend').remove(); x--;
        });
    </script>
@endpush
