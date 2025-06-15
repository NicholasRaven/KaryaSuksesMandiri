{{-- resources/views/transactions/create.blade.php (or create_step1.blade.php) --}}
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
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="order_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Pemesanan:</label>
                                <input type="date" name="order_date" id="order_date" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('order_date') border-red-500 @enderror" value="{{ old('order_date', now()->toDateString()) }}" required>
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
                                <input type="text" name="orderer_name" id="orderer_name" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('orderer_name') }}">
                                @error('orderer_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="orderer_email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email Pemesan:</label>
                                <input type="email" name="orderer_email" id="orderer_email" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('orderer_email') }}">
                                @error('orderer_email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="orderer_phone" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Telepon Pemesan:</label>
                                <input type="text" name="orderer_phone" id="orderer_phone" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ old('orderer_phone') }}">
                                @error('orderer_phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Detail Barang Pesanan</h3>
                        <div id="items_container">
                            {{-- Item rows will be appended here by JavaScript --}}
                        </div>

                        <button type="button" id="add_item_button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-4">
                            Tambah Barang
                        </button>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Lanjutkan ke Input Harga Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let itemIndex = 0;
        const itemsContainer = document.getElementById('items_container');
        const availableItems = @json($items); // Get items data from PHP

        // Function to create a new item row
        function createItemRow(oldItem = null) {
            const div = document.createElement('div');
            div.classList.add('item-row', 'mb-4', 'p-4', 'border', 'border-gray-200', 'dark:border-gray-700', 'rounded-md', 'relative');
            div.setAttribute('data-index', itemIndex);

            div.innerHTML = `
                <button type="button" class="remove-item-button absolute top-2 right-2 text-red-500 hover:text-red-700 text-lg">&times;</button>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="items_${itemIndex}_item_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Pilih Barang (Master Data):</label>
                        <select name="items[${itemIndex}][item_id]" id="items_${itemIndex}_item_id" class="item-id-select border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                            <option value="">Pilih Barang (atau input manual)</option>
                            ${availableItems.map(item => `<option value="${item.id}">${item.name}</option>`).join('')}
                        </select>
                        @error('items.${itemIndex}.item_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="items_${itemIndex}_item_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Barang Manual (jika tidak ada di master):</label>
                        <input type="text" name="items[${itemIndex}][item_name]" id="items_${itemIndex}_item_name" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" placeholder="Input nama barang jika tidak memilih">
                        @error('items.${itemIndex}.item_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="items_${itemIndex}_quantity" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Kuantitas:</label>
                        <input type="number" name="items[${itemIndex}][quantity]" id="items_${itemIndex}_quantity" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('items.${itemIndex}.quantity') border-red-500 @enderror" min="1" required value="${oldItem ? oldItem.quantity : ''}">
                        @error('items.${itemIndex}.quantity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label for="items_${itemIndex}_specification" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Spesifikasi/Catatan Tambahan:</label>
                    <textarea name="items[${itemIndex}][specification]" id="items_${itemIndex}_specification" rows="2" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">${oldItem ? oldItem.specification : ''}</textarea>
                    @error('items.${itemIndex}.specification')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            `;
            itemsContainer.appendChild(div);

            // Set old values if available
            if (oldItem) {
                const itemSelect = div.querySelector(`#items_${itemIndex}_item_id`);
                const itemNameInput = div.querySelector(`#items_${itemIndex}_item_name`);
                const quantityInput = div.querySelector(`#items_${itemIndex}_quantity`);
                const specificationInput = div.querySelector(`#items_${itemIndex}_specification`);

                if (oldItem.item_id) {
                    itemSelect.value = oldItem.item_id;
                    itemNameInput.style.display = 'none'; // Hide manual input if master item selected
                    itemNameInput.required = false;
                } else {
                    itemSelect.value = ''; // Ensure "Pilih Barang" is selected
                    itemNameInput.value = oldItem.item_name;
                    itemNameInput.style.display = 'block'; // Show manual input
                    itemNameInput.required = true;
                }
                quantityInput.value = oldItem.quantity;
                specificationInput.value = oldItem.specification;
            } else {
                 // Initial state for new row
                const itemSelect = div.querySelector(`#items_${itemIndex}_item_id`);
                const itemNameInput = div.querySelector(`#items_${itemIndex}_item_name`);
                itemSelect.value = ''; // Ensure "Pilih Barang" is selected by default
                itemNameInput.style.display = 'block'; // Show manual input by default
                itemNameInput.required = true; // Make it required initially
            }

            // Add event listener for remove button
            div.querySelector('.remove-item-button').addEventListener('click', function() {
                div.remove();
                if (itemsContainer.children.length === 0) {
                    createItemRow(); // Ensure at least one row always exists
                }
            });

            // Add event listener for item selection dropdown
            div.querySelector('.item-id-select').addEventListener('change', function() {
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

            itemIndex++;
        }

        document.getElementById('add_item_button').addEventListener('click', function () {
            createItemRow();
        });

        // Get old items from Laravel's old() helper, if any
        const oldItems = @json(old('items', []));

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
