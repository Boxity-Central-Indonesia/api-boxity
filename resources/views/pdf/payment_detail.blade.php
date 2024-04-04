@extends('pdf.master')
@section('title', 'Payment Receipt')

@section('content')
    @if (!function_exists('formatRupiah'))
        @include('app/helpers/helpers')
    @endif

    <h3>Invoice To:</h3>
    <table border="1" cellspacing="0" cellpadding="10" width="100%">
        <tr>
            <th>Kode Transaksi</th>
            <td>{{ $formattedOrder['kode_payment'] }}</td>
        </tr>
        <tr>
            <th>{{ $formattedOrder['vendor']['transaction_type'] == 'outbound' ? 'Customer' : 'Supplier' }}</th>
            <td>{{ $formattedOrder['vendor']['name'] }}</td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td>{{ $formattedOrder['vendor']['address'] }}</td>
        </tr>
    </table>

    <table border="1" cellspacing="0" cellpadding="10" width="100%">
        <h3>Rincian {{ $formattedOrder['vendor']['transaction_type'] == 'outbound' ? 'Penerimaan' : 'Pengiriman' }}</h3>
        <tr>
            <th>Total Tagihan</th>
            <td>{{ formatRupiah($formattedOrder['invoice']['total_amount']) }}</td>
        </tr>
        <tr>
            <th>Tagihan Terbayar</th>
            <td>{{ formatRupiah($formattedOrder['invoice']['paid_amount']) }}</td>
        </tr>
        <tr>
            <th>Waktu Tagihan Terbayar</th>
            <td>{{ $formattedOrder['invoice']['created_at'] }}</td>
        </tr>
        <tr>
            <th>Sisa Tagihan</th>
            <td>{{ formatRupiah($formattedOrder['invoice']['balance_due']) }}</td>
        </tr>
        <tr>
            <th>URL</th>
            <td><a href="{{ $pdfUrl }}">{{ $pdfUrl }}</a></td>
        </tr>
        <tr>
            <td colspan="2">
                <img src="{{ $qrCodePath }}" alt="QR Code">
            </td>
        </tr>
    </table>
@endsection
