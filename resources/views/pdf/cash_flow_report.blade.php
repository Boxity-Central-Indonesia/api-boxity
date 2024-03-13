@extends('pdf.master')
@section('title', 'Cash Flow Report')
@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Akun</th>
                <th>Jenis</th>
                <th>Saldo Awal</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item['account_name'] }}</td>
                    <td>{{ $item['type'] }}</td>
                    <td class="align-right">Rp. {{ number_format($item['opening_balance'], 0, ',', '.') }}</td>
                    <td class="align-right">Rp. {{ number_format($item['total_debit'], 0, ',', '.') }}</td>
                    <td class="align-right">Rp. {{ number_format($item['total_credit'], 0, ',', '.') }}</td>
                    <td class="align-right">Rp. {{ number_format($item['net_cash_flow'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
