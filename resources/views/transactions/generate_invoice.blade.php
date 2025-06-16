{{-- resources/views/transactions/generate_invoice.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Buat Invoice untuk Transaksi #{{ $transaction->transaction_number }}</h3>

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
                    @if(session('info'))
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('info') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Whoops!</strong>
                            <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                            <ul class="mt-3 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('transactions.store_invoice', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="invoice_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nomor Invoice:</label>
                                <input type="text" name="invoice_number" id="invoice_number" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('invoice_number') border-red-500 @enderror" value="{{ old('invoice_number', 'INV-' . date('Ymd') . '-' . Str::random(4)) }}" required>
                                @error('invoice_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="invoice_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Invoice:</label>
                                <input type="date" name="invoice_date" id="invoice_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('invoice_date') border-red-500 @enderror" value="{{ old('invoice_date', now()->toDateString()) }}" required>
                                @error('invoice_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="due_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Jatuh Tempo:</label>
                                <input type="date" name="due_date" id="due_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('due_date') border-red-500 @enderror" value="{{ old('due_date', now()->addDays(30)->toDateString()) }}" required> {{-- Default 30 hari --}}
                                @error('due_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="subtotal_display" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Subtotal Pesanan:</label>
                            <input type="text" id="subtotal_display" value="Rp {{ number_format($subtotal, 0, ',', '.') }}" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full" readonly>
                            <input type="hidden" name="subtotal_calculated" id="subtotal_calculated" value="{{ $subtotal }}">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="tax_percentage" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Pajak (%):</label>
                                <input type="number" name="tax_percentage" id="tax_percentage" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('tax_percentage', $defaultTaxPercentage ?? 0) }}" step="0.01" min="0">
                                @error('tax_percentage')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="other_costs" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Biaya Lain-lain:</label>
                                <input type="number" name="other_costs" id="other_costs" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('other_costs', $defaultOtherCosts ?? 0) }}" step="0.01" min="0">
                                @error('other_costs')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="total_amount_display" class="block font-medium text-sm text-gray-700 dark:text-gray-300">TOTAL INVOICE:</label>
                            <input type="text" id="total_amount_display" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full font-bold text-lg" readonly>
                        </div>

                        {{-- PO File yang sudah diupload (jika ada dari tahap sebelumnya) --}}
                        @if($transaction->invoice && $transaction->invoice->po_file)
                            <div class="mb-4">
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>File PO Terlampir:</strong> <a href="{{ Storage::url(str_replace('storage/', 'public/', $transaction->invoice->po_file)) }}" target="_blank" class="text-blue-500 hover:underline">{{ basename($transaction->invoice->po_file) }}</a></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">File PO yang sudah diunggah pada tahap Konfirmasi PO akan otomatis dilampirkan ke Invoice.</p>
                            </div>
                        @else
                            <div class="mb-4">
                                <label for="po_file_manual" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Upload File PO (Opsional, jika belum diunggah):</label>
                                <input type="file" name="po_file" id="po_file_manual" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">
                                @error('po_file')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif


                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('transactions.show', $transaction->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Simpan Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subtotalCalculated = parseFloat(document.getElementById('subtotal_calculated').value);
            const taxPercentageInput = document.getElementById('tax_percentage');
            const otherCostsInput = document.getElementById('other_costs');
            const totalAmountDisplay = document.getElementById('total_amount_display');

            function calculateTotal() {
                let taxPercentage = parseFloat(taxPercentageInput.value) || 0;
                let otherCosts = parseFloat(otherCostsInput.value) || 0;

                let taxAmount = (taxPercentage / 100) * subtotalCalculated;
                let totalAmount = subtotalCalculated + taxAmount + otherCosts;

                totalAmountDisplay.value = 'Rp ' + totalAmount.toLocaleString('id-ID'); // Format ke Rupiah
            }

            taxPercentageInput.addEventListener('input', calculateTotal);
            otherCostsInput.addEventListener('input', calculateTotal);

            // Initial calculation on load
            calculateTotal();
        });
    </script>
    @endpush
</x-app-layout>
