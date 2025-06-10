<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Penawaran Harga (PH)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-center text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">PENAWARAN HARGA</h3>
                    <h4 class="text-center text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $transaction->customer->name }}</h4>
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400 mb-6">Tanggal : {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y') }}</p>

                    <p class="mb-4 text-gray-700 dark:text-gray-300">Berikut ini kami berikan penawaran harga dengan rincian sebagai berikut:</p>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg mb-6">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">No</th>
                                    <th scope="col" class="py-3 px-6">Barang</th>
                                    <th scope="col" class="py-3 px-6">Jumlah</th>
                                    <th scope="col" class="py-3 px-6">Harga per Unit</th>
                                    <th scope="col" class="py-3 px-6">Jumlah</th>
                                </tr>
                            </thead>
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
                                    </tr>
                                @endforeach
                                <tr class="bg-white dark:bg-gray-800">
                                    <td colspan="4" class="py-4 px-6 text-right font-bold text-gray-900 dark:text-gray-100">Grand Total:</td>
                                    <td class="py-4 px-6 font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>