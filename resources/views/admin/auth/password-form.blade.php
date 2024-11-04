<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('adminassets/assets/images/favicon.ico') }}">
        <!-- App title -->
        <title><?php echo env('PROJECT_NAME') ?></title>

        <!-- App css -->
        <link href="{{ asset('adminassets/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/core.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/components.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/pages.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/menu.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('adminassets/assets/css/responsive.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="{{ asset('adminassets/assets/js/modernizr.min.js') }}"></script>

    </head>


    <body class="bg-transparent">

        <!-- HOME -->
        <section>
            <div class="container-alt">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="wrapper-page">
                            <div class="m-t-40 account-pages">
                                <div class="text-center account-logo-box">
                                    <h2 class="text-uppercase">
                                        <a href="" class="text-success">
                                            <span><img src="{{ asset('adminassets/assets/images/logo.png') }}" alt="" height="36"></span>
                                        </a>
                                    </h2>
                                    <!--<h4 class="text-uppercase font-bold m-b-0">Sign In</h4>-->
                                </div>
                                <div class="account-content">
                                    <div class="text-center m-b-20">
                                        <p class="text-muted m-b-0 font-13">Enter your email address and we'll send you an email with instructions to reset your password.  </p>
                                    </div>
                                    <form class="form-horizontal" method="post" action="{{ route('admin.password.email') }}">
                                        {{ csrf_field() }}
                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                            <div class="col-xs-12">
                                                <input class="form-control" type="email" name="email" required="" placeholder="Email" value="{{ old('email') }}">
                                                @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group account-btn text-center m-t-10">
                                            <div class="col-xs-12">
                                                <button class="btn w-md btn-bordered btn-danger waves-effect waves-light" type="submit">Send Password Reset Link</button>
                                            </div>
                                        </div>

                                    </form>

                                    <div class="clearfix"></div>

                                </div>
                            </div>
                            <!-- end card-box-->


                        </div>
                        <!-- end wrapper -->

                    </div>
                </div>
            </div>
          </section>
          <!-- END HOME -->

        <script>
            var resizefunc = [];
        </script>

        <!-- jQuery  -->
        <script src="{{ asset('adminassets/assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('adminassets/assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('adminassets/assets/js/detect.js') }}"></script>
        <script src="{{ asset('adminassets/assets/js/fastclick.js') }}"></script>
        <script src="{{ asset('adminassets/assets/js/jquery.blockUI.js') }}"></script>
        <script src="{{ asset('adminassets/assets/js/waves.js') }}"></script>
        <script src="{{ asset('adminassets/assets/js/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('adminassets/assets/js/jquery.scrollTo.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('adminassets/assets/js/jquery.core.js') }}"></script>
        <script src="{{ asset('adminassets/assets/js/jquery.app.js') }}"></script>

    </body>
</html>
