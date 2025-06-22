<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>Selamat datang di dashboard Anda.</p>
                    <p class="mt-4">Di sini Anda bisa melihat ringkasan penting atau statistik aplikasi.</p>

                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="bg-red-100 dark:bg-red-700 p-4 rounded-lg shadow-md flex items-center space-x-4">
                            <div class="text-red-600 dark:text-red-200 text-3xl">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-red-800 dark:text-red-100">Total Pelanggan</h4>
                                <p class="text-red-700 dark:text-red-200">120</p>
                            </div>
                        </div>
                        <div class="bg-red-200 dark:bg-red-600 p-4 rounded-lg shadow-md flex items-center space-x-4">
                            <div class="text-red-600 dark:text-red-100 text-3xl">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-red-800 dark:text-red-100">Total Transaksi</h4>
                                <p class="text-red-700 dark:text-red-200">55</p>
                            </div>
                        </div>
                        <div class="bg-red-300 dark:bg-red-500 p-4 rounded-lg shadow-md flex items-center space-x-4">
                            <div class="text-red-800 dark:text-red-100 text-3xl">
                                <i class="fas fa-money-check-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-red-900 dark:text-red-100">Invoice Belum Bayar</h4>
                                <p class="text-red-800 dark:text-red-200">7</p>
                            </div>
                        </div>
                    </div>

        @if (Auth::check() && Auth::user()->UserRole == 'SuperAdmin')
        <a href="{{ route('register') }}" class="flex items-center space-x-2 hover:text-blue-600">
          <span>User</span>
        </a>
        @endif

        <a href="{{ route('customers.index') }}" class="flex items-center space-x-2 hover:text-blue-600">
          <span>Pelanggan</span>
        </a>
        <a href="{{ route('suppliers.index') }}" class="flex items-center space-x-2 hover:text-blue-600">
          <span>Supplier</span>
        </a>
        <a href="{{ route('transactions.index') }}" class="flex items-center space-x-2 hover:text-blue-600">
          <span>Sistem Transaksi</span>
        </a>
        <a href="{{ route('payments.index') }}" class="flex items-center space-x-2 hover:text-blue-600">
          <span>Pembayaran</span>
        </a>
      </nav>
    </div>
</x-app-layout>
