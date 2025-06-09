<x-app-layout>
     <div class="flex h-screen">

    <!-- Sidebar -->
    <div :class="open ? 'translate-x-0' : '-translate-x-full'"
         class="fixed z-30 inset-y-0 left-0 w-64 bg-gray-200 text-black transform transition-transform duration-200 ease-in-out md:relative md:translate-x-0">
      <div class="p-4 text-2xl font-bold">KSM</div>
      <nav class="mt-4 space-y-4 pl-4 text-lg">
        <a href="#" class="flex items-center space-x-2 hover:text-blue-600">
          <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center space-x-2 hover:text-blue-600">
          <span>User</span>
        </a>
        <a href="#" class="flex items-center space-x-2 hover:text-blue-600">
          <span>Customer</span>
        </a>
        <a href="#" class="flex items-center space-x-2 hover:text-blue-600">
          <span>Supplier</span>
        </a>
        <a href="#" class="flex items-center space-x-2 hover:text-blue-600">
          <span>Pesanan</span>
        </a>
        <a href="#" class="flex items-center space-x-2 hover:text-blue-600">
          <span>Penjualan & Pembelian</span>
        </a>
      </nav>
    </div>

    <!-- Overlay -->
    <div x-show="open" @click="open = false"
         class="fixed inset-0 bg-black opacity-50 z-20 md:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col w-full ml-0 md:ml-64">
      <!-- Page Content -->
      <main class="flex-1 p-4">
        <!-- Your content here -->
        <p>Welcome to your dashboard.</p>
      </main>
    </div>
  </div>

</x-app-layout>
