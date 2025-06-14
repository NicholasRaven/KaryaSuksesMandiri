<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $invoice->invoice_number }}</title>
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
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
            color: #666;
        }
        .invoice-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-details td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-details .label {
            width: 120px;
            font-weight: bold;
        }
        .billed-to {
            margin-top: 20px;
            margin-bottom: 20px;
            padding-left: 5px;
            border-left: 3px solid #007bff;
        }
        .billed-to h3 {
            margin: 0;
            font-size: 14px;
            color: #333;
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
        .summary-table {
            width: 40%; /* Adjust as needed */
            float: right;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .summary-table .label {
            font-weight: bold;
        }
        .total-amount {
            background-color: #e2f0ff;
            font-size: 14px;
            font-weight: bold;
        }
        .signature {
            margin-top: 80px;
            text-align: center;
            float: right; /* Untuk posisi di kanan bawah */
            width: 200px;
        }
        .signature p {
            margin-top: 50px;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .po-info {
            margin-top: 20px;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>CV. KARYA SUKSES MANDIRI</p>
        <p>Jl. Contoh Alamat No. 123, Kota Contoh, Kode Pos 12345</p>
        <p>Telepon: (021) 12345678 | Email: info@cvksm.com</p>
    </div>

    <table class="invoice-details">
        <tr>
            <td class="label">Invoice No:</td>
            <td>{{ $invoice->invoice_number }}</td>
            <td class="label">Tanggal Invoice:</td>
            <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal PO:</td>
            <td>{{ \Carbon\Carbon::parse($transaction->order_date)->format('d M Y') }}</td>
            <td class="label">Jatuh Tempo:</td>
            <td>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Transaksi:</td>
            <td>{{ $transaction->transaction_number }}</td>
            <td class="label">Status Pembayaran:</td>
            <td>{{ $transaction->payment_status }}</td>
        </tr>
    </table>

    <div class="billed-to">
        <h3>Ditujukan Kepada:</h3>
        <p><strong>{{ $transaction->customer->name ?? '-' }}</strong></p>
        <p>{{ $transaction->customer->address ?? '-' }}</p>
        <p>Kontak: {{ $transaction->customer->contact_person ?? '-' }} ({{ $transaction->customer->phone ?? '-' }})</p>
    </div>

    <div class="section-title">Detail Barang</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Barang</th>
                <th>Spesifikasi/Catatan</th>
                <th>Kuantitas</th>
                <th>Harga Final Per Unit</th>
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
    </table>

    <div class="clearfix">
        <table class="summary-table">
            <tr>
                <td class="label">Subtotal:</td>
                <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Pajak ({{ $invoice->tax_percentage ?? 0 }}%):</td>
                <td>Rp {{ number_format($taxAmount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Biaya Lain-lain:</td>
                <td>Rp {{ number_format($invoice->other_costs ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-amount">
                <td class="label">TOTAL INVOICE:</td>
                <td>Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="po-info">
        @if($invoice->po_file)
            <p>File PO: Terlampir ({{ basename($invoice->po_file) }})</p>
        @else
            <p>File PO: Tidak terlampir</p>
        @endif
        <p>Metode Pembayaran: Transfer Bank (contoh)</p>
        <p>Rekening: BCA 1234567890 a.n. CV. Karya Sukses Mandiri</p>
    </div>

    <div class="signature">
        <p>Hormat Kami,</p>
        <p style="width: 180px;">(Nama Petugas Keuangan)</p>
    </div>
</body>
</html>
