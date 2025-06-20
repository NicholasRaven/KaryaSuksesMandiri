<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-center text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">INVOICE</h3>

                    <form action="{{ route('transactions.store_invoice', $transaction->id) }}" method="POST">
                        <input type="hidden" name="subtotal_calculated" id="subtotal_calculated" value="{{ $subtotal }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="invoice_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">No Invoice:</label>
                                <input type="text" name="invoice_number" id="invoice_number" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('invoice_number') border-red-500 @enderror" value="{{ old('invoice_number', $invoice ? $invoice->invoice_number : '') }}" required>
                                @error('invoice_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                @if ($invoice->invoice_date)
                                <label for="invoice_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Invoice:</label>
                                <input type="date" name="invoice_date" id="invoice_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('invoice_date') border-red-500 @enderror" value="{{ old('invoice_date', $invoice ? $invoice->invoice_date : date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                @error('invoice_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @endif

                            </div>
                            <div>
                                <label for="due_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Jatuh Tempo:</label>
                                <input type="date" name="due_date" id="due_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('due_date') border-red-500 @enderror" value="{{ old('due_date', $invoice ? $invoice->due_date : '') }}" min="{{ old('invoice_date', $invoice->invoice_date) }}" required>
                                @error('due_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg mb-4">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">Nama Barang</th>
                                        <th scope="col" class="py-3 px-6">Jumlah</th>
                                        <th scope="col" class="py-3 px-6">Harga per Unit</th>
                                        <th scope="col" class="py-3 px-6">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->details as $detail)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="py-4 px-6">{{ $detail->item_name }}</td>
                                            <td class="py-4 px-6">{{ $detail->quantity }} pcs</td>
                                            <td class="py-4 px-6">Rp {{ number_format($detail->selectedSupplierPrice->price ?? 0, 0, ',', '.') }}</td>
                                            <td class="py-4 px-6">Rp {{ number_format(($detail->selectedSupplierPrice->price ?? 0) * $detail->quantity, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div></div> {{-- Empty div for alignment --}}
                            <div class="text-right">
                                <div class="mb-2">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Subtotal:</label>
                                    <input type="text" id="subtotal_display" value="Rp {{ number_format($subtotal, 0, ',', '.') }}" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full text-right" readonly>
                                </div>
                                <div class="mb-2">
                                    <label for="tax_percentage" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Pajak (%):</label>
                                    <input type="number" step="0.01" name="tax_percentage" id="tax_percentage" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full text-right @error('tax_percentage') border-red-500 @enderror" value="{{ old('tax_percentage', $invoice ? $invoice->tax_percentage : 0) }}" oninput="calculateTotal()">
                                    @error('tax_percentage')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="other_costs" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Biaya lain-lain (Rp):</label>
                                    <input type="number" step="0.01" name="other_costs" id="other_costs" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full text-right @error('other_costs') border-red-500 @enderror" value="{{ old('other_costs', $invoice ? $invoice->other_costs : 0) }}" oninput="calculateTotal()">
                                    @error('other_costs')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-2">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">TOTAL:</label>
                                    <input type="text" id="total_amount_display" value="Rp {{ number_format($invoice ? $invoice->total_amount : $subtotal, 0, ',', '.') }}" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full text-right font-bold text-lg" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                Kembali
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
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
            document.getElementById('subtotal_calculated').value = subtotal;
        }

        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal(); // Calculate on page load

            const dueDateInput = document.getElementById('due_date');
            const displayDueDateInput = document.getElementById('display_due_date');

            function updateDisplayDueDate() {
                if (dueDateInput.value) {
                    const date = new Date(dueDateInput.value);
                    const options = { day: '2-digit', month: 'long', year: 'numeric' };
                    displayDueDateInput.value = date.toLocaleDateString('id-ID', options);
                } else {
                    displayDueDateInput.value = '';
                }
            }

            dueDateInput.addEventListener('change', updateDisplayDueDate);
            updateDisplayDueDate(); // Initial call
        });
    </script>
    @endpush
</x-app-layout>
