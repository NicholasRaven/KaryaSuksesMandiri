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
                                    {{ $pStatus }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full md:w-auto">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>
                        <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150 w-full md:w-auto">
                            <i class="fas fa-sync-alt mr-2"></i> Reset
                        </a>
                    </div>
                </form>

                {{-- Notifikasi --}}
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

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($invoices as $invoice)
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-md p-5 flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Invoice: {{ $invoice->invoice_number }}</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Pelanggan: {{ $invoice->transaction->customer->name }}</p>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Tanggal Terbit: {{ \Carbon\Carbon::parse($invoice->issue_date)->translatedFormat('d M Y') }}</p>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Jatuh Tempo: {{ \Carbon\Carbon::parse($invoice->due_date)->translatedFormat('d M Y') }}</p>
                                <p class="text-gray-600 dark:text-gray-300 text-sm font-bold">Total: Rp {{ number_format($invoice->total_amount, 2, ',', '.') }}</p>
                                <p class="text-sm font-semibold mt-2">
                                    Status Pembayaran:
                                    <span class="
                                        @if($invoice->transaction->payment_status == 'Lunas') text-green-600
                                        @elseif($invoice->transaction->payment_status == 'Jatuh Tempo') text-red-600
                                        @elseif($invoice->transaction->payment_status == 'Belum Bayar') text-yellow-600
                                        @else text-gray-500
                                        @endif
                                    ">
                                        {{ $invoice->transaction->payment_status }}
                                    </span>
                                </p>

                                @if($invoice->payment_received_date)
                                    <p class="text-gray-600 dark:text-gray-300 text-sm">Tanggal Bayar: {{ \Carbon\Carbon::parse($invoice->payment_received_date)->translatedFormat('d M Y') }}</p>
                                @endif
                                @if($invoice->payment_method)
                                    <p class="text-gray-600 dark:text-gray-300 text-sm">Metode Pembayaran: {{ $invoice->payment_method }}</p>
                                @endif
                                @if($invoice->reminder_sent_at)
                                    <p class="text-gray-600 dark:text-gray-300 text-sm">Reminder Terakhir: {{ \Carbon\Carbon::parse($invoice->reminder_sent_at)->translatedFormat('d M Y H:i') }}</p>
                                @endif
                            </div>

                            <div class="mt-4 flex flex-col sm:flex-row gap-2">
                                {{-- Link to mark as paid (redirects to edit_payment_status) --}}
                                @if($invoice->transaction->payment_status !== 'Lunas')
                                    <a href="{{ route('transactions.edit_payment_status', $invoice->transaction->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 w-full sm:w-auto">
                                        <i class="fas fa-check-circle mr-2"></i> Tandai Lunas
                                    </a>
                                    {{-- Form to send reminder --}}
                                    <form action="{{ route('payments.send_reminder', $invoice->id) }}" method="POST" class="w-full sm:w-auto">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-500 text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 w-full">
                                            <i class="fas fa-bell mr-2"></i> Kirim Reminder
                                        </button>
                                    </form>
                                @endif

                                @if($invoice->payment_proof_file)
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-eZq/dZtLzC5G3tP5x5X5g5t5J5t5V5t5U5t5Q5t5O5t5N5t5M5t5L5t5K5t5J5t5I5t5H5t5G5t5F5t5E5t5D5t5C5t5B5t5A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush
</x-app-layout>