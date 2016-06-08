<!DOCTYPE html>
<html>
    <head>
        <title>Freeradius Web - @yield('title')</title>
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
        </style>
    </head>
    <body>
        <div class="container">
			<div class="page-header">
                @section('header')
                    <div class="text-right">
                        @if (Auth::check())
                            Logged in as
                            <strong>{{ Auth::user()->name }}</strong>
                            {!! link_to('logout', 'Log Out') !!}
                        @else
                            {!! link_to('login', 'Log in') !!}
                        @endif
                    </div>
                @stop

				@yield('header')

			</div>

			@if (Session::has('message'))
				<div class="alert alert-success">
					{{ Session::get('message') }}
				</div>
			@endif

			@if (Session::has('error'))
				<div class="alert alert-warning">
					{{ Session::get('error') }}
				</div>
			@endif

            <div class="content">

                @yield('content')

            </div>
        </div>
    </body>
</html>
