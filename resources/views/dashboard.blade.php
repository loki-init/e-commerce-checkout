<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - E-Commerce Store</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Razorpay Checkout Script -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="bg-gray-50 text-gray-800" 
      x-data="{ 
          cart: [], 
          openCart: false, 
          openCheckout: false,
          addToCart(product) {
              let qtyInput = document.getElementById('qty-' + product.id);
              let qty = parseInt(qtyInput ? qtyInput.value : 1);
              
              if(qty > product.stock_quantity) {
                  alert('Not enough stock available!');
                  return;
              }

              let existing = this.cart.find(item => item.id === product.id);
              if (existing) {
                  existing.quantity += qty;
              } else {
                  this.cart.push({ ...product, quantity: qty });
              }
              this.openCart = true;
          },
          removeFromCart(index) {
              this.cart.splice(index, 1);
          },
          get cartTotal() {
              return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
          },
          get totalItems() {
              return this.cart.reduce((sum, item) => sum + item.quantity, 0);
          }
      }">

    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b sticky top-0 z-30 px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-600">Store Dashboard</h1>
        
        <div class="flex items-center space-x-6">
            <!-- Cart Button -->
            <button @click="openCart = true" class="relative bg-gray-100 px-4 py-2 rounded-lg font-medium hover:bg-gray-200 transition flex items-center space-x-2">
                <span>🛒 Cart</span>
                <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full" x-text="totalItems">0</span>
            </button>

            <!-- User Info / Logout -->
            <div class="flex items-center space-x-3">
                <span class="text-sm font-medium text-gray-700">Hello, {{ Auth::user()->name ?? 'User' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:underline">Log Out</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content: Product Cards Grid -->
    <div class="max-w-7xl mx-auto py-10 px-6">
         <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Available Products ({{ count($products) }})</h2>
        </div> 

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-mono text-gray-400">#{{ $product->id }}</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $product->stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->stock_quantity > 0 ? $product->stock_quantity . ' in stock' : 'Out of stock' }}
                            </span>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $product->name }}</h3>
                        <p class="text-blue-600 font-bold text-xl mb-4">₹{{ number_format($product->price, 2) }}</p>
                    </div>

                    @if($product->stock > 0)
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <label class="text-xs text-gray-500 font-medium">Qty:</label>
                                <input type="number" id="qty-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" 
                                    class="w-20 px-2 py-1 border rounded text-center text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <button @click="addToCart({{ json_encode($product) }})" 
                                class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition text-sm shadow-sm">
                                Add to Cart
                            </button>
                        </div>
                    @else
                        <button disabled class="w-full bg-gray-200 text-gray-400 py-2 rounded-lg font-semibold cursor-not-allowed text-sm">
                            Sold Out
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- ================= SLIDING CART DRAWER ================= -->
    <div x-show="openCart" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" @click="openCart = false"></div>
        
        <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white shadow-xl flex flex-col">
                <div class="p-6 border-b flex justify-between items-center bg-gray-50">
                    <h2 class="text-xl font-bold">Your Shopping Cart</h2>
                    <button @click="openCart = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="flex justify-between items-center border-b pb-4">
                            <div>
                                <h4 class="font-semibold text-gray-900" x-text="item.name"></h4>
                                <p class="text-sm text-gray-500">₹<span x-text="item.price"></span> × <span x-text="item.quantity"></span></p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="font-bold text-gray-800">₹<span x-text="item.price * item.quantity"></span></span>
                                <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700 text-sm font-medium">Remove</button>
                            </div>
                        </div>
                    </template>
                    <div x-show="cart.length === 0" class="text-center text-gray-400 py-12">
                        Your cart is empty. Add products from the dashboard!
                    </div>
                </div>

                <div class="border-t p-6 bg-gray-50 space-y-4" x-show="cart.length > 0">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span>₹<span x-text="cartTotal.toFixed(2)"></span></span>
                    </div>
                    <button @click="openCart = false; openCheckout = true;" 
                        class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition shadow">
                        Proceed to Checkout &rarr;
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= CHECKOUT & SHIPPING MODAL ================= -->
    <div x-show="openCheckout" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative max-h-[90vh] overflow-y-auto" @click.away="openCheckout = false">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-xl font-bold">Shipping & Payment</h3>
                <button @click="openCheckout = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>

            <form id="razorpay-checkout-form" class="space-y-4" @submit.prevent="handleRazorpayPayment">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Full Name</label>
                    <input type="text" id="cust_name" value="{{ Auth::user()->name ?? '' }}" required 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Email Address</label>
                    <input type="email" id="cust_email" value="{{ Auth::user()->email ?? '' }}" required 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Phone Number</label>
                    <input type="text" id="cust_phone" placeholder="9876543210" required 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Shipping Address</label>
                    <textarea id="cust_address" rows="3" required placeholder="123 Street Name, City"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-between items-center pt-2 border-t font-bold text-lg">
                    <span>Payable Amount:</span>
                    <span>₹<span x-text="cartTotal.toFixed(2)"></span></span>
                </div>

                <div class="flex space-x-3 pt-2">
                    <button type="button" @click="openCheckout = false" class="w-1/2 px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">Cancel</button>
                    <button type="submit" id="pay-btn" class="w-1/2 bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Pay with Razorpay
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Razorpay Handler Script -->
    <script>
        async function handleRazorpayPayment() {
            const payBtn = document.getElementById('pay-btn');
            payBtn.disabled = true;
            payBtn.innerText = "Processing...";

            // Access alpine cart data from DOM scope
            let alpineData = Alpine.$data(document.body);
            let cartItems = alpineData.cart.map(item => ({ product_id: item.id, quantity: item.quantity }));

            const customerDetails = {
                customer_name: document.getElementById('cust_name').value,
                email: document.getElementById('cust_email').value,
                phone: document.getElementById('cust_phone').value,
                shipping_address: document.getElementById('cust_address').value,
                cart_items: cartItems
            };

            try {
                // Step 1: Create Razorpay Order on Backend
                const orderRes = await fetch('/api/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(customerDetails)
                });

                const orderData = await orderRes.json();

                if (!orderData.success) {
                    alert("Error: " + (orderData.message || orderData.error));
                    payBtn.disabled = false;
                    payBtn.innerText = "Pay with Razorpay";
                    return;
                }

                // Step 2: Open Razorpay Modal
                const options = {
                    key: orderData.key_id,
                    amount: orderData.amount,
                    currency: orderData.currency,
                    name: "E-Commerce Store",
                    description: "Order Payment",
                    order_id: orderData.razorpay_order_id,
                    handler: async function (response) {
                        // Step 3: Verify Signature on Backend
                        const verifyRes = await fetch('/api/verify-payment', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature,
                                cart_items: cartItems
                            })
                        });

                        const verifyData = await verifyRes.json();

                        if (verifyData.success) {
                            alert("Payment Successful! Order placed successfully.");
                            window.location.reload();
                        } else {
                            alert("Payment Verification Failed: " + verifyData.message);
                            payBtn.disabled = false;
                            payBtn.innerText = "Pay with Razorpay";
                        }
                    },
                    prefill: {
                        name: customerDetails.customer_name,
                        email: customerDetails.email,
                        contact: customerDetails.phone
                    },
                    theme: {
                        color: "#2563EB"
                    }
                };

                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function (response) {
                    alert("Payment failed: " + response.error.description);
                    payBtn.disabled = false;
                    payBtn.innerText = "Pay with Razorpay";
                });
                rzp.open();

            } catch (error) {
                console.error(error);
                alert("An unexpected error occurred.");
                payBtn.disabled = false;
                payBtn.innerText = "Pay with Razorpay";
            }
        }
    </script>
</body>
</html>