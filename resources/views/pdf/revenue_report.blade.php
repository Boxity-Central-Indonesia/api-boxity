@extends('pdf.master')
@section('title', 'Revenue Report')
@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Akun</th>
                <th>Tanggal</th>
                <th>Jenis Pembukuan</th>
                <th>Transaksi</th>
                <th class="align-right">Jumlah Biaya</th>
                <th class="align-right">Saldo Akun</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($revenueData as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->account->name }}</td>
                    <td>{{ $item->date }}</td>
                    <td>{{ $item->account->type }}</td>
                    <td>{{ $item->type }}</td>
                    <td class="align-right">Rp. {{ number_format($item->amount, 0, ',', '.') }}</td>
                    <td class="align-right">{{ number_format($item->account->balance, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
