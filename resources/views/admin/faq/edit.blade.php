@extends('admin.layout')
@section('title')
    FAQ | ACAI
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Edit FAQ</h4>
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
                                <form action="{{ route('faq.update',$faq) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="status">Faq Category<span class="text-danger">*</span></label>
                                        <select name="faq_category_id" class="form-control" id="faq_category_id">
                                            @foreach ($faqcategories as $category)
                                                <option {{ ($faq->faq_category_id == $category->id) ? "selected" : "" }} value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="question">Question<span class="text-danger">*</span></label>
                                        <input type="text" name="question" placeholder="Enter Question" class="form-control" id="question" parsley-trigger="change" required value="{{ $faq->question }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="answer">Answer<span class="text-danger">*</span></label>
                                        <textarea name="answer" placeholder="Answer" class="form-control" id="answer" parsley-trigger="change" required>{{ $faq->answer }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Faq Status<span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" id="link">
                                            <option value="1" {{ ($faq->status == 1) ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ ($faq->status == 0) ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group text-right m-b-0">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Update
                                        </button>
                                        <a href="{{ route('faq.index') }}" type="reset" class="btn btn-default waves-effect m-l-5">
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
