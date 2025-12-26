<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan - {{ $month }}/{{ $year }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            text-transform: uppercase;
            color: #1a56db;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-mono {
            font-family: 'Courier', monospace;
            font-size: 11px;
        }

        .footer-total {
            background-color: #1e40af;
            color: white;
            font-weight: bold;
        }

        .summary {
            margin-top: 20px;
            text-align: right;
        }

        .date-print {
            font-size: 10px;
            color: #999;
            margin-top: 50px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Laporan Pendapatan Bulanan</h2>
        <p>Toko Dinda</p>
        <p style="font-size: 12px;">Periode: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="50%">No. Invoice</th>
                <th width="35%" class="text-right">Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse ($sales as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                    <td class="font-mono">{{ $sale->invoice_number }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                </tr>
                @php $total += $sale->total_price; @endphp
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px; color: #999;">
                        Tidak ada data penjualan pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td colspan="2" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="date-print">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>

</body>

</html>
