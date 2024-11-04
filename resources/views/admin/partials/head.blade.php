    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="{{config('admin.project_description', '')}}">

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('adminassets/assets/images/favicon.ico') }}">
        <!-- App title -->
        <title>@yield('title', config('admin.project_name', ''))</title>

        <!--Morris Chart CSS -->
        <link rel="stylesheet" href="{{ asset('adminassets/plugins/morris/morris.css') }}">

        <!-- App css -->
        <link href="{{ asset('adminassets/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/core.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/components.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/pages.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/menu.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/responsive.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/elements.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{{ asset('adminassets/plugins/switchery/switchery.min.css') }}">

        <!-- Notification css (Toastr) -->
        <link href="{{ asset('adminassets/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js') }} IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js') }} doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js') }}"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js') }}/1.3.0/respond.min.js') }}"></script>
        <![endif]-->

        <script src="{{ asset('adminassets/assets/js/modernizr.min.js') }}"></script>
@stack('css')
    </head>
