@if (!function_exists('formatRupiah'))
    @include('app/helpers/helpers')
@endif
@extends('pdf.master')
@if ($formattedOrder && $formattedOrder['vendor'])
    @php
        $transactionType = $formattedOrder['vendor']['transaction_type'];
        $orderCode = $formattedOrder['kode_order'];
        $title = 'Transaksi ' . ($transactionType == 'outbound' ? 'Penjualan' : 'Pembelian') . ' ' . $orderCode;
    @endphp
    @section('title', $title)
@endif

@section('content')
    <table border="1" cellspacing="0" cellpadding="10" width="100%">
        <tr>
            <th>Kode Transaksi</th>
            <td>{{ $formattedOrder['kode_order'] }}</td>
        </tr>
        <tr>
            <th>Kode Ref</th>
            <td>{{ $formattedOrder['no_ref'] }}</td>
        </tr>
        <tr>
            <th>{{ $formattedOrder['vendor']['transaction_type'] == 'outbound' ? 'Customer' : 'Supplier' }}</th>
            <td>{{ $formattedOrder['vendor']['name'] }}</td>
        </tr>
        <tr>
            <th>Uraian</th>
            <td>{{ $formattedOrder['details'] }}</td>
        </tr>
        <tr>
            <th>Tanggal {{ $formattedOrder['vendor']['transaction_type'] == 'outbound' ? 'Penjualan' : 'Pembelian' }}</th>
            <td>{{ $formattedOrder['created_at'] }}</td>
        </tr>

        <tr>
            <th>Gudang {{ $formattedOrder['vendor']['transaction_type'] == 'outbound' ? 'Tujuan' : 'Asal' }}</th>
            <td>{{ $formattedOrder['warehouse']['name'] }}</td>
        </tr>
        <tr>
            <th>Kode transaksi invoices</th>
            <td>
                @if (count($formattedOrder['invoices']) > 0)
                    {{ implode(', ', $formattedOrder['invoices']->pluck('kode_invoice')->toArray()) }}
                @else
                    --
                @endif
            </td>
        </tr>
        <tr>
            <th>Order Status / Type</th>
            <td>{{ $formattedOrder['order_status'] }} | {{ $formattedOrder['order_type'] }}</td>
        </tr>
        <tr>
            <td colspan="2">
                <table border="1" cellspacing="0" cellpadding="5" width="100%">
                    <tr>
                        <th>Nama Produk</th>
                        <th>Quantity</th>
                        <th style="text-align:right;">Harga Satuan</th>
                        <th style="text-align:right;">Total Harga</th>
                    </tr>
                    @foreach ($formattedOrder['products'] as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['quantity'] }} Pcs</td>
                            <td style="text-align:right;">{{ formatRupiah($product['price_per_unit']) }}</td>
                            <td style="text-align:right;">{{ formatRupiah($product['total_price']) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" align="right">Jumlah Tagihan</td>
                        <td style="text-align:right;">{{ formatRupiah($formattedOrder['total_price']) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="right">PPN</td>
                        <td style="text-align:right;">{{ formatRupiah($formattedOrder['taxes']) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="right">Biaya Pengiriman</td>
                        <td style="text-align:right;">{{ formatRupiah($formattedOrder['shipping_cost']) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="right">Total Jumlah Tagihan</td>
                        <td style="text-align:right;">
                            {{ formatRupiah($formattedOrder['total_price'] + $formattedOrder['taxes'] + $formattedOrder['shipping_cost']) }}
                        </td>
                    </tr>
                </table>
            </td>
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
