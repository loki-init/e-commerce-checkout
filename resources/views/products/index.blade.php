<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Master - E-Commerce</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800" 
      x-data="{ 
          openAddModal: false, 
          openEditModal: false, 
          productToEdit: { id: '', name: '', price: '', stock: '' },
          setEditProduct(prod) {
              this.productToEdit = { ...prod };
              this.openEditModal = true;
          }
      }">

    <div class="max-w-6xl mx-auto py-10 px-4">
        
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold">Product Master</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your store inventory, pricing, and stock levels.</p>
            </div>
            <div class="flex space-x-3">
                <!-- <a href="/checkout" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition flex items-center">
                    Go to Checkout &rarr;
                </a> -->
                <button @click="openAddModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow">
                    + Add Product
                </button>
            </div>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Full Width Product Listing Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-semibold">All Products</h2>
                <span class="text-sm text-gray-500">Total: {{ count($products) }} items</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider border-b">
                            <th class="p-4">ID</th>
                            <th class="p-4">Product Name</th>
                            <th class="p-4">Price</th>
                            <th class="p-4">Stock Quantity</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $prod)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 text-gray-500 font-mono text-sm">#{{ $prod->id }}</td>
                                <td class="p-4 font-semibold text-gray-900">{{ $prod->name }}</td>
                                <td class="p-4 text-gray-700 font-medium">₹{{ number_format($prod->price, 2) }}</td>
                                <td class="p-4">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $prod->stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $prod->stock_quantity }} units
                                    </span>
                                </td>
                                <td class="p-4 text-center space-x-3">
                                    <!-- Trigger Edit Modal via Alpine.js -->
                                    <button @click="setEditProduct({{ json_encode($prod) }})" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        Edit
                                    </button>
                                    
                                    <form action="{{ route('products.destroy', $prod->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this product?')" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">
                                    No products found in Master. Click the "+ Add Product" button above to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= ADD PRODUCT MODAL ================= -->
        <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4" x-cloak>
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 relative" @click.away="openAddModal = false">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="text-xl font-bold">Add New Product</h3>
                    <button @click="openAddModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>
                
                <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Product Name</label>
                        <input type="text" name="name" required placeholder="e.g. Wireless Headphones" 
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Price (₹)</label>
                        <input type="number" step="0.01" name="price" required placeholder="1500" 
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Stock Quantity</label>
                        <input type="number" name="stock_quantity" required placeholder="10" 
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" @click="openAddModal = false" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">Save Product</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= EDIT PRODUCT MODAL ================= -->
        <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4" x-cloak>
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 relative" @click.away="openEditModal = false">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="text-xl font-bold">Edit Product</h3>
                    <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>
                
                <!-- Dynamic form action updated via Alpine binding :action -->
                <form :action="'/products/' + productToEdit.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Product Name</label>
                        <input type="text" name="name" x-model="productToEdit.name" required 
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Price (₹)</label>
                        <input type="number" step="0.01" name="price" x-model="productToEdit.price" required 
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Stock Quantity</label>
                        <input type="number" name="stock_quantity" x-model="productToEdit.stock_quantity" required 
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">Update Product</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>
</html>