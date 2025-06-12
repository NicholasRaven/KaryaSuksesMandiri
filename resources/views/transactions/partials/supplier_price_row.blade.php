
<div class="supplier-price-row grid grid-cols-1 md:grid-cols-5 gap-4 items-center mb-3 p-3 border border-gray-400 rounded">
    {{-- Hidden transaction detail ID --}}
    <input type="hidden" name="item_prices[{{ $detail->id }}][{{ $price_index }}][transaction_detail_id]" value="{{ $detail->id }}">

    {{-- Hidden supplier price ID if it exists --}}
    @if ($supplier_price)
        <input type="hidden" name="item_prices[{{ $detail->id }}][{{ $price_index }}][id]" id="item_prices_{{ $detail->id }}_{{ $price_index }}_id" value="{{ $supplier_price->id }}">
    @endif

    {{-- Supplier Dropdown --}}
    <div>
        <label for="item_prices_{{ $detail->id }}_{{ $price_index }}_supplier_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Supplier:</label>
        <select name="item_prices[{{ $detail->id }}][{{ $price_index }}][supplier_id]" id="item_prices_{{ $detail->id }}_{{ $price_index }}_supplier_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
            <option value="">Pilih Supplier</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ (isset($supplier_price) && $supplier_price->supplier_id == $supplier->id) ? 'selected' : '' }}>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Harga Input --}}
    <div>
        <label for="item_prices_{{ $detail->id }}_{{ $price_index }}_price" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Harga:</label>
        <input type="number" step="0.01" name="item_prices[{{ $detail->id }}][{{ $price_index }}][price]" id="item_prices_{{ $detail->id }}_{{ $price_index }}_price" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" placeholder="Rp" value="{{ isset($supplier_price) ? $supplier_price->price : '' }}" required>
    </div>

    {{-- Catatan Input --}}
    <div class="md:col-span-1">
        <label for="item_prices_{{ $detail->id }}_{{ $price_index }}_notes" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Catatan Tambahan:</label>
        <input type="text" name="item_prices[{{ $detail->id }}][{{ $price_index }}][notes]" id="item_prices_{{ $detail->id }}_{{ $price_index }}_notes" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" value="{{ isset($supplier_price) ? $supplier_price->notes : '' }}">
    </div>

@if (isset($supplier_price))
    {{-- Select Best Price Radio --}}
    <div class="flex items-center justify-center">
        <label for="selected_price_id_{{ $detail->id }}_{{ $price_index }}" class="inline-flex items-center">
            <input type="radio"
                name="selected_prices[{{ $detail->id }}]"
                value="{{ $supplier_price->id }}"
                id="selected_price_id_{{ $detail->id }}_{{ $price_index }}"
                data-price-row-id="{{ $detail->id }}_{{ $price_index }}"
                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                {{ $isSelected ? 'checked' : '' }}>
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Pilih</span>
        </label>
    </div>
@endif


</div>
