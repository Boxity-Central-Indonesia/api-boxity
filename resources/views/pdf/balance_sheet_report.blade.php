@extends('pdf.master')
@section('title', 'Balance Sheet Report')
@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Kategori Keuangan</th>
                <th class="align-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Assets</td>
                <td class="align-right">Rp. {{ number_format($assets, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Liabilities</td>
                <td class="align-right">Rp. {{ number_format($liabilities, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Equity</td>
                <td class="align-right">Rp. {{ number_format($equity, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
@endsection
