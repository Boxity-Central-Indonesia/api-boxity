@extends('pdf.master')
@section('title', 'Purchases Report')
@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Transaksi</th>
                <th>Tanggal Pembelian</th>
                <th>Customer</th>
                <th class="align-right">Total Tagihan</th>
                <th class="align-right">Tagihan Dibayar</th>
                <th class="align-center">Status Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseData as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_order }}</td>
                    <td>{{ $item->invoice_date }}</td>
                    <td>{{ $item->vendor_name }}</td>
                    <td class="align-right">Rp. {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    <td class="align-right">Rp. {{ number_format($item->paid_amount, 0, ',', '.') }}</td>
                    <td class="capitalize align-center">
                        @if ($item->invoice_status == 'paid')
                            Lunas
                        @else
                            Belum Lunas
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
