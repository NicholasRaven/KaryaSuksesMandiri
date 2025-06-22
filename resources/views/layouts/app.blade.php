<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <div class="flex flex-1">
            {{-- Sidebar --}}
            <div x-data="{ open: false }" :class="open ? 'translate-x-0' : '-translate-x-full'"
                class="fixed z-30 inset-y-0 left-0 w-64 h-screen overflow-y-auto bg-red-700 dark:bg-red-900 text-white transform transition-transform duration-200 ease-in-out md:relative md:translate-x-0">
                <!-- <div class="p-4 text-2xl font-bold border-b border-red-800 dark:border-red-600">
                    <i class="fas fa-cubes mr-2"></i> KSM
                </div> -->
                <nav class="mt-4 space-y-2 px-4 text-lg">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 py-2 px-3 rounded-md hover:bg-red-600 dark:hover:bg-red-800 hover:text-white transition">
                        <i class="fas fa-tachometer-alt fa-fw"></i> <span>Dashboard</span>
                    </a>
                    @if (Auth::check() && Auth::user()->UserRole == 'Pimpinan')
                        <a href="{{ route('users.index') }}" class="flex items-center space-x-3 py-2 px-3 rounded-md hover:bg-red-600 dark:hover:bg-red-800 hover:text-white transition">
                            <i class="fas fa-users-cog fa-fw"></i> <span>Manajemen User</span>
                        </a>
                    @endif
                    <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 py-2 px-3 rounded-md {{ request()->routeIs('customers.*') ? 'bg-red-600 dark:bg-red-800 text-white font-semibold shadow' : 'hover:bg-red-600 dark:hover:bg-red-800 hover:text-white' }} transition">
                        <i class="fas fa-users fa-fw"></i> <span>Pelanggan</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="flex items-center space-x-3 py-2 px-3 rounded-md {{ request()->routeIs('suppliers.*') ? 'bg-red-600 dark:bg-red-800 text-white font-semibold shadow' : 'hover:bg-red-600 dark:hover:bg-red-800 hover:text-white' }} transition">
                        <i class="fas fa-truck fa-fw"></i> <span>Supplier</span>
                    </a>
                    <a href="{{ route('items.index') }}" class="flex items-center space-x-3 py-2 px-3 rounded-md {{ request()->routeIs('items.*') ? 'bg-red-600 dark:bg-red-800 text-white font-semibold shadow' : 'hover:bg-red-600 dark:hover:bg-red-800 hover:text-white' }} transition">
                        <i class="fas fa-box-open fa-fw"></i> <span>Barang</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" class="flex items-center space-x-3 py-2 px-3 rounded-md {{ request()->routeIs('transactions.*') ? 'bg-red-600 dark:bg-red-800 text-white font-semibold shadow' : 'hover:bg-red-600 dark:hover:bg-red-800 hover:text-white' }} transition">
                        <i class="fas fa-exchange-alt fa-fw"></i> <span>Transaksi</span>
                    </a>
                    <a href="{{ route('payments.index') }}" class="flex items-center space-x-3 py-2 px-3 rounded-md {{ request()->routeIs('payments.*') ? 'bg-red-600 dark:bg-red-800 text-white font-semibold shadow' : 'hover:bg-red-600 dark:hover:bg-red-800 hover:text-white' }} transition">
                        <i class="fas fa-money-check-alt fa-fw"></i> <span>Pembayaran</span>
                    </a>
                </nav>
            </div>

            {{-- Main content --}}
            <div class="flex-1 md:ml-64 p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
