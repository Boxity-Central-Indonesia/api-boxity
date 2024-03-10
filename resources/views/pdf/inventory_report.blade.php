@extends('pdf.master')
@section('title', 'Inventory Report')
@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Barang</th>
                <th>Nama Produk</th>
                <th>Kategori Produk</th>
                <th>Tipe Hewan</th>
                <th class="align-right">Kuantitas</th>
                <th class="align-right">Harga Satuan</th>
                <th class="align-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventoryData as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name }}</td>
                    <td>{{ $item->animal_type }}</td>
                    <td class="align-right">{{ number_format($item->stock, 0, ',', '.') }} Pcs</td>
                    <td class="align-right">Rp. {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="align-right">Rp. {{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
