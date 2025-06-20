<x-app-layout>
     <div class="flex h-screen">

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


