<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="@yield('html-class', 'h-100')">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Default page')</title>
        <link href="css/app.css" rel="stylesheet">
    </head>
    <body class="@yield('body-class', 'h-100 d-flex flex-column')">
        <nav class="navbar border-bottom">
            <div class="container">
                <a class="navbar-brand" href="/">The test project on Laravel</a>
                @auth
                <span class="navbar-text ms-auto">
                    Hello {{ auth()->user()->name }}
                </span>
                <ul class="navbar-nav ms-3">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                    </li>
                </ul>
                @endauth
            </div>
        </nav>
        @yield('content')
        <footer class="@yield('footer-class', 'd-flex justify-content-center mt-auto border-top')">
            @section('footer-content')
                <p>Make with love</p>
            @show
        </footer>
        @section('scripts')
            <script defer type="text/javascript" src="js/app.js"></script>
        @show
    </body>
</html>