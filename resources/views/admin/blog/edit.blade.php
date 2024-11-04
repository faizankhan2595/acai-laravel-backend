@extends('admin.layout')
@section('title')
    Blog | ACAI
@endsection
@push('css')
    <!-- Jquery filer css -->
    <link href="{{ asset('adminassets/plugins/jquery.filer/css/jquery.filer.css') }}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('adminassets/plugins/jodit/jodit.min.css') }}">
    <link href="{{ asset('adminassets/plugins/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css') }}" rel="stylesheet" />
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Edit Blog</h4>
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
                                <form action="{{ route('blog.update',$blog) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="status">Category<span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-control" id="category_id" required>
                                            <option value hidden selected>Select Category</option>
                                            @foreach ($categories as $category)
                                                <option {{ ($blog->category_id == $category->id) ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Blog Title<span class="text-danger">*</span></label>
                                        <input type="text" name="title" placeholder="Enter title" class="form-control" id="name" parsley-trigger="change" required value="{{ $blog->title }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Blog Content</label>
                                        <textarea name="post_body" id="editor" required>{{ $blog->post_body }}</textarea>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label class="control-label">Blog featured Image</label>
                                        @if ($blog->featured_image != '')
                                            (<small class="text-primary"><a target="_blank" href="{{asset('storage/'.$blog->featured_image)}}">View old Image</a></small>)
                                        @endif
                                        <input type="file" id="featured_image" name="featured_image" class="filestyle" data-buttonname="btn-primary">

                                    </div>
                                    <input type="hidden" name="old_image" value="{{ ($blog->featured_image != '') ? $blog->featured_image : '' }}"> --}}
                                    <div class="form-group">
                                        <label class="control-label">Blog Images</label>
                                        <input type="file" name="images[]" id="filer_input2"
                                           multiple="multiple">
                                    </div>

                                    @if ($blog->images->count())
                                        <hr>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                Old Images
                                            </div>
                                            <div class="panel-body">
                                                <div class="jFiler-items jFiler-row">
                                                    <ul class="jFiler-items-list jFiler-items-default">
                                                        @foreach ($blog->images as $i => $image)
                                                            <li class="jFiler-item" data-jfiler-index="{{ $i }}" style="">
                                                                <div class="jFiler-item-container">
                                                                    <div class="jFiler-item-inner">
                                                                        <div class="jFiler-item-icon pull-left">
                                                                            <i class="icon-jfi-file-image jfi-file-ext-png">
                                                                            </i>
                                                                        </div>
                                                                        <div class="jFiler-item-info pull-left">
                                                                            <div class="jFiler-item-title" title="Image {{ $i+1 }}">
                                                                                <a target="_blank" href="{{asset('storage/'.$image->path)}}">Image {{ $i+1 }}</a>
                                                                            </div>
                                                                            <div class="jFiler-item-assets">
                                                                                <ul class="list-inline">
                                                                                    <li>
                                                                                        <a onclick="return confirm('Are you sure you want to delete this?')" href="{{ route('image.destroy',$image) }}" class="icon-jfi-trash jFiler-item-trash-action" type="submit"></a>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="name">Featured Video Link</label>
                                        <input type="text" name="featured_video" placeholder="Video Link" class="form-control" id="featured_video" parsley-trigger="change" value="{{ $blog->featured_video }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="name">Tags/Keywords</label>
                                        <input type="text" name="tags" placeholder="Tags/Keywords" class="form-control" id="tags" parsley-trigger="change" value="{{ $blog->tags }}">
                                    </div>

                                    <label for="name">Is featured post?</label>
                                    <div class="form-group">
                                        <input type="checkbox" id="is_featured" name="is_featured" data-switch="primary" {{ ($blog->is_featured == 1) ? 'checked' : '' }}/>
                                        <label for="is_featured" data-on-label="Yes" data-off-label="No"></label>
                                    </div>

                                    <label for="name">Allow Comments</label>
                                    <div class="form-group">
                                        <input type="checkbox" id="allow_comments" name="allow_comments" data-switch="primary" {{ ($blog->allow_comments == 1) ? 'checked' : '' }}/>
                                        <label for="allow_comments" data-on-label="Yes" data-off-label="No"></label>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Category Status<span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" id="link">
                                            <option value="1" {{ ($blog->status == 1) ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ ($blog->status == 0) ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Update
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
    <!-- Jquery filer js -->
    <script src="{{ asset('adminassets/plugins/jquery.filer/js/jquery.filer.min.js') }}"></script>
    <script src="{{ asset('adminassets/plugins/jodit/jodit.min.js') }}"></script>
        @include('admin.includes.form')
        <script>
            var editor = new Jodit('#editor');
            $('#filer_input2').filer({
                maxSize: 3,
                extensions: ['jpg', 'jpeg', 'png'],
                changeInput: true,
                showThumbs: true,
                addMore: true
            });
        </script>
@endpush
