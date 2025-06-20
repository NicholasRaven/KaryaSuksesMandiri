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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>

<body class="font-sans antialiased">
    <div x-data="{ open: true }" class="min-h-screen flex bg-gray-100 dark:bg-gray-900">

        <!-- Sidebar -->
        <div :class="open ? 'translate-x-0' : '-translate-x-full'"
             class="fixed z-30 inset-y-0 left-0 w-64 bg-gray-200 text-black transform transition-transform duration-200 ease-in-out md:relative md:translate-x-0">
            <div class="flex items-center justify-between p-4 bg-white">
                <div class="text-2xl font-bold">KSM</div>
                <button class="md:hidden" @click="open = false">
                    ✕
                </button>
            </div>
            <nav class="mt-4 space-y-4 pl-4 text-lg">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 hover:text-blue-600">
                    <span>Dashboard</span>
                </a>

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

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            @include('layouts.navigation')

            <!-- Sidebar Toggle Button for Mobile -->
            <div class="md:hidden p-4">
                <button @click="open = true" class="text-gray-700 bg-gray-300 px-4 py-2 rounded">
                    ☰ Menu
                </button>
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
