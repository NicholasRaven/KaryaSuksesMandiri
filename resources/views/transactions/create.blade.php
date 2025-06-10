{{-- resources/views/transactions/create_step1.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Transaksi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="customer_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Perusahaan:</label>
                                <select name="customer_id" id="customer_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('customer_id') border-red-500 @enderror" required>
                                    <option value="">Pilih Pelanggan</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="order_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Pemesanan:</label>
                                <input type="date" name="order_date" id="order_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('order_date') border-red-500 @enderror" value="{{ old('order_date', date('Y-m-d')) }}" required>
                                @error('order_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="delivery_address" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Alamat Pengiriman:</label>
                            <textarea name="delivery_address" id="delivery_address" rows="3" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('delivery_address') border-red-500 @enderror">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="orderer_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Pemesan:</label>
                                <input type="text" name="orderer_name" id="orderer_name" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('orderer_name') border-red-500 @enderror" value="{{ old('orderer_name') }}">
                                @error('orderer_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="orderer_email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email Pemesan:</label>
                                <input type="email" name="orderer_email" id="orderer_email" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('orderer_email') border-red-500 @enderror" value="{{ old('orderer_email') }}">
                                @error('orderer_email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="orderer_phone" class="block font-medium text-sm text-gray-700 dark:text-gray-300">No Hp Pemesan:</label>
                                <input type="text" name="orderer_phone" id="orderer_phone" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('orderer_phone') border-red-500 @enderror" value="{{ old('orderer_phone') }}">
                                @error('orderer_phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Barang Dipesan</h3>
                        <div id="items_container">
                            <!-- Item rows will be dynamically added here by JavaScript -->
                        </div>

                        <button type="button" id="add_item_button" class="inline-flex items-center px-4 py-2 mt-4 bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold rounded">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Barang
                        </button>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Simpan & Lanjut Input Harga Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script type="text/javascript">
        let itemIndex = 0; // Initialize item index to 0

        // Pass master items and old items as JSON
        const masterItems = @json($items->mapWithKeys(fn($item) => [$item->id => $item->name]));
        const oldItems = @json(old('items') ?? []);

        // Hidden template for new item rows
        const itemRowTemplate = `
            <div class="item-row mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        {{-- Label "Nama Barang:" dihapus sesuai permintaan --}}
                        <select name="items[INDEX][item_id]" id="items_INDEX_item_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"></select>
                        <input type="text" name="items[INDEX][item_name]" id="items_INDEX_item_name" placeholder="Nama Barang" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                    </div>
                    <div>
                        <label for="items_INDEX_quantity" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Jumlah:</label>
                        <input type="number" name="items[INDEX][quantity]" id="items_INDEX_quantity" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="1" min="1" required>
                    </div>
                    <div>
                        <label for="items_INDEX_specification" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Spesifikasi/Catatan:</label>
                        <input type="text" name="items[INDEX][specification]" id="items_INDEX_specification" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="remove-item-button inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        `;

        function createItemRow(itemData = {}) {
            let container = document.getElementById('items_container');
            let newItemRow = document.createElement('div');
            newItemRow.innerHTML = itemRowTemplate.replace(/INDEX/g, itemIndex);
            newItemRow = newItemRow.firstElementChild; // Get the actual div element

            const selectElement = newItemRow.querySelector(`[name="items[${itemIndex}][item_id]"]`);
            const itemNameInput = newItemRow.querySelector(`[name="items[${itemIndex}][item_name]"]`);
            const quantityInput = newItemRow.querySelector(`[name="items[${itemIndex}][quantity]"]`);
            const specificationInput = newItemRow.querySelector(`[name="items[${itemIndex}][specification]"]`);

            // Populate the select options
            let selectOptions = '<option value="">Pilih Barang</option>'; // Teks diubah di sini
            for (const id in masterItems) {
                selectOptions += `<option value="${id}">${masterItems[id]}</option>`;
            }
            selectElement.innerHTML = selectOptions;

            // Set values if itemData is provided (for old input)
            if (itemData.item_id) {
                selectElement.value = itemData.item_id;
                itemNameInput.value = masterItems[itemData.item_id]; // Set manual input value to selected item name
                itemNameInput.style.display = 'none'; // Hide manual input
                itemNameInput.required = false; // Not required if dropdown selected
            } else if (itemData.item_name) {
                itemNameInput.value = itemData.item_name;
                selectElement.value = ''; // Ensure dropdown is reset
                itemNameInput.style.display = 'block'; // Show manual input
                itemNameInput.required = true; // Required if dropdown not selected
            } else {
                selectElement.value = ''; // Default to "Pilih Barang"
                itemNameInput.value = '';
                itemNameInput.style.display = 'block';
                itemNameInput.required = true;
            }

            if (itemData.quantity) {
                quantityInput.value = itemData.quantity;
            }
            if (itemData.specification) {
                specificationInput.value = itemData.specification;
            }

            // Attach event listeners for the new row
            attachRemoveListeners(newItemRow);
            attachDropdownListeners(newItemRow);

            container.appendChild(newItemRow);
            itemIndex++;
        }

        function attachRemoveListeners(element) {
            const button = element.querySelector('.remove-item-button');
            if (button) {
                button.onclick = function() {
                    this.closest('.item-row').remove();
                };
            }
        }

        function attachDropdownListeners(containerElement) {
            const selectElement = containerElement.querySelector('select[name^="items["][id$="_item_id"]');
            if (selectElement) {
                selectElement.addEventListener('change', function() {
                    const index = this.id.split('_')[1];
                    const itemNameInput = document.getElementById(`items_${index}_item_name`);
                    if (this.value) { // If an item is selected from dropdown
                        itemNameInput.value = this.options[this.selectedIndex].text; // Set manual input value to selected item name
                        itemNameInput.style.display = 'none'; // Hide manual input
                        itemNameInput.required = false; // Not required if dropdown selected
                    } else { // If 'Pilih Barang' is selected (value is empty)
                        itemNameInput.value = ''; // Clear manual input
                        itemNameInput.style.display = 'block'; // Show manual input
                        itemNameInput.required = true; // Required if dropdown not selected
                    }
                });
            }
        }

        document.getElementById('add_item_button').addEventListener('click', function () {
            createItemRow();
        });

        // On page load, populate with old items or add one empty row if no old items
        document.addEventListener('DOMContentLoaded', function() {
            if (oldItems.length > 0) {
                oldItems.forEach(item => {
                    createItemRow(item);
                });
            } else {
                createItemRow(); // Add one empty row initially if no old input
            }
        });
    </script>
    @endpush
</x-app-layout>
