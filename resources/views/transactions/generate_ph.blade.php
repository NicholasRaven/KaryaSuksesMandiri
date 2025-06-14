{{-- resources/views/transactions/generate_ph.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Penawaran Harga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
<<<<<<< Updated upstream
                    <h3 class="text-center text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">PENAWARAN HARGA</h3>
                    <h4 class="text-center text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $transaction->customer->name }}</h4>
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400 mb-6">Tanggal : {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y') }}</p>

                    <p class="mb-4 text-gray-700 dark:text-gray-300">Berikut ini kami berikan penawaran harga dengan rincian sebagai berikut:</p>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg mb-6">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
=======
                    <h3 class="text-lg font-semibold mb-4">Detail Penawaran Harga Transaksi #{{ $transaction->transaction_number }}</h3>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p><strong>Nama Pelanggan:</strong> {{ $transaction->customer->name ?? '-' }}</p>
                            <p><strong>Tanggal Pemesanan:</strong> {{ \Carbon\Carbon::parse($transaction->order_date)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p><strong>Status Proses:</strong> <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-500 text-white">{{ $transaction->process_status }}</span></p>
                            <p><strong>Status Pembayaran:</strong> <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-400 text-white">{{ $transaction->payment_status }}</span></p>
                        </div>
                    </div>

                    <h4 class="text-md font-semibold mb-3">Barang Dipesan</h4>
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border dark:border-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
>>>>>>> Stashed changes
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Barang</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Spesifikasi/Catatan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kuantitas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga per Unit (PH)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                                </tr>
                            </thead>
<<<<<<< Updated upstream
                            <tbody>
                                @php
                                    $grandTotal = 0;
                                @endphp
                                @foreach ($transaction->details as $index => $detail)
                                    @php
                                        $hargaPerUnit = $detail->final_price_per_unit ?? 0;
                                        $jumlahHarga = $hargaPerUnit * $detail->quantity;
                                        $grandTotal += $jumlahHarga;
                                    @endphp
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6">{{ $index + 1 }}</td>
                                        <td class="py-4 px-6">{{ $detail->item_name }}</td>
                                        <td class="py-4 px-6">{{ $detail->quantity }} pcs</td>
                                        <td class="py-4 px-6">Rp {{ number_format($hargaPerUnit, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6">Rp {{ number_format($jumlahHarga, 0, ',', '.') }}</td>
=======
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($transaction->details as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->item_name ?? ($detail->item->name ?? '-') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->specification_notes ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($detail->selectedSupplierPrice)
                                                Rp {{ number_format($detail->selectedSupplierPrice->price, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($detail->selectedSupplierPrice)
                                                Rp {{ number_format($detail->selectedSupplierPrice->price * $detail->quantity, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
>>>>>>> Stashed changes
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <td colspan="4" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"><strong>Subtotal PH:</strong></td>
                                    <td class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"><strong>Rp {{ number_format($phSubtotal, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

<<<<<<< Updated upstream
                    <div class="mb-6">
                        <label for="catatan" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Catatan:</label>
                        <textarea name="catatan" id="catatan" rows="3" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">Harga belum termasuk pajak 11%</textarea>
                    </div>

                    <div class="mb-6">
                        <label for="cap_perusahaan" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Cap Perusahaan:</label>
                        <div class="border border-gray-300 dark:border-gray-700 rounded-md h-24 flex items-center justify-center text-gray-500 dark:text-gray-400">
                            Upload Cap Perusahaan (Opsional)
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 space-x-4">
                        <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            Kembali
                        </a>
                        <form action="{{ route('transactions.confirm_ph_sent', $transaction->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600 active:bg-green-800 dark:active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Tandai sebagai PH Dikirim
                            </button>
                        </form>
                    </div>
=======
                    <form action="{{ route('transactions.confirm_ph_sent', $transaction->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-4">
                            <label for="ph_notes" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Catatan PH (opsional):</label>
                            <textarea name="ph_notes" id="ph_notes" rows="3" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('ph_notes', $transaction->ph_notes) }}</textarea>
                            @error('ph_notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end items-center">
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                Kembali
                            </a>
                            {{-- Tombol Download PH PDF --}}
                            <a href="{{ route('transactions.download_ph_pdf', $transaction->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                <i class="fas fa-file-pdf mr-2"></i> Download PH PDF
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Konfirmasi PH Dikirim
                            </button>
                        </div>
                    </form>
>>>>>>> Stashed changes
                </div>
            </div>
        </div>
    </div>
</x-app-layout>