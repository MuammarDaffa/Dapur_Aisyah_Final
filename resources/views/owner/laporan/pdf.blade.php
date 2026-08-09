<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemasukan Dapur Aisyah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 20px; }
        .text-muted { color: #6c757d; }
        
        .header { margin-bottom: 30px; }
        .title { font-size: 20px; text-transform: uppercase; margin: 0 0 5px 0;}
        .brand { font-size: 26px; font-weight: bold; color: #2c3e50; margin: 0 0 5px 0;}
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h1 class="brand">Dapur Aisyah</h1>
        <h2 class="title">LAPORAN PEMASUKAN</h2>
        <div class="text-muted">
            @if($request->filled('period'))
                Periode: 
                @if($request->period == 'weekly') Minggu Ini
                @elseif($request->period == 'monthly') Bulan Ini
                @elseif($request->period == 'yearly') Tahun Ini
                @endif
            @elseif($request->filled('start_date') || $request->filled('end_date'))
                Periode: 
                {{ $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d M Y') : 'Awal' }} 
                - 
                {{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d M Y') : 'Sekarang' }}
            @else
                Periode: Semua Waktu
            @endif
            
            @if($request->filled('tipe_layanan'))
                <br>
                Layanan: Katering {{ ucfirst($request->tipe_layanan) }}
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="20%">Nomor Pesanan</th>
                <th width="15%">Tanggal Transaksi</th>
                <th width="25%">Pelanggan</th>
                <th width="20%">Layanan</th>
                <th width="15%" class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesanan as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->nomor_pesanan }}</td>
                    <td>{{ $p->created_at->format('d M Y') }}</td>
                    <td>{{ $p->user->name ?? '-' }}</td>
                    <td>Katering {{ ucfirst($p->tipe_layanan) }}</td>
                    <td class="text-end">{{ number_format($p->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pesanan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end fw-bold" style="border: none; padding-top: 15px;">Total Pemasukan:</td>
                <td class="text-end fw-bold" style="border: none; padding-top: 15px;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
