@if (
    !function_exists('getCompanyName') ||
        !function_exists('getCompanyAddress') ||
        !function_exists('getCompanyPhone') ||
        !function_exists('getCompanyEmail'))
    @include('app.helpers.helpers')
@endif
<!doctype html>
<html lang="en">

<head>
    <title>@yield('title') - {{ Carbon\Carbon::now() }}</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="custom-pdf.css">
</head>

<body>
    <div class="container-fluid">
        <div class="col-lg-12 text-center">
            <h3>{{ getCompanyName() }}</h3>
            <h5>{{ getCompanyAddress() }} | No. Telepon: {{ getCompanyPhone() }} | Email: {{ getCompanyEmail() }}</h5>
        </div>
    </div>
    <hr>
    <h5 class="align-center mt-4" style="text-transform: uppercase;">@yield('title')</h5>
    <p class="align-center" style="text-transform: capitalize;">Waktu Cetak:
        {{ Carbon\Carbon::now()->translatedFormat('d F Y h:m:s') }}</p>
    @yield('content')
    <footer class="my-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-left mt-3">
                    Dicetak pada {{ Carbon\Carbon::now()->translatedFormat('d F Y h:m:s') }}
                </div>
                <div class="col-12 text-left">
                    &copy;Copyright {{ Carbon\Carbon::now()->format('Y') }}&nbsp;{{ getCompanyName() }}. All rights
                    reserved by <abbr title="PT Boxity Central Indonesia"><a href="https://boxity.id"
                            target="_blank">PT DHK Jaya Manufacturing</a></abbr>.
                </div>
            </div>
        </div>
    </footer>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>
