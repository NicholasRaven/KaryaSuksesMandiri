{{-- resources/views/transactions/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-bold mb-4">Detail Transaksi {{ $transaction->transaction_number }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>No Transaksi:</strong> {{ $transaction->transaction_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Nama Pelanggan:</strong> {{ $transaction->customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Tanggal Pemesanan:</strong> {{ \Carbon\Carbon::parse($transaction->order_date)->translatedFormat('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Status Proses:</strong>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @if ($transaction->process_status == 'PO Diterima') bg-blue-100 text-blue-800
                                    @elseif ($transaction->process_status == 'Invoice Dibuat') bg-purple-100 text-purple-800
                                    @elseif ($transaction->process_status == 'PH Dikirim') bg-yellow-100 text-yellow-800
                                    @elseif ($transaction->process_status == 'Selesai') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $transaction->process_status }}
                                </span>
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600 dark:text-gray-400"><strong>Status Pembayaran:</strong>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @if ($transaction->payment_status == 'Belum Ada Invoice') bg-gray-100 text-gray-800
                                    @elseif ($transaction->payment_status == 'Belum Bayar') bg-red-100 text-red-800
                                    @elseif ($transaction->payment_status == 'Lunas') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $transaction->payment_status }}
                                </span>
                            </p>
                        </div>
                    </div>

                <div class="mb-8 p-4 border border-gray-300 dark:border-gray-700 rounded-md">
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
                <div class="mb-8 p-4 border border-gray-300 dark:border-gray-700 rounded-md">
                <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3 mt-6">Harga Penawaran Supplier (per Barang)</h4>

                @foreach ($transaction->details as $detail)
                    <div class="mb-4 p-4 border border-gray-300 dark:border-gray-700 rounded-md">
                        <p class="font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $detail->item_name }} ({{ $detail->quantity }} pcs)</p>
                        @if ($detail->supplierPrices->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400">Belum ada harga penawaran dari supplier.</p>
                        @else
                            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="py-3 px-6">Supplier</th>
                                            <th scope="col" class="py-3 px-6">Harga</th>
                                            <th scope="col" class="py-3 px-6">Catatan</th>
                                            <th scope="col" class="py-3 px-6">Dipilih</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detail->supplierPrices as $supplierPrice)
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <td class="py-4 px-6">{{ $supplierPrice->supplier->name }}</td>
                                                <td class="py-4 px-6">Rp {{ number_format($supplierPrice->price, 0, ',', '.') }}</td>
                                                <td class="py-4 px-6">{{ $supplierPrice->notes ?? '-' }}</td>
                                                <td class="py-4 px-6">
                                                    @if ($supplierPrice->is_selected)
                                                        <span class="text-green-500">✔</span>
                                                    @else
                                                        <span class="text-red-500">✖</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach
                {{-- Tombol Download PH PDF --}}
                <a href="{{ route('transactions.download_ph_pdf', $transaction->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-4 mt-4">
                    <i class="fas fa-file-pdf mr-2"></i> Download PH PDF
                </a>
                </div>
                        @endif

                <div class="mb-8 p-4 border border-gray-300 dark:border-gray-700 rounded-md">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3 mt-6">Informasi Invoice</h4>
                    @if ($transaction->invoice)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400"><strong>No Invoice:</strong> {{ $transaction->invoice->invoice_number }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400"><strong>Tanggal Invoice:</strong> {{ \Carbon\Carbon::parse($transaction->invoice->invoice_date)->translatedFormat('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400"><strong>Jatuh Tempo:</strong> {{ $transaction->invoice->due_date ? \Carbon\Carbon::parse($transaction->invoice->due_date)->translatedFormat('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400"><strong>Subtotal:</strong> Rp {{ number_format($transaction->invoice->subtotal, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400"><strong>Pajak (%):</strong> {{ $transaction->invoice->tax_percentage }}%</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400"><strong>Biaya lain-lain:</strong> Rp {{ number_format($transaction->invoice->other_costs, 0, ',', '.') }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100"><strong>TOTAL INVOICE:</strong> Rp {{ number_format($transaction->invoice->total_amount, 0, ',', '.') }}</p>
                            </div>
                            @if ($transaction->invoice->po_file)
                            <div class="md:col-span-2">
                                <p class="text-gray-600 dark:text-gray-400"><strong>File PO:</strong> <a href="{{ Storage::url($transaction->invoice->po_file) }}" target="_blank" class="text-blue-500 hover:underline">Lihat File PO</a></p>
                            </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Invoice belum dibuat untuk transaksi ini.</p>
                    @endif
                      <a href="{{ route('transactions.download_invoice_pdf', $transaction->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-4">
                                <i class="fas fa-file-pdf mr-2"></i> Download Invoice PDF
                            </a>
                </div>
            </div>

                <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4">
                <a href="{{ route('transactions.index') }}"
                class="inline-block align-baseline font-bold text-sm text-red-600 hover:text-white border border-red-600 hover:bg-red-600 px-4 py-2 rounded transition">
                    Kembali ke Daftar Transaksi
                </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
