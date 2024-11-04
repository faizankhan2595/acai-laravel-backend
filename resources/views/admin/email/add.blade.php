@extends('admin.layout')
@section('title')
    Blog | ACAI
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Add Blog</h4>
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
                                <form action="{{ route('blog.store') }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    <div class="form-group">
                                        <label for="status">Category<span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-control" id="category_id" required>
                                            <option value hidden selected>Select Category</option>
                                            @foreach ($categories as $category)
                                                <option {{ (old('category_id') == $category->id) ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Blog Title<span class="text-danger">*</span></label>
                                        <input type="text" name="title" placeholder="Enter title" class="form-control" id="name" parsley-trigger="change" required value="{{ old('title') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Blog Content</label>
                                        <textarea name="post_body" class="editor" required>{{ old('post_body') }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Blog featured Image</label>
                                        <input type="file" id="featured_image" name="featured_image" class="filestyle" data-buttonname="btn-primary">
                                    </div>

                                    <div class="form-group">
                                        <label for="name">Featured Video Link</label>
                                        <input type="text" name="featured_video" placeholder="Video Link" class="form-control" id="featured_video" parsley-trigger="change" value="{{ old('featured_video') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="name">Tags/Keywords</label>
                                        <input type="text" name="tags" placeholder="Tags/Keywords" class="form-control" id="tags" parsley-trigger="change" value="{{ old('tags') }}">
                                    </div>

                                    <label for="name">Is featured post?</label>
                                    <div class="form-group">
                                        <input type="checkbox" id="is_featured" name="is_featured" data-switch="primary"/>
                                        <label for="is_featured" data-on-label="Yes" data-off-label="No"></label>
                                    </div>

                                    <label>Allow Comments</label>
                                    <div class="form-group">
                                        <input type="checkbox" id="allow_comments" name="allow_comments" data-switch="primary" checked />
                                        <label for="allow_comments" data-on-label="Yes" data-off-label="No"></label>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Category Status<span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" id="link">
                                            <option value="1" {{ (old('status') == 1) ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ (old('status') == 0) ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Submit
                                        </button>
                                        <a href="{{ route('blog.index') }}" type="reset" class="btn btn-default waves-effect m-l-5">
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
