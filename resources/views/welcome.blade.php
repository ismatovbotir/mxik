<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Cards</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 w-full max-w-6xl p-6">
    
    <!-- Card 1 -->
    <div class="bg-white rounded-2xl shadow p-6 flex flex-col">
      <div class="flex items-center justify-between">
        <span class="text-gray-500 text-sm">Total Products</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 7l9-4 9 4-9 4-9-4zm0 4l9 4 9-4M3 15l9 4 9-4"/>
        </svg>
      </div>
      <div class="mt-4 text-3xl font-bold text-gray-800">12,345</div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white rounded-2xl shadow p-6 flex flex-col">
      <div class="flex items-center justify-between">
        <span class="text-gray-500 text-sm">Total Groups</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v1h20v-1c0-3.33-6.67-5-10-5z"/>
        </svg>
      </div>
      <div class="mt-4 text-3xl font-bold text-gray-800">248</div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white rounded-2xl shadow p-6 flex flex-col">
      <div class="flex items-center justify-between">
        <span class="text-gray-500 text-sm">Items with GTIN</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 6h18v2H3zm0 5h18v2H3zm0 5h18v2H3z"/>
        </svg>
      </div>
      <div class="mt-4 text-3xl font-bold text-gray-800">8,732</div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white rounded-2xl shadow p-6 flex flex-col">
      <div class="flex items-center justify-between">
        <span class="text-gray-500 text-sm">Items with Mark</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 
          5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 
          4.5 2.09C13.09 3.81 14.76 3 16.5 
          3 19.58 3 22 5.42 22 8.5c0 
          3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
      </div>
      <div class="mt-4 text-3xl font-bold text-gray-800">5,102</div>
    </div>

  </div>

</body>
</html>