@extends('pdf.master')
@section('title', 'Ledger Report')
@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Jenis</th>
                <th>Nama Akun</th>
                <th>Tanggal</th>
                <th>Catatan</th>
                <th class="align-right">Debit</th>
                <th class="align-right">Credit</th>
                <th class="align-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $account)
                @foreach ($account['entries'] as $entry)
                    <tr>
                        <td>{{ $account['account_type'] }}</td>
                        <td>{{ $account['account_name'] }}</td>
                        <td>{{ $entry['date'] ?? '0000-00-00' }}</td>
                        <td>{{ $entry['description'] ?? 'No description' }}</td>
                        <td class="align-right">Rp. {{ number_format($entry['debit'] ?? 0, 0, ',', '.') }}</td>
                        <td class="align-right">Rp. {{ number_format($entry['credit'] ?? 0, 0, ',', '.') }}</td>
                        <td class="align-right">Rp. {{ number_format($entry['running_balance'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endsection
