{{-- resources/views/transactions/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">

                {{-- Panel 1: Detail Pemesanan --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Detail Pemesanan</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>No. Transaksi:</strong> {{ $transaction->transaction_number }}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Nama Pelanggan:</strong> {{ $transaction->customer->name ?? '-' }}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Tanggal Pemesanan:</strong> {{ \Carbon\Carbon::parse($transaction->order_date)->format('d M Y') }}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Alamat Pengiriman:</strong> {{ $transaction->shipping_address ?? '-' }}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Nama Pemesan:</strong> {{ $transaction->orderer_name ?? '-' }}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Email Pemesan:</strong> {{ $transaction->orderer_email ?? '-' }}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Telepon Pemesan:</strong> {{ $transaction->orderer_phone ?? '-' }}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Jumlah Barang:</strong> {{ $transaction->details->count() }} item</p>
                    <div class="mt-4 text-right">
                        {{-- Tombol untuk melihat detail barang lebih lanjut atau mengedit --}}
                        <a href="{{ route('transactions.show', $transaction->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Lihat Detail Pesanan Lengkap
                        </a>
                    </div>
                </div>

                {{-- Panel 2: Penawaran Harga --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Penawaran Harga</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Status Proses:</strong> <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($transaction->process_status == 'PO Diterima') bg-blue-500 text-white
                        @elseif($transaction->process_status == 'PH Dikirim') bg-indigo-500 text-white
                        @else bg-gray-500 text-white
                        @endif
                        ">{{ $transaction->process_status }}</span></p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Subtotal PH:</strong>
                        @php
                            $phSubtotal = 0;
                            foreach ($transaction->details as $detail) {
                                if ($detail->selectedSupplierPrice) {
                                    $phSubtotal += $detail->selectedSupplierPrice->price * $detail->quantity;
                                }
                            }
                        @endphp
                        Rp {{ number_format($phSubtotal, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Catatan PH:</strong> {{ $transaction->ph_notes ?? '-' }}</p>
                    <div class="mt-4 text-right">
                        {{-- Perbaiki nama rute di sini --}}
                        @if($transaction->process_status != 'PO Diterima') {{-- PH bisa di-download setelah harga supplier diinput --}}
                            <a href="{{ route('transactions.download_ph_pdf', $transaction->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-file-pdf mr-2"></i> Download PH PDF
                            </a>
                        @else
                            <span class="text-sm text-gray-500">PH akan tersedia setelah input harga supplier.</span>
                        @endif
                    </div>

                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Barang Dipesan</h4>
                    @if ($transaction->details->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Tidak ada barang yang dipesan untuk transaksi ini.</p>
                    @else
                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg mb-6">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">Nama Barang</th>
                                        <th scope="col" class="py-3 px-6">Jumlah</th>
                                        <th scope="col" class="py-3 px-6">Spesifikasi/Catatan</th>
                                        <th scope="col" class="py-3 px-6">Harga Final per Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->details as $detail)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="py-4 px-6">{{ $detail->item_name }}</td>
                                            <td class="py-4 px-6">{{ $detail->quantity }}</td>
                                            <td class="py-4 px-6">{{ $detail->specification ?? '-' }}</td>
                                            <td class="py-4 px-6">Rp {{ number_format($detail->selectedSupplierPrice->price ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                {{-- Panel 3: Konfirmasi PO --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Konfirmasi PO</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Status PO:</strong>
                        @if($transaction->invoice && $transaction->invoice->po_file)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500 text-white">Sudah Diunggah</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500 text-white">Belum Diunggah</span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Status Proses:</strong> <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($transaction->process_status == 'PH Dikirim') bg-indigo-500 text-white
                        @elseif($transaction->process_status == 'PO Dikonfirmasi') bg-green-500 text-white
                        @else bg-gray-500 text-white
                        @endif
                        ">{{ $transaction->process_status }}</span></p>
                    <div class="mt-4 text-right">
                        @if($transaction->invoice && $transaction->invoice->po_file)
                            <a href="{{ Storage::url(str_replace('storage/', 'public/', $transaction->invoice->po_file)) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-eye mr-2"></i> Lihat File PO
                            </a>
                        @else
                            <a href="{{ route('transactions.confirm_po_received', $transaction->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-black border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-upload mr-2"></i> Upload File PO
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Panel 4: Invoice --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Invoice</h3>
                    @if($transaction->invoice)
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>No. Invoice:</strong> {{ $transaction->invoice->invoice_number }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Tanggal Invoice:</strong> {{ \Carbon\Carbon::parse($transaction->invoice->invoice_date)->format('d M Y') }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Jatuh Tempo:</strong> {{ $transaction->invoice->due_date ? \Carbon\Carbon::parse($transaction->invoice->due_date)->format('d M Y') : '-' }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Total Invoice:</strong> Rp {{ number_format($transaction->invoice->total_amount, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Status Pembayaran:</strong> <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($transaction->payment_status == 'Belum Bayar') bg-red-500 text-white
                            @elseif($transaction->payment_status == 'Jatuh Tempo') bg-orange-500 text-white
                            @elseif($transaction->payment_status == 'Lunas') bg-green-500 text-white
                            @else bg-gray-400 text-white
                            @endif
                            ">{{ $transaction->payment_status }}</span></p>
                        <div class="mt-4 text-right">
                            {{-- Perbaiki nama rute di sini --}}
                            <a href="{{ route('transactions.download_invoice_pdf', $transaction->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-file-pdf mr-2"></i> Download Invoice PDF
                            </a>
                            {{-- Tombol Update Status Pembayaran (mengarah ke form di TransactionController) --}}
                            @if($transaction->payment_status != 'Lunas')
                                <a href="{{ route('transactions.edit_payment_status', $transaction->id) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                                    <i class="fas fa-money-check-alt mr-2"></i> Update Pembayaran
                                </a>
                            @endif
                            @if($transaction->payment_status == 'Lunas' && $transaction->process_status != 'Selesai')
                                <form action="{{ route('transactions.mark_as_completed', $transaction->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Tandai transaksi ini sebagai Selesai?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                        <i class="fas fa-check-circle mr-2"></i> Tandai Selesai
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                            <span class="block sm:inline">Invoice belum dibuat untuk transaksi ini.</span>
                        </div>
                        <div class="mt-4 text-right">
                            @if($transaction->process_status == 'PO Dikonfirmasi' || ($transaction->process_status == 'PH Dikirim' && $transaction->ph_notes))
                                <a href="{{ route('transactions.generate_invoice', $transaction->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-file-invoice-dollar mr-2"></i> Buat Invoice
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

            </div>

            {{-- Kembali ke Dashboard --}}
            <div class="flex justify-start mt-6">
                <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Kembali ke Daftar Transaksi
                </a>
            </div>
        </div>
    </div>
    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @endpush
</x-app-layout>
