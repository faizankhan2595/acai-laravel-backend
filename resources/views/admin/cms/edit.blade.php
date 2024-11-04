@extends('admin.layout')
@section('title')
    Page | ACAI
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Edit Page</h4>
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
                                <form action="{{ route('page.update',$page) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="title">Page Title<span class="text-danger">*</span></label>
                                        <input type="text" name="title" placeholder="Enter title" class="form-control" id="title" parsley-trigger="change" required value="{{ $page->title }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="content">Page Content</label>
                                        <textarea name="content" class="editor" required>{{ $page->content }}</textarea>
                                    </div>


                                    <div class="form-group">
                                        <label for="status">Status<span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" id="link">
                                            <option value="1" {{ ($page->status == 1) ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ ($page->status == 0) ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Update
                                        </button>
                                        <a href="{{ route('page.index') }}" type="reset" class="btn btn-default waves-effect m-l-5">
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
@endpush
