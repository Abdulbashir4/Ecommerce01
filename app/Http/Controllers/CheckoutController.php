<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        abort_if(empty($cart), 302, '/shop');

        if (!auth()->check() && !Setting::get('general.guest_checkout', false)) {
            return redirect()->route('login')->with('warning', 'Please sign in before checkout.');
        }

        $subtotal = collect($cart)->sum(fn ($i) => (float) ($i['price'] ?? 0) * (int) ($i['qty'] ?? 0));
        $taxEnabled = (bool) Setting::get('general.tax_enabled', false);
        $taxRate = (float) Setting::get('general.tax_rate', 0);
        $tax = $taxEnabled ? round($subtotal * $taxRate / 100, 2) : 0;
        $shippingEnabled = (bool) Setting::get('general.shipping_enabled', true);
        $shipping = $shippingEnabled ? (float) Setting::get('general.default_shipping_cost', 0) : 0;

        return view('checkout.index', compact('cart', 'subtotal', 'taxEnabled', 'taxRate', 'tax', 'shippingEnabled', 'shipping'));
    }

    public function place(Request $r)
    {
        if (!auth()->check() && !Setting::get('general.guest_checkout', false)) {
            return redirect()->route('login')->with('warning', 'Please sign in before checkout.');
        }

        $data = $r->validate([
            'customer_name' => 'required|string|max:200',
            'email' => 'nullable|email|max:200',
            'phone' => 'required|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'required|string|max:100',
            'payment_method' => 'required|in:COD,Bkash,Nagad,Card',
        ]);

        $cart = session('cart', []);
        if (!$cart) return back()->withErrors(['cart' => 'Your cart is empty.']);

        $order = DB::transaction(function () use ($data, $cart, $r) {
            $subtotal = 0;
            $items = [];

            foreach ($cart as $item) {
                $productId = (int) ($item['id'] ?? 0);
                $qty = max(1, (int) ($item['qty'] ?? 1));
                $product = Product::query()->lockForUpdate()->find($productId);

                if (!$product || !$product->status) {
                    abort(422, 'One of the products in your cart is no longer available.');
                }

                if ((int) $product->stock_qty < $qty) {
                    abort(422, "Insufficient stock for {$product->product_name}. Available: {$product->stock_qty}.");
                }

                $price = (float) $product->sale_price;
                $lineTotal = round($price * $qty, 2);
                $subtotal += $lineTotal;
                $items[] = compact('product', 'productId', 'qty', 'price', 'lineTotal');

                $product->stock_qty = max(0, (int) $product->stock_qty - $qty);
                $product->stock_status = $product->stock_qty > 0 ? 'In Stock' : 'Out of Stock';
                $product->save();
            }

            $taxEnabled = (bool) Setting::get('general.tax_enabled', false);
            $taxRate = (float) Setting::get('general.tax_rate', 0);
            $tax = $taxEnabled ? round($subtotal * $taxRate / 100, 2) : 0;
            $shipping = Setting::get('general.shipping_enabled', true)
                ? (float) Setting::get('general.default_shipping_cost', 0)
                : 0;
            $total = round($subtotal + $tax + $shipping, 2);

            $order = Order::create($data + [
                'total_amount' => $total,
                'created_at' => now(),
                'user_id' => $r->user()?->id,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['productId'],
                    'product_name' => $item['product']->product_name,
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'total' => $item['lineTotal'],
                ]);
            }

            return $order;
        });

        session()->forget('cart');

        $notificationEmail = Setting::get('general.order_notification_email', '');
        if (filter_var($notificationEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::raw(
                    "New order #{$order->order_id} was placed. Total: "
                    . Setting::get('general.currency_symbol', '৳') . ' ' . number_format((float) $order->total_amount, 2),
                    function ($message) use ($notificationEmail, $order) {
                        $message->to($notificationEmail)
                            ->subject('New Order #' . $order->order_id);
                    }
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return auth()->check()
            ? redirect()->route('account.order', $order)->with('success', 'Order placed successfully.')
            : redirect()->route('order.success', $order)->with('success', 'Order placed successfully.');
    }

    public function success(Order $order)
    {
        abort_if(auth()->check() && $order->user_id && $order->user_id !== auth()->id(), 403);

        $order->load('items');

        return view('checkout.success', compact('order'));
    }
}
