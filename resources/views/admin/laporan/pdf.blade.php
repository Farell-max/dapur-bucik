<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Dapur Bucik</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1B362D;
            font-size: 12px;
            line-height: 1.5;
            margin: 24px;
        }
        .header {
            border-bottom: 3px solid #E29E31;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #1B362D;
        }
        .header p {
            margin: 4px 0 0;
            color: #555;
            font-size: 11px;
        }
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary td {
            border: 1px solid #d9d2c5;
            background: #FAF4E8;
            padding: 10px 12px;
            width: 33.33%;
        }
        .summary .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #555;
            display: block;
        }
        .summary .value {
            font-size: 15px;
            font-weight: bold;
            color: #1B362D;
            margin-top: 4px;
            display: block;
        }
        h2 {
            font-size: 14px;
            margin: 18px 0 8px;
            color: #1B362D;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        table.data th {
            background: #1B362D;
            color: #FAF4E8;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
        }
        table.data td {
            border-bottom: 1px solid #e5dfd3;
            padding: 7px 10px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Keuangan &amp; Penjualan — Dapur Bucik</h1>
        <p>Periode: {{ $startDate }} s/d {{ $endDate }} | Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Total Pendapatan</span>
                <span class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </td>
            <td>
                <span class="label">Total Transaksi</span>
                <span class="value">{{ $totalTransaksi }} pesanan</span>
            </td>
            <td>
                <span class="label">Rata-rata Pesanan</span>
                <span class="value">Rp {{ number_format($rataRata, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <h2>Ringkasan Penjualan Harian</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Pesanan</th>
                <th>Metode Dominan</th>
                <th class="text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dailySales as $day)
                <tr>
                    <td>{{ $day['tanggal'] }}</td>
                    <td>{{ $day['jumlah_pesanan'] }} pesanan</td>
                    <td>{{ $day['metode_dominan'] }}</td>
                    <td class="text-right">Rp {{ number_format($day['pendapatan'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Belum ada transaksi selesai/lunas pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Menu Terlaris</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Peringkat</th>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th>Terjual</th>
                <th class="text-right">Total Omzet</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topMenus as $idx => $menu)
                <tr>
                    <td>#{{ $idx + 1 }}</td>
                    <td>{{ $menu['nama'] }}</td>
                    <td>{{ $menu['kategori'] }}</td>
                    <td>{{ $menu['total_qty'] }} porsi</td>
                    <td class="text-right">Rp {{ number_format($menu['total_omzet'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada penjualan menu pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
