<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Exception;

class CheckoutController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            env('RAZORPAY_KEY', 'rzp_test_RiO6by7RpQnrJ5'),
            env('RAZORPAY_SECRET', 'RyIQJumbGpoF7uzEb1Sq1pNV')
        );
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'shipping_address' => 'required|string',
            'cart_items' => 'required|array',
        ]);

        try {
            $totalAmount = 0;
            foreach ($request->cart_items as $item) {
                $product = DB::table('products')->where('id', $item['product_id'])->first();
                if (!$product || $product->stock < $item['quantity']) {
                    return response()->json(['success' => false, 'message' => 'Insufficient stock for product ID: ' . $item['product_id']], 400);
                }
                $totalAmount += $product->price * $item['quantity'];
            }

            $orderData = [
                'receipt' => 'rcptid_' . time(),
                'amount' => intval($totalAmount * 100), 
                'currency' => 'INR',
                'payment_capture' => 1
            ];

            $razorpayOrder = $this->razorpay->order->create($orderData);

            DB::table('orders')->insert([
                'user_id' => auth()->id() ?? 1,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'customer_address' => $request->shipping_address,
                'total_amount' => $totalAmount,
                'razorpay_order_id' => $razorpayOrder['id'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $razorpayOrder['amount'],
                'currency' => $razorpayOrder['currency'],
                'key_id' => env('RAZORPAY_KEY', 'rzp_test_RiO6by7RpQnrJ5'),
            ]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'cart_items' => 'required|array',
        ]);

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            DB::beginTransaction();

            DB::table('orders')
                ->where('razorpay_order_id', $request->razorpay_order_id)
                ->update([
                    'status' => 2,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'updated_at' => now()
                ]);

            foreach ($request->cart_items as $item) {
                DB::table('products')
                    ->where('id', $item['product_id'])
                    ->decrement('stock', $item['quantity']); // Fixed column name from 'stock' to 'stock'
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully and order placed!'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }
}