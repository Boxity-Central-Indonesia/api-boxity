@extends('pdf.master')

@section('title', 'Order Detail ' . $formattedOrder['kode_order'])

@section('content')
    <table border="1" cellspacing="0" cellpadding="10" width="100%">
        <tr>
            <th>ID</th>
            <td>{{ $formattedOrder['id'] }}</td>
        </tr>
        <tr>
            <th>Kode Order</th>
            <td>{{ $formattedOrder['kode_order'] }}</td>
        </tr>
        <tr>
            <th>Vendor</th>
            <td>{{ $formattedOrder['vendor']['name'] }}</td>
        </tr>
        <tr>
            <th>Products</th>
            <td>
                <table border="1" cellspacing="0" cellpadding="5" width="100%">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Price Per Unit</th>
                        <th>Total Price</th>
                    </tr>
                    @foreach ($formattedOrder['products'] as $product)
                        <tr>
                            <td>{{ $product['id'] }}</td>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['quantity'] }}</td>
                            <td>{{ $product['price_per_unit'] }}</td>
                            <td>{{ $product['total_price'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <th>Warehouse</th>
            <td>{{ $formattedOrder['warehouse']['name'] }}</td>
        </tr>
        <tr>
            <th>Invoices</th>
            <td>
                @if (count($formattedOrder['invoices']) > 0)
                    {{ implode(', ', $formattedOrder['invoices']->pluck('kode_invoice')->toArray()) }}
                @else
                    --
                @endif
            </td>
        </tr>
        <tr>
            <th>Total Price</th>
            <td>{{ $formattedOrder['total_price'] }}</td>
        </tr>
        <tr>
            <th>Order Status</th>
            <td>{{ $formattedOrder['order_status'] }}</td>
        </tr>
        <tr>
            <th>Order Type</th>
            <td>{{ $formattedOrder['order_type'] }}</td>
        </tr>
        <tr>
            <th>Taxes</th>
            <td>{{ $formattedOrder['taxes'] }}</td>
        </tr>
        <tr>
            <th>Shipping Cost</th>
            <td>{{ $formattedOrder['shipping_cost'] }}</td>
        </tr>
    </table>
@endsection
