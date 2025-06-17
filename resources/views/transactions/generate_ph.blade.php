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
                    <h3 class="text-center text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">PENAWARAN HARGA</h3>
                    <h4 class="text-center text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $transaction->customer->name ?? 'Nama Pelanggan' }}</h4>
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400 mb-6">Tanggal : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>

                    <p class="mb-4 text-gray-700 dark:text-gray-300">Berikut ini kami berikan penawaran harga dengan rincian sebagai berikut:</p>

                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No.</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Barang</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Spesifikasi/Catatan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kuantitas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga Per Unit</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @php $i = 1; @endphp
                                @foreach($transaction->details as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $i++ }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->item_name ?? ($detail->item->name ?? '-') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->specification_notes ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($detail->final_price_per_unit ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format(($detail->final_price_per_unit ?? 0) * $detail->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-100 dark:bg-gray-900">
                                    <td colspan="5" class="px-6 py-4 text-right font-bold text-gray-900 dark:text-gray-100">Subtotal PH:</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($phSubtotal, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <form action="{{ route('transactions.confirm_ph_sent', $transaction->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="ph_notes" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Catatan Penawaran Harga (Opsional):</label>
                            <textarea name="ph_notes" id="ph_notes" rows="3" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('ph_notes', $transaction->ph_notes) }}</textarea>
                            @error('ph_notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('transactions.show', $transaction->id) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
