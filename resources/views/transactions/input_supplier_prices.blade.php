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
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Pemesanan: {{ $transaction->customer->name }} - {{ \Carbon\Carbon::parse($transaction->order_date)->translatedFormat('d/m/Y') }}</p>

                    <form action="{{ route('transactions.store_supplier_prices', $transaction->id) }}" method="POST">
                        @csrf
                        @foreach ($transaction->details as $detailIndex => $detail)
                            <div class="mb-8 p-4 border border-gray-300 dark:border-gray-700 rounded-md">
                                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ $detail->item_name }} ({{ $detail->quantity }} pcs)</h4>
                                <div id="supplier_prices_container_{{ $detail->id }}">
                                    @forelse ($detail->supplierPrices as $priceIndex => $supplierPrice)
                                        @include('transactions.partials.supplier_price_row', [
                                            'detail' => $detail,
                                            'price_index' => $priceIndex,
                                            'supplier_price' => $supplierPrice,
                                            'suppliers' => $suppliers,
                                            'isSelected' => $supplierPrice->is_selected
                                        ])
                                    @empty
                                        @include('transactions.partials.supplier_price_row', [
                                            'detail' => $detail,
                                            'price_index' => 0,
                                            'supplier_price' => null,
                                            'suppliers' => $suppliers,
                                            'isSelected' => false
                                        ])
                                    @endforelse
                                </div>
                                <button type="button" class="add-supplier-button inline-flex items-center px-4 py-2 mt-4 bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold rounded" data-detail-id="{{ $detail->id }}" data-detail-name="{{ $detail->item_name }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Alternatif Supplier
                                </button>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Simpan & Lanjutkan Ke PH
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
