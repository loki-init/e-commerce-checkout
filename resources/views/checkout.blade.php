<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Commerce Checkout with Razorpay</title>
    <!-- Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Razorpay Checkout Script -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-8 text-center">Checkout Page</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Left Column: Products & Cart -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Your Cart</h2>
                
                <div id="cart-items-list" class="space-y-4 mb-6">
                    <!-- Dynamic Cart Item Example -->
                    <div class="flex justify-between items-center border-b pb-3">
                        <div>
                            <h3 class="font-medium">Wireless Headphones</h3>
                            <p class="text-sm text-gray-500">₹1,500.00 each</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-gray-100 rounded text-sm" id="item-qty">2</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between font-bold text-lg border-t pt-4">
                    <span>Total Amount:</span>
                    <span id="cart-total">₹3,000.00</span>
                </div>
            </div>

            <!-- Right Column: Customer & Shipping Details -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Shipping Details</h2>
                
                <form id="checkout-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Full Name</label>
                        <input type="text" id="customer_name" value="Rahul Sharma" required 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Email Address</label>
                        <input type="email" id="email" value="rahul@example.com" required 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Phone Number</label>
                        <input type="text" id="phone" value="9876543210" required 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Shipping Address</label>
                        <textarea id="shipping_address" rows="3" required 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">123, MG Road, Bengaluru</textarea>
                    </div>

                    <button type="submit" id="pay-btn" 
                        class="w-full bg-blue-600 text-white py-3 rounded-md font-semibold hover:bg-blue-700 transition">
                        Proceed to Pay (₹3,000)
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('checkout-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            const payBtn = document.getElementById('pay-btn');
            payBtn.disabled = true;
            payBtn.innerText = "Processing...";

            const customerDetails = {
                customer_name: document.getElementById('customer_name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                shipping_address: document.getElementById('shipping_address').value,
                cart_items: [
                    { product_id: 1, quantity: 2 } // Mock cart payload matching your backend requirement
                ]
            };

            try {
                // Step 1: Call Laravel backend to create Razorpay Order
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
                    payBtn.innerText = "Proceed to Pay (₹3,000)";
                    return;
                }

                // Step 2: Open Razorpay Checkout Modal
                const options = {
                    key: orderData.key_id,
                    amount: orderData.amount,
                    currency: orderData.currency,
                    name: "My Laravel Store",
                    description: "E-Commerce Order Payment",
                    order_id: orderData.razorpay_order_id,
                    handler: async function (response) {
                        // Step 3: Send verification payload to backend
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
                                cart_items: customerDetails.cart_items
                            })
                        });

                        const verifyData = await verifyRes.json();

                        if (verifyData.success) {
                            alert("Payment Successful! Order placed successfully.");
                            window.location.reload();
                        } else {
                            alert("Payment Verification Failed: " + verifyData.message);
                            payBtn.disabled = false;
                            payBtn.innerText = "Proceed to Pay (₹3,000)";
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
                    payBtn.innerText = "Proceed to Pay (₹3,000)";
                });
                rzp.open();

            } catch (error) {
                console.error(error);
                alert("An unexpected error occurred.");
                payBtn.disabled = false;
                payBtn.innerText = "Proceed to Pay (₹3,000)";
            }
        });
    </script>
</body>
</html>