<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="transaction_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">No Transaksi:</label>
                            <input type="text" name="transaction_number" id="transaction_number" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full" value="{{ old('transaction_number', $transaction->transaction_number) }}" readonly>
                            <p class="text-sm text-gray-500 mt-1">Nomor transaksi tidak bisa diubah.</p>
                        </div>
                        <div class="mb-4">
                            <label for="customer_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Pelanggan:</label>
                            <select name="customer_id" id="customer_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('customer_id') border-red-500 @enderror" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $transaction->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="order_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Pemesanan:</label>
                            <input type="date" name="order_date" id="order_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('order_date') border-red-500 @enderror" value="{{ old('order_date', $transaction->order_date) }}" required>
                            @error('order_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="process_status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Status Proses:</label>
                            <select name="process_status" id="process_status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('process_status') border-red-500 @enderror" required>
                                <option value="PO Diterima" {{ old('process_status', $transaction->process_status) == 'PO Diterima' ? 'selected' : '' }}>PO Diterima</option>
                                <option value="Invoice Dibuat" {{ old('process_status', $transaction->process_status) == 'Invoice Dibuat' ? 'selected' : '' }}>Invoice Dibuat</option>
                                <option value="PH Dikirim" {{ old('process_status', $transaction->process_status) == 'PH Dikirim' ? 'selected' : '' }}>PH Dikirim</option>
                                <option value="Selesai" {{ old('process_status', $transaction->process_status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            @error('process_status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="payment_status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Status Pembayaran:</label>
                            <select name="payment_status" id="payment_status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('payment_status') border-red-500 @enderror" required>
                                <option value="Belum Ada Invoice" {{ old('payment_status', $transaction->payment_status) == 'Belum Ada Invoice' ? 'selected' : '' }}>Belum Ada Invoice</option>
                                <option value="Belum Bayar" {{ old('payment_status', $transaction->payment_status) == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="Lunas" {{ old('payment_status', $transaction->payment_status) == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            </select>
                            @error('payment_status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>