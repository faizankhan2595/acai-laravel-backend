@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Privacy Policy</div>
                <div class="card-body">
                    {!! $data->content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
