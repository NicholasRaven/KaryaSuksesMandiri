{{-- resources/views/payments/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Pembayaran Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{-- Form Filter dan Pencarian --}}
                <form action="{{ route('payments.index') }}" method="GET" class="mb-6">
                    <div class="flex flex-col md:flex-row items-center gap-4 mb-4">
                        <input type="text" name="search" placeholder="Cari No. Invoice / Pelanggan..." value="{{ $search }}"
                               class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-full md:flex-grow">
                        <select name="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-full md:w-auto">
                            @foreach($paymentStatuses as $pStatus)
                                <option value="{{ $pStatus }}" {{ $status == $pStatus ? 'selected' : '' }}>
                                    {{ $pStatus == 'All' ? 'Semua Status' : $pStatus }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 w-full md:w-auto">
                            Filter
                        </button>
                    </div>
                </form>

                {{-- Notifikasi --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('warning') }}</span>
                    </div>
                @endif

                {{-- Tampilan Card untuk Invoice --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($invoices as $invoice)
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-md p-5 flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Invoice #{{ $invoice->invoice_number }}
                                </h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                    <strong>Pelanggan:</strong> {{ $invoice->transaction->customer->name ?? '-' }}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                    <strong>Tanggal Invoice:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                    <strong>Jatuh Tempo:</strong>
                                    @if($invoice->due_date)
                                        {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                                        @if($invoice->due_date < now()->toDateString() && $invoice->transaction->payment_status != 'Lunas')
                                            <span class="ml-1 text-red-500 text-xs font-semibold">(Jatuh Tempo!)</span>
                                        @elseif($invoice->due_date == now()->toDateString() && $invoice->transaction->payment_status != 'Lunas')
                                            <span class="ml-1 text-orange-500 text-xs font-semibold">(Hari Ini!)</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                                    <strong>Total Tagihan:</strong> Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </p>
                                <p class="mb-4">
                                    <span class="px-3 py-1 text-sm font-bold rounded-full
                                        @if($invoice->transaction->payment_status == 'Belum Ada Invoice') bg-gray-400 text-white
                                        @elseif($invoice->transaction->payment_status == 'Belum Bayar') bg-red-500 text-white
                                        @elseif($invoice->transaction->payment_status == 'Jatuh Tempo') bg-orange-500 text-white
                                        @elseif($invoice->transaction->payment_status == 'Lunas') bg-green-500 text-white
                                        @endif
                                    ">{{ $invoice->transaction->payment_status }}</span>
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-end gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <a href="{{ route('payments.show', $invoice->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 w-full sm:w-auto">
                                    <i class="fas fa-edit mr-2"></i> Detail/Update
                                </a>
                                @if($invoice->transaction->payment_status != 'Lunas' && $invoice->transaction->customer->email) {{-- Hanya tampilkan jika belum lunas dan ada email pelanggan --}}
                                    <form action="{{ route('payments.send_reminder', $invoice->id) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Kirim reminder pembayaran untuk invoice {{ $invoice->invoice_number }}?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-500 text-black border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 w-full">
                                            <i class="fas fa-bell mr-2"></i> Kirim Reminder
                                        </button>
                                    </form>
                                @endif
                                @if($invoice->transaction->payment_status == 'Lunas' && $invoice->payment_proof_file)
                                    <a href="{{ Storage::url(str_replace('storage/', 'public/', $invoice->payment_proof_file)) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 bg-blue-500 text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 w-full sm:w-auto">
                                        <i class="fas fa-download mr-2"></i> Bukti Bayar
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="sm:col-span-2 lg:col-span-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-md p-5 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada invoice pembayaran ditemukan.
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Pastikan Font Awesome CSS terload di app.blade.php atau di sini --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @endpush
</x-app-layout>
