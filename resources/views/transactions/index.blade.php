{{-- resources/views/transactions/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sistem Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Sukses!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <form action="{{ route('transactions.index') }}" method="GET" class="flex items-center">
                            <input type="text" name="search" placeholder="Cari Transaksi" value="{{ $search }}" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mr-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">Cari</button>
                        </form>
                        <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Tambah Transaksi
                        </a>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Daftar Transaksi</h3>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">No Transaksi</th>
                                    <th scope="col" class="py-3 px-6">Nama Pelanggan</th>
                                    <th scope="col" class="py-3 px-6">Tanggal Pemesanan</th>
                                    <th scope="col" class="py-3 px-6">Status Proses</th>
                                    <th scope="col" class="py-3 px-6">Status Pembayaran</th>
                                    <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $transaction->transaction_number }}</td>
                                        <td class="py-4 px-6">{{ $transaction->customer->name }}</td>
                                        <td class="py-4 px-6">{{ \Carbon\Carbon::parse($transaction->order_date)->translatedFormat('d M Y') }}</td>
                                        <td class="py-4 px-6">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                @if ($transaction->process_status == 'PO Diterima') bg-blue-100 text-blue-800
                                                @elseif ($transaction->process_status == 'Invoice Dibuat') bg-purple-100 text-purple-800
                                                @elseif ($transaction->process_status == 'PH Dikirim') bg-yellow-100 text-yellow-800
                                                @elseif ($transaction->process_status == 'Selesai') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $transaction->process_status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                @if ($transaction->payment_status == 'Belum Ada Invoice') bg-gray-100 text-gray-800
                                                @elseif ($transaction->payment_status == 'Belum Bayar') bg-red-100 text-red-800
                                                @elseif ($transaction->payment_status == 'Lunas') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $transaction->payment_status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                {{-- Tombol Detail --}}
                                                <a href="{{ route('transactions.show', $transaction->id) }}" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>

                                                {{-- Tombol Aksi Dinamis --}}
                                                @if ($transaction->process_status == 'PO Diterima' && $transaction->details->count() > 0 && $transaction->details->every(fn($detail) => !$detail->final_price_per_unit))
                                                    {{-- Jika PO Diterima dan harga supplier belum diinput atau dipilih --}}
                                                    <a href="{{ route('transactions.input_supplier_prices', $transaction->id) }}" class="inline-flex items-center px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold rounded">
                                                        Input Harga Supplier
                                                    </a>
                                                @elseif ($transaction->process_status == 'PO Diterima' && $transaction->details->every(fn($detail) => $detail->final_price_per_unit))

                                                    <a href="{{ route('transactions.generate_ph', $transaction->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded">
                                                        Buat PH
                                                    </a>
                                                @elseif ($transaction->process_status == 'PH Dikirim' && !$transaction->invoice)
                                                    {{-- Jika PH sudah dikirim, tapi PO belum dikonfirmasi (file belum diupload) --}}
                                                    <a href="{{ route('transactions.confirm_po_received', $transaction->id) }}" class="inline-flex items-center px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded">
                                                        Konfirmasi PO
                                                    </a>

                                                @elseif ($transaction->process_status == 'PO Diterima' && $transaction->payment_status == 'Belum Ada Invoice')
                                                    {{-- Jika PO sudah dikonfirmasi (file ada), dan status pembayaran belum ada invoice --}}
                                                    <a href="{{ route('transactions.create_invoice', $transaction->id) }}" class="inline-flex items-center px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded">
                                                        Buat Invoice
                                                    </a>
                                                @elseif ($transaction->process_status == 'Invoice Dibuat' && $transaction->payment_status == 'Belum Bayar')
                                                    {{-- Jika Invoice sudah dibuat, dan belum bayar --}}
                                                    <form action="{{ route('transactions.edit_payment_status', ['transaction' => $transaction->id, 'type' => 'payment']) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="Lunas">
                                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-purple-500 hover:bg-purple-600 text-white text-xs font-semibold rounded">
                                                            Update Bayar
                                                        </button>
                                                    </form>
                                                @elseif ($transaction->process_status == 'Invoice Dibuat' && $transaction->payment_status == 'Lunas')
                                                    {{-- Jika Invoice sudah dibuat dan Lunas, bisa diselesaikan --}}
                                                    <form action="{{ route('transactions.edit_payment_status', ['transaction' => $transaction->id, 'type' => 'process']) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="Selesai">
                                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-700 hover:bg-green-800 text-white text-xs font-semibold rounded">
                                                            Selesai
                                                        </button>
                                                    </form>
                                                @elseif ($transaction->process_status == 'Selesai')
                                                    <span class="px-3 py-1 bg-gray-200 text-gray-700 text-xs font-semibold rounded">Selesai</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 px-6 text-center text-gray-500 dark:text-gray-400">Tidak ada data transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-gray-700 dark:text-gray-300">
                            Halaman {{ $transactions->currentPage() }} dari {{ $transactions->lastPage() }}
                        </div>
                        <div class="flex items-center">
                            <form action="{{ route('transactions.index') }}" method="GET" class="flex items-center">
                                <label for="per_page" class="mr-2 text-sm text-gray-600 dark:text-gray-400">per halaman</label>
                                <select name="per_page" id="per_page" onchange="this.form.submit()" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                @if ($search)
                                    <input type="hidden" name="search" value="{{ $search }}">
                                @endif
                            </form>
                        </div>
                        <div>
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
