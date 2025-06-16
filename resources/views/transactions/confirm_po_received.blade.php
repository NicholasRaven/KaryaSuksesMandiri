<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Konfirmasi PO Diterima') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('transactions.store_po_received', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Transaksi: {{ $transaction->transaction_number }}</p>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">Nama Pelanggan: {{ $transaction->customer->name }}</p>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Tanggal PH Dikirim: {{ \Carbon\Carbon::parse($transaction->updated_at)->translatedFormat('d/m/Y') }}</p> {{-- Assuming updated_at changes when PH Dikirim --}}
                        </div>

                        <div class="mb-6">
                            <label for="po_file" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Upload File PO:</label>
                            <input type="file" name="po_file" id="po_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 @error('po_file') border-red-500 @enderror">
                            @error('po_file')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @if ($transaction->invoice && $transaction->invoice->po_file)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">File PO sudah ada: <a href="{{ Storage::url($transaction->invoice->po_file) }}" target="_blank" class="text-blue-500 hover:underline">Lihat File</a></p>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-4 space-x-4">
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                Kembali
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Konfirmasi PO Diterima
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
