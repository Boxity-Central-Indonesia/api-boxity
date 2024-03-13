@extends('pdf.master')
@section('title', 'Vendor Report')
@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Order</th>
                <th>Nama Vendor</th>
                <th>Nama Barang</th>
                <th class="align-right">Harga Satuan</th>
                <th class="align-right">PPN</th>
                <th class="align-right">Tarif Pengiriman</th>
                <th class="align-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filteredTransactions as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['kode_order'] }}</td>
                    <td>{{ $item['nama_vendor'] }}</td>
                    <td>{{ $item['nama_product'] }}</td>
                    <td class="align-right">Rp. {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                    <td class="align-right">Rp. {{ number_format($item['taxes'], 0, ',', '.') }}</td>
                    <td class="align-right">Rp. {{ number_format($item['shipping_cost'], 0, ',', '.') }}</td>
                    <td class="align-right">Rp. {{ number_format($item['total_price'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
