@extends('pdf.master')
@section('title', 'Inventory Report')
@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Produk</th>
                <th>Tipe Hewan</th>
                <th class="align-right">Harga Rata-Rata</th>
                <th class="align-right">Jumlah Kuantitas</th>
                <th class="align-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventoryData as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->animal_type }}</td>
                    <td class="align-right">Rp. {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="align-right">{{ number_format($item->stock, 0, ',', '.') }} {{ $item->unit_of_measure }}</td>
                    <td class="align-right">Rp. {{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
