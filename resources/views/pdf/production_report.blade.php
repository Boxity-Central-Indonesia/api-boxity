@extends('pdf.master')

@section('title', 'Production Report')

@section('content')
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Item</th>
                <th>Kode Transaksi</th>
                <th>Aktifitas Produksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groupedActivities as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['product_name'] }}</td>
                    <td>{{ $item['kodeOrder'] }}</td>
                    <td>
                        <table border="0" cellpadding="5" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Jenis Aktivitas</th>
                                    <th>Status Produksi</th>
                                    <th>Tanggal Aktivitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item['activities'] as $activity)
                                    <tr>
                                        <td>{{ $activity['activity_type'] }}</td>
                                        <td>{{ $activity['status_production'] }}</td>
                                        <td>{{ $activity['tanggal_aktifitas'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
