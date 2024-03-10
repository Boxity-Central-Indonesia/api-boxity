@extends('pdf.master')
@section('title', 'Purchases Report')
@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Transaksi</th>
                <th>Customer</th>
                <th>Nama Produk</th>
                <th class="align-right">Harga Satuan</th>
                <th class="align-right">Kuantitas</th>
                <th class="align-right">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseData as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_order }}</td>
                    <td>{{ $item->vendor_name }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="align-right">Rp. {{ number_format($item->price_per_unit, 0, ',', '.') }}</td>
                    <td class="align-right">{{ number_format($item->quantity, 0, ',', '.') }} Pcs
                    </td>
                    <td class="align-right">Rp. {{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
