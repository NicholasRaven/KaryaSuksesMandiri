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

                    <form action="{{ route('transactions.store_invoice', $transaction->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="subtotal_calculated" id="subtotal_calculated" value="{{ $subtotal }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="invoice_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nomor Invoice <span class="text-red-500">*</span></label>
                                <input type="text" name="invoice_number" id="invoice_number" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('invoice_number', $invoice->invoice_number ?? 'INV-' . $transaction->transaction_number) }}" required>
                            </div>
                            <div>
                                <label for="invoice_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Invoice <span class="text-red-500">*</span></label>
                                <input type="date" name="invoice_date" id="invoice_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('invoice_date', $invoice->invoice_date ?? date('Y-m-d')) }}" required>
                            </div>
                            <div>
<<<<<<< Updated upstream
                                <label for="due_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Jatuh Tempo:</label>
                                <input type="date" name="due_date" id="due_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('due_date') border-red-500 @enderror" value="{{ old('due_date', $invoice ? $invoice->due_date : '') }}">
                                @error('due_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
=======
                                <label for="due_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Jatuh Tempo</label>
                                <input type="date" name="due_date" id="due_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('due_date', $invoice->due_date) }}">
>>>>>>> Stashed changes
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Jatuh Tempo:</label>
                                <input type="text" id="display_due_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full" readonly>
                            </div>
                        </div>

                        <h4 class="text-md font-semibold mb-3">Detail Barang untuk Invoice</h4>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border dark:border-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Barang</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Spesifikasi/Catatan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kuantitas</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga Final Per Unit</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                                    </tr>
                                </thead>
<<<<<<< Updated upstream
                                <tbody>
                                    @foreach ($transaction->details as $detail)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="py-4 px-6">{{ $detail->item_name }}</td>
                                            <td class="py-4 px-6">{{ $detail->quantity }} pcs</td>
                                            <td class="py-4 px-6">Rp {{ number_format($detail->final_price_per_unit ?? 0, 0, ',', '.') }}</td>
                                            <td class="py-4 px-6">Rp {{ number_format(($detail->final_price_per_unit ?? 0) * $detail->quantity, 0, ',', '.') }}</td>
=======
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($transaction->details as $detail)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $detail->item_name ?? ($detail->item->name ?? '-') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $detail->specification_notes ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $detail->quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($detail->final_price_per_unit ?? 0, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format(($detail->final_price_per_unit ?? 0) * $detail->quantity, 0, ',', '.') }}</td>
>>>>>>> Stashed changes
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <td colspan="4" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"><strong>Subtotal Barang:</strong></td>
                                        <td class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" id="display_subtotal_items"><strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="tax_percentage" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Pajak (%)</label>
                                <input type="number" name="tax_percentage" id="tax_percentage" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('tax_percentage', $invoice->tax_percentage ?? 0) }}" min="0" max="100" step="0.01">
                            </div>
                            <div>
                                <label for="other_costs" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Biaya Lain-lain</label>
                                <input type="number" name="other_costs" id="other_costs" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('other_costs', $invoice->other_costs ?? 0) }}" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-md">
                            <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300">Ringkasan Invoice:</h4>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">Total Akhir: <span id="display_total_amount">Rp {{ number_format($invoice->total_amount ?? $subtotal, 0, ',', '.') }}</span></p>
                        </div>


                        <div class="flex justify-end items-center">
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                Kembali ke Dashboard
                            </a>
                            {{-- Tombol Download Invoice PDF --}}
                            @if($invoice->invoice_number && $invoice->total_amount > 0)
                            <a href="{{ route('transactions.download_invoice_pdf', $transaction->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                <i class="fas fa-file-pdf mr-2"></i> Download Invoice PDF
                            </a>
                            @endif
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
<<<<<<< Updated upstream
        const subtotal = {{ $subtotal }};

        function formatRupiah(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function calculateTotal() {
            let taxPercentage = parseFloat(document.getElementById('tax_percentage').value) || 0;
            let otherCosts = parseFloat(document.getElementById('other_costs').value) || 0;

            let taxAmount = (taxPercentage / 100) * subtotal;
            let totalAmount = subtotal + taxAmount + otherCosts;

            document.getElementById('total_amount_display').value = formatRupiah(totalAmount);
        }

=======
>>>>>>> Stashed changes
        document.addEventListener('DOMContentLoaded', function() {
            const subtotalItems = parseFloat(document.getElementById('subtotal_calculated').value);
            const taxPercentageInput = document.getElementById('tax_percentage');
            const otherCostsInput = document.getElementById('other_costs');
            const displayTotalAmount = document.getElementById('display_total_amount');

            function calculateTotal() {
                let taxPercentage = parseFloat(taxPercentageInput.value) || 0;
                let otherCosts = parseFloat(otherCostsInput.value) || 0;

                let taxAmount = (taxPercentage / 100) * subtotalItems;
                let totalAmount = subtotalItems + taxAmount + otherCosts;

                displayTotalAmount.textContent = 'Rp ' + totalAmount.toLocaleString('id-ID'); // Format ke Rupiah
            }

            taxPercentageInput.addEventListener('input', calculateTotal);
            otherCostsInput.addEventListener('input', calculateTotal);

            // Initial calculation
            calculateTotal();
        });
    </script>
    @endpush
</x-app-layout>