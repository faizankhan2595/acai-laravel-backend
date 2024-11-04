@extends('admin.layout')
@section('title')
    Email Template | ACAI
@endsection
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('adminassets/plugins/jodit/jodit.min.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Edit Email Template</h4>
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
                                <form action="{{ route('email.update',$email) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="name">Email Type</label>
                                        <input type="text" disabled class="form-control" value="{{ $email->email_type }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Subject<span class="text-danger">*</span></label>
                                        <input type="text" name="subject" placeholder="Enter subject" class="form-control" id="name" parsley-trigger="change" required value="{{ $email->subject }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="content">Email Content <small class="text-danger">Please do not change variables (<strong>$anyname</strong>)</small></label>
                                        <textarea name="content" class="editor" id="editor" required>{{ $email->content }}</textarea>
                                    </div>

                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Update
                                        </button>
                                        <a href="{{ route('email.index') }}" type="reset" class="btn btn-default waves-effect m-l-5">
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
    <script src="{{ asset('adminassets/plugins/jodit/jodit.min.js') }}"></script>
    <script>
        var editor = new Jodit('#editor');
    </script>
@endpush
