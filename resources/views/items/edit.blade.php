<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('items.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Barang:</label>
                            <input type="text" name="name" id="name" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('name') border-red-500 @enderror" value="{{ old('name', $item->name) }}" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="unit_type" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Unit:</label>
                            <select name="unit_type" id="unit_type" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('unit_type') border-red-500 @enderror" required>
                                <option value="">Pilih Unit</option>
                                {{-- Menggunakan unit_type untuk old() dan $item --}}
                                <option value="Pcs" {{ old('unit_type', $item->unit_type) == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                <option value="Set" {{ old('unit_type', $item->unit_type) == 'Set' ? 'selected' : '' }}>Set</option>
                                <option value="m" {{ old('unit_type', $item->unit_type) == 'm' ? 'selected' : '' }}>m</option>
                                <option value="Roll" {{ old('unit_type', $item->unit_type) == 'Roll' ? 'selected' : '' }}>Roll</option>
                                <option value="L" {{ old('unit_type', $item->unit_type) == 'L' ? 'selected' : '' }}>L</option>
                                <option value="kg" {{ old('unit_type', $item->unit_type) == 'kg' ? 'selected' : '' }}>kg</option>
                                <option value="gr" {{ old('unit_type', $item->unit_type) == 'gr' ? 'selected' : '' }}>gr</option>
                                <option value="Box" {{ old('unit_type', $item->unit_type) == 'Box' ? 'selected' : '' }}>Box</option>
                                <option value="Kaleng" {{ old('unit_type', $item->unit_type) == 'Kaleng' ? 'selected' : '' }}>Kaleng</option>
                                <option value="Botol" {{ old('unit_type', $item->unit_type) == 'Botol' ? 'selected' : '' }}>Botol</option>
                                <option value="Paket" {{ old('unit_type', $item->unit_type) == 'Paket' ? 'selected' : '' }}>Paket</option>
                            </select>
                            @error('unit_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="supplier_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Supplier:</label>
                            <select name="supplier_name" id="supplier_name" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full @error('supplier_name') border-red-500 @enderror" required>
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->name }}" {{ old('supplier_name', $item->supplier_name) == $supplier->name ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('items.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>