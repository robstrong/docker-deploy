<html>
    <head>
        <title>Deploy - @yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/lib/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="/css/common.css">
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
        @yield('head')
    </head>

    <body class="login">
        <div class="container">
            <h1>@yield('title')</h1>
            @yield('content')
            @if (strlen(Session::get('error')))
                <br>
                <br>
                <div class="alert alert-danger">{{{ Session::get('error') }}}</div>
            @endif
        </div>
        <script src="https://code.jquery.com/jquery.js"></script>
        <script src="/lib/bootstrap/js/bootstrap.min.js"></script>
    </body>
    @yield('scripts')
</html>
