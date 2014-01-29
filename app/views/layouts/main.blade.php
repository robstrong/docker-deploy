<html>
    <head>
        <title>Zunction - @yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/lib/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="/css/common.css">
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
        @yield('head')
    </head>

    <body>
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Zunction</a>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="{{ Request::segment(1) == '' || Request::segment(1) == 'sites'? 'active' : '' }}"><a href="/sites">Sites</a></li>
                        <li class="{{ Request::segment(1) == 'domains' ? 'active' : '' }}"><a href="/domains">Domains</a></li>
                        <li class="{{ Request::segment(1) == 'images' ? 'active' : '' }}"><a href="/builds">Images</a></li>
                        <li class="{{ Request::segment(1) == 'instances' ? 'active' : '' }}"><a href="/repositories">Instances</a></li>
                        <li class="{{ Request::segment(1) == 'users' ? 'active' : '' }}"><a href="/users">Users</a></li>
                    </ul>
                 <ul class="nav navbar-nav navbar-right">
                      <li><a href="/login/logout">Logout</a></li>
                </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <h1>@yield('title')</h1>
            @if (strlen(Session::get('success')))
                <div class="alert alert-success">{{{ Session::get('success') }}}</div>
            @endif
            @if (strlen(Session::get('info')))
                <div class="alert alert-info">{{{ Session::get('info') }}}</div>
            @endif
            @if (strlen(Session::get('error')))
                <div class="alert alert-danger">{{{ Session::get('error') }}}</div>
            @endif
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{{ $error }}}</div>
            @endforeach
            @yield('content')
            <br>
        </div>
        <br>
        <script src="https://code.jquery.com/jquery.js"></script>
        <script src="/lib/bootstrap/js/bootstrap.min.js"></script>
    </body>
    @yield('scripts')
</html>
