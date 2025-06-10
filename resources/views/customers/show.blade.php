<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Nama:</strong> {{ $customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Email:</strong> {{ $customer->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>No Telp:</strong> {{ $customer->phone_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Tipe:</strong> {{ $customer->type }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600 dark:text-gray-400"><strong>Alamat:</strong> {{ $customer->address }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600 dark:text-gray-400"><strong>Dibuat Pada:</strong> {{ $customer->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600 dark:text-gray-400"><strong>Diperbarui Pada:</strong> {{ $customer->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('customers.edit', $customer->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Edit
                        </a>
                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Hapus
                            </button>
                        </form>
                        <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            Kembali ke Daftar Pelanggan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>