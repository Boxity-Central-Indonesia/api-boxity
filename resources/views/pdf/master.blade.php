<!DOCTYPE html>
<html>

<head>
    <title>@yield('title') - {{ Carbon\Carbon::now() }}</title>
    <link rel="stylesheet" href="custom-pdf.css">
</head>

<body>
    <h2 class="align-center">@yield('title')</h2>
    @yield('content')
    <div id="footer">
        <div class="left">
            Dicetak pada {{ Carbon\Carbon::now()->format('d F Y h:m:s') }}
        </div>
        <div class="center">
            Copyright &copy; PT. Boxity Central Indonesia
        </div>
    </div>
</body>

</html>
