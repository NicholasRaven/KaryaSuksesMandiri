{{-- resources/views/items/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Pesan Sukses --}}
                    @if (session('success'))
                        <div class="bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg relative mb-4" role="alert">
                            <strong class="font-bold">Sukses!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
                                <i class="fas fa-times-circle"></i>
                            </span>
                        </div>
                    @endif

                    {{-- Pesan Error --}}
                    @if (session('error'))
                        <div class="bg-red-100 dark:bg-red-700 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded-lg relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
                                <i class="fas fa-times-circle"></i>
                            </span>
                        </div>
                    @endif

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                        <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mb-3 md:mb-0">
                            <i class="fas fa-plus mr-2"></i> Tambah Barang
                        </a>
                        <form action="{{ route('items.index') }}" method="GET" class="flex items-center w-full md:w-auto">
                            <input type="text" name="search" placeholder="Cari Barang..." value="{{ $search ?? '' }}" class="flex-grow border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mr-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-search mr-2"></i> Cari
                            </button>
                        </form>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Daftar Barang</h3>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">No</th>
                                    <th scope="col" class="py-3 px-6">Nama Barang</th>
                                    <th scope="col" class="py-3 px-6">Unit</th>
                                    <th scope="col" class="py-3 px-6">Supplier</th>
                                    <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                                        <td class="py-4 px-6">{{ $item->name }}</td>
                                        <td class="py-4 px-6">{{ $item->unit_type }}</td>
                                        <td class="py-4 px-6">{{ $item->supplier->name ?? '-' }}</td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('items.edit', $item->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline inline-flex items-center">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                                <a href="{{ route('items.show', $item->id) }}" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline inline-flex items-center">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </a>
                                                {{-- Tombol Hapus dengan form terpisah --}}
                                                <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline inline-flex items-center">
                                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 px-6 text-center text-gray-500 dark:text-gray-400">Tidak ada data barang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col md:flex-row justify-between items-center">
                        <div class="text-gray-700 dark:text-gray-300 mb-3 md:mb-0">
                            Halaman {{ $items->currentPage() }} dari {{ $items->lastPage() }} (Total {{ $items->total() }} data)
                        </div>
                        <div class="flex items-center space-x-4">
                            <form action="{{ route('items.index') }}" method="GET" class="flex items-center">
                                <label for="per_page" class="mr-2 text-sm text-gray-600 dark:text-gray-400">Tampilkan:</label>
                                <select name="per_page" id="per_page" onchange="this.form.submit()" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                @if ($search)
                                    <input type="hidden" name="search" value="{{ $search ?? '' }}">
                                @endif
                            </form>
                            {{ $items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-eZq/dZtLzC5G3tP5x5X5g5t5J5t5V5t5U5t5Q5t5Y5t5F5t5G5t5H5t5I5t5J5t5K5t5L5t5M5t5N5t5O5t5P5t5Q5t5R5t5S5t5T5t5U5t5V5t5W5t5X5t5Y5t5Z5" crossorigin="anonymous">
    @endpush
</x-app-layout>