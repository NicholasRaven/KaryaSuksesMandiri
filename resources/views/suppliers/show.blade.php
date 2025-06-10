<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Nama Supplier:</strong> {{ $supplier->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Jenis Barang:</strong> {{ $supplier->jenis_barang }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>No Telp:</strong> {{ $supplier->phone_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Email:</strong> {{ $supplier->email }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600 dark:text-gray-400"><strong>Alamat:</strong> {{ $supplier->address }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600 dark:text-gray-400"><strong>Dibuat Pada:</strong> {{ $supplier->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600 dark:text-gray-400"><strong>Diperbarui Pada:</strong> {{ $supplier->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Edit
                        </a>
                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Hapus
                            </button>
                        </form>
                        <a href="{{ route('suppliers.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                            Kembali ke Daftar Supplier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>