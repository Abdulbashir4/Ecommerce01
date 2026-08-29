<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cart()
    {
        return session('cart', []);
    }

    /**
     * Re-sync cart prices with the current Product record.
     * This fixes old cart sessions that may contain a stale price.
     */
    private function normalizeCart(array $cart): array
    {
        foreach ($cart as $id => &$item) {
            $productId = $item['id'] ?? $id;
            $product = Product::find($productId);

            if (!$product) {
                continue;
            }

            $item['id'] = $product->product_id;
            $item['name'] = $product->product_name;
            $item['price'] = (float) $product->sale_price;
            $item['regular_price'] = (float) $product->original_price;
            $item['qty'] = max(1, (int) ($item['qty'] ?? 1));
            $item['image'] = $product->thumbnail;
        }

        unset($item);

        return $cart;
    }

    public function index()
    {
        $cart = $this->normalizeCart($this->cart());
        session(['cart' => $cart]);

        return view('cart.index', [
            'cart' => $cart,
        ]);
    }

    public function add(Request $r, Product $product)
    {
        $qty = max(1, (int) $r->input('qty', 1));
        $cart = $this->normalizeCart($this->cart());
        $id = $product->product_id;

        $salePrice = (float) $product->sale_price;
        $regularPrice = (float) $product->original_price;
        $existingQty = isset($cart[$id]) ? (int) ($cart[$id]['qty'] ?? 0) : 0;

        $cart[$id] = [
            'id' => $id,
            'name' => $product->product_name,
            'price' => $salePrice,
            'regular_price' => $regularPrice,
            'qty' => $existingQty + $qty,
            'image' => $product->thumbnail,
        ];

        session(['cart' => $cart]);

        $count = collect($cart)->sum('qty');

        if ($r->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'cartCount' => $count,
            ]);
        }

        return back()->with('success', 'Product added to cart.');
    }

    public function update(Request $r)
    {
        $cart = $this->normalizeCart($this->cart());

        foreach ($r->input('qty', []) as $id => $qty) {
            if (!isset($cart[$id])) {
                continue;
            }

            $qty = max(0, (int) $qty);

            if ($qty === 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['qty'] = $qty;
            }
        }

        session(['cart' => $cart]);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) ($item['price'] ?? 0)) * ((int) ($item['qty'] ?? 0));
        }

        $count = collect($cart)->sum('qty');

        if ($r->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'cartCount' => $count,
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'cart' => $cart,
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $r, $id)
    {
        $cart = $this->normalizeCart($this->cart());
        unset($cart[$id]);
        session(['cart' => $cart]);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) ($item['price'] ?? 0)) * ((int) ($item['qty'] ?? 0));
        }

        $count = collect($cart)->sum('qty');

        if ($r->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'cartCount' => $count,
                'subtotal' => number_format($subtotal, 2, '.', ''),
            ]);
        }

        return back()->with('success', 'Product removed from cart.');
    }
}
