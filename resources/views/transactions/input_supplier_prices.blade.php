<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Form Input Harga Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Pemesanan: {{ $transaction->customer->name }} -
                        {{ \Carbon\Carbon::parse($transaction->order_date)->translatedFormat('d/m/Y') }}
                    </p>

                    @if ($errors->any())
                        <div class="mb-4 text-red-500">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('transactions.store_supplier_prices', $transaction->id) }}" method="POST">
                        @csrf

                        @foreach ($transaction->details as $detail)
                            <div class="mb-8 p-4 border border-gray-300 dark:border-gray-700 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                    {{ $loop->iteration }}. {{ $detail->item_name }} (Jumlah: {{ $detail->quantity }})
                                </h3>
                                @if ($detail->specification_notes)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Catatan Spesifikasi: {{ $detail->specification_notes }}
                                    </p>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="supplier_id_{{ $detail->id }}"
                                               class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                            Supplier
                                        </label>
                                        <select id="supplier_id_{{ $detail->id }}"
                                                name="item_prices[{{ $detail->id }}][supplier_id]"
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full">
                                            <option value="">-- Pilih Supplier --</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ old('item_prices.' . $detail->id . '.supplier_id', $detail->supplierPrices->firstWhere('is_selected', true)->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('item_prices.' . $detail->id . '.supplier_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="price_{{ $detail->id }}"
                                               class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                            Harga Penawaran per Unit
                                        </label>
                                        <input type="number"
                                               id="price_{{ $detail->id }}"
                                               name="item_prices[{{ $detail->id }}][price]"
                                               value="{{ old('item_prices.' . $detail->id . '.price', $detail->supplierPrices->firstWhere('is_selected', true)->price ?? '') }}"
                                               class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                               min="0" step="any" placeholder="Rp">
                                        @error('item_prices.' . $detail->id . '.price')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="notes_{{ $detail->id }}"
                                            class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                            Catatan Supplier (Opsional)
                                        </label>
                                        <input type="text"
                                            id="notes_{{ $detail->id }}"
                                            name="item_prices[{{ $detail->id }}][notes]"
                                            value="{{ old('item_prices.' . $detail->id . '.notes', $detail->supplierPrices->firstWhere('is_selected', true)->notes ?? '') }}"
                                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full dark:text-gray-300"
                                            placeholder="Opsional">
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('transactions.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 mr-4">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Simpan & Lanjutkan Ke PH
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>