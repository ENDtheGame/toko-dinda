<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Harian - {{ $date }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 20px;
        }

        .info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #f2f2f2;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            background-color: #eee;
            font-weight: bold;
            font-size: 14px;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
        }

        .signature {
            margin-top: 80px;
            border-top: 1px solid #000;
            display: inline-block;
            width: 200px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Toko Dinda - Grosir & Eceran</h1>
        <p>Laporan Penjualan Harian</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td style="border: none; width: 15%;">Tanggal Cetak</td>
                <td style="border: none;">: {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="border: none;">Periode Laporan</td>
                <td style="border: none;">: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">No. Invoice</th>
                <th style="width: 20%;">Waktu</th>
                <th style="width: 20%;">Kasir</th>
                <th class="text-right">Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($sales as $sale)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->created_at->format('H:i') }} WIB</td>
                    <td>{{ $sale->user->name ?? 'Admin' }}</td>
                    <td class="text-right">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada transaksi pada tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-section">
                <td colspan="4" class="text-right">TOTAL PENDAPATAN HARI INI:</td>
                <td class="text-right">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name }}</p>
        <p>Penanggung Jawab,</p>
        <div class="signature">
            (........................................)
        </div>
    </div>

</body>

</html>
