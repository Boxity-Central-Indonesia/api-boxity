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
            @php
                $totalDebit = 0;
                $totalCredit = 0;
                $prevAccountType = null;
                $prevAccountName = null;
                $rowspan = 0;
            @endphp
            @foreach ($data as $account)
                @php $rowspan += count($account['entries']); @endphp
                @foreach ($account['entries'] as $key => $entry)
                    <tr>
                        @if ($prevAccountType === $account['account_type'] && $prevAccountName === $account['account_name'] && $key > 0)
                            <td style="display: none;">{{ $account['account_type'] }}</td>
                            <td style="display: none;">{{ $account['account_name'] }}</td>
                        @else
                            <td rowspan="{{ $rowspan }}">{{ $account['account_type'] }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $account['account_name'] }}</td>
                        @endif
                        <td>{{ $entry['date'] ?? '0000-00-00' }}</td>
                        <td>{{ $entry['description'] ?? 'No description' }}</td>
                        <td class="align-right">Rp. {{ number_format($entry['debit'] ?? 0, 0, ',', '.') }}</td>
                        <td class="align-right">Rp. {{ number_format($entry['credit'] ?? 0, 0, ',', '.') }}</td>
                        @php
                            $totalDebit += $entry['debit'] ?? 0;
                            $totalCredit += $entry['credit'] ?? 0;
                            $totalBalance = $entry['running_balance'] ?? 0;
                        @endphp
                        <td class="align-right">Rp. {{ number_format($totalBalance, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $prevAccountType = $account['account_type'];
                        $prevAccountName = $account['account_name'];
                    @endphp
                @endforeach
                @php $rowspan = 0; @endphp
            @endforeach
            <tr>
                <td colspan="4" class="align-right"><b>Total Debit</b></td>
                <td colspan="3" class="align-right"><b>Rp. {{ number_format($totalDebit, 0, ',', '.') }}</b></td>
            </tr>
            <tr>
                <td colspan="4" class="align-right"><b>Total Credit</b></td>
                <td colspan="3" class="align-right"><b>Rp. {{ number_format($totalCredit, 0, ',', '.') }}</b></td>
            </tr>
            <tr>
                <td colspan="4" class="align-right"><b>Total Saldo</b></td>
                <td colspan="3" class="align-right"><b>Rp. {{ number_format($totalBalance, 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>
@endsection
