{{-- resources/views/transactions/edit_payment_status.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Update Status Pembayaran Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Update Pembayaran untuk Invoice #{{ $transaction->invoice->invoice_number ?? 'N/A' }}
                    <span class="ml-4 px-3 py-1 text-sm font-bold rounded-full
                        @if($transaction->payment_status == 'Belum Ada Invoice') bg-gray-400 text-white
                        @elseif($transaction->payment_status == 'Belum Bayar') bg-red-500 text-white
                        @elseif($transaction->payment_status == 'Jatuh Tempo') bg-orange-500 text-white
                        @elseif($transaction->payment_status == 'Lunas') bg-green-500 text-white
                        @endif
                    ">{{ $transaction->payment_status }}</span>
                </h3>

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

                <form action="{{ route('transactions.update_payment_status', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="payment_status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Status Pembayaran:</label>
                        <select name="payment_status" id="payment_status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                            <option value="Belum Bayar" {{ old('payment_status', $transaction->payment_status) == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="Jatuh Tempo" {{ old('payment_status', $transaction->payment_status) == 'Jatuh Tempo' ? 'selected' : '' }}>Jatuh Tempo</option>
                            <option value="Lunas" {{ old('payment_status', $transaction->payment_status) == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            {{-- 'Belum Ada Invoice' tidak muncul di sini karena ini form update --}}
                        </select>
                        @error('payment_status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="payment_received_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Pembayaran Diterima:</label>
                        <input type="date" name="payment_received_date" id="payment_received_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                            value="{{ old('payment_received_date', $transaction->invoice->payment_received_date ? \Carbon\Carbon::parse($transaction->invoice->payment_received_date)->format('Y-m-d') : '') }}">
                        @error('payment_received_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="payment_method" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Metode Pembayaran:</label>
                        <input type="text" name="payment_method" id="payment_method" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                            value="{{ old('payment_method', $transaction->invoice->payment_method) }}">
                        @error('payment_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="payment_proof_file" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Bukti Pembayaran (PDF/Gambar):</label>
                        <input type="file" name="payment_proof_file" id="payment_proof_file" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                        @if($transaction->invoice->payment_proof_file)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">File saat ini: <a href="{{ Storage::url(str_replace('storage/', 'public/', $transaction->invoice->payment_proof_file)) }}" target="_blank" class="text-blue-500 hover:underline">Lihat Bukti</a></p>
                        @endif
                        @error('payment_proof_file')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                            Kembali ke Daftar Pembayaran
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Update Status Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @endpush
</x-app-layout>
