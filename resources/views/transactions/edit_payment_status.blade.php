{{-- resources/views/transactions/edit_payment_status.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Update Status Pembayaran Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-bold mb-4">Invoice: {{ $transaction->invoice->invoice_number }}</h3>
                    <p class="mb-2"><strong>No Transaksi:</strong> {{ $transaction->transaction_number }}</p>
                    <p class="mb-2"><strong>Pelanggan:</strong> {{ $transaction->customer->name }}</p>
                    <p class="mb-2"><strong>Total Tagihan:</strong> Rp {{ number_format($transaction->invoice->total_amount, 2, ',', '.') }}</p>
                    <p class="mb-4"><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($transaction->invoice->due_date)->translatedFormat('d M Y') }}</p>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Sukses!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <strong class="font-bold">Oops!</strong>
                            <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                            <ul class="mt-3 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('transactions.update_payment_status', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Pembayaran</label>
                            <select name="payment_status" id="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="Belum Bayar" {{ old('payment_status', $transaction->payment_status) == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="Jatuh Tempo" {{ old('payment_status', $transaction->payment_status) == 'Jatuh Tempo' ? 'selected' : '' }}>Jatuh Tempo</option>
                                <option value="Lunas" {{ old('payment_status', $transaction->payment_status) == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="payment_received_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Pembayaran Diterima (Opsional)</label>
                            <input type="date" name="payment_received_date" id="payment_received_date"
                                   value="{{ old('payment_received_date', $transaction->invoice->payment_received_date) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Metode Pembayaran (Opsional)</label>
                            <input type="text" name="payment_method" id="payment_method"
                                   value="{{ old('payment_method', $transaction->invoice->payment_method) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                            <label for="payment_proof_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bukti Pembayaran (Opsional)</label>
                            <input type="file" name="payment_proof_file" id="payment_proof_file"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-gray-200 dark:hover:file:bg-gray-600">
                            @if($transaction->invoice->payment_proof_file)
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">File saat ini: <a href="{{ Storage::url(str_replace('storage/', 'public/', $transaction->invoice->payment_proof_file)) }}" target="_blank" class="text-blue-500 hover:underline">Lihat Bukti Pembayaran</a></p>
                                <div class="flex items-center mt-2">
                                    <input type="checkbox" name="clear_payment_proof" id="clear_payment_proof" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-indigo-400">
                                    <label for="clear_payment_proof" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Hapus bukti pembayaran yang ada</label>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-eZq/dZtLzC5G3tP5x5X5g5t5J5t5V5t5U5t5Q5t5O5t5N5t5M5t5L5t5K5t5J5t5I5t5H5t5G5t5F5t5E5t5D5t5C5t5B5t5A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush
</x-app-layout>