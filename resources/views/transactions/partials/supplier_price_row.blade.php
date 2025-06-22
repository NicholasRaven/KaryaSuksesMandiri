<div class="border p-4 mb-4 rounded shadow-sm">
    <input type="hidden" name="item_prices[{{ $detail_id }}][{{ $index }}][transaction_detail_id]" value="{{ $detail_id }}">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier:</label>
            <select name="item_prices[{{ $detail_id }}][{{ $index }}][supplier_id]" required
                class="w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300">
                <option value="">Pilih Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        {{ isset($supplier_price) && $supplier_price->supplier_id == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga:</label>
            <input type="number" name="item_prices[{{ $detail_id }}][{{ $index }}][price]" required
                value="{{ $supplier_price->price ?? '' }}"
                class="w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300"
                placeholder="Rp">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Tambahan:</label>
            <input type="text" name="item_prices[{{ $detail_id }}][{{ $index }}][notes]"
                value="{{ $supplier_price->notes ?? '' }}"
                class="w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300"
                placeholder="Opsional">
        </div>
    </div>
</div>
