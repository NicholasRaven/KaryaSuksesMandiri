<!DOCTYPE html>
<html>
<head>
    <title>Penawaran Harga {{ $transaction->transaction_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
            color: #666;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table .label {
            width: 150px;
            font-weight: bold;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
        }
        .notes {
            margin-top: 30px;
            font-size: 10px;
            color: #777;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .footer p {
            margin: 0;
            font-size: 10px;
        }
        .signature {
            margin-top: 40px;
            text-align: center;
        }
        .signature p {
            margin-top: 50px;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PENAWARAN HARGA</h1>
        <p>CV. KARYA SUKSES MANDIRI</p>
        <p>Jl. Kolonel Andrians Lintas Barat Sukabangun II, Palembang</p>
        <p>Telepon: (0711) 5611815 | Email: ksm@yahoo.com</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nomor Transaksi:</td>
            <td>{{ $transaction->transaction_number }}</td>
            <td class="label">Nama Pelanggan:</td>
            <td>{{ $transaction->customer->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Pemesanan:</td>
            <td>{{ \Carbon\Carbon::parse($transaction->order_date)->format('d M Y') }}</td>
            <td class="label">Alamat Pelanggan:</td>
            <td>{{ $transaction->customer->address ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal PH:</td>
            <td>{{ now()->format('d M Y') }}</td>
            <td class="label">Nama Pemesan:</td>
            <td>{{ $transaction->customer->contact_person ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label"></td>
            <td></td>
            <td class="label">Telepon Pemesan:</td>
            <td>{{ $transaction->customer->phone ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Detail Barang</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Barang</th>
                <th>Spesifikasi/Catatan</th>
                <th>Kuantitas</th>
                <th>Harga Per Unit</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($transaction->details as $detail)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $detail->item_name ?? ($detail->item->name ?? '-') }}</td>
                    <td>{{ $detail->specification_notes ?? '-' }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>Rp {{ number_format($detail->final_price_per_unit ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format(($detail->final_price_per_unit ?? 0) * $detail->quantity, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold;">Subtotal:</td>
                <td>Rp {{ number_format($phSubtotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="notes">
        <p><strong>Catatan:</strong></p>
        <p>{{ $transaction->ph_notes ?? 'Tidak ada catatan PH.' }}</p>
    </div>

    <div class="signature">
        <p>Hormat Kami,</p>
        <p style="width: 200px;">Priyo DA</p>
    </div>
</body>
</html>
