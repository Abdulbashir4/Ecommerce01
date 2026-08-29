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

    public function index()
    {
        return view('cart.index', [
            'cart' => $this->cart(),
        ]);
    }

    public function add(Request $r, Product $product)
    {
        $qty = max(1, (int) $r->input('qty', 1));

        $cart = $this->cart();

        $id = $product->product_id;

        $cart[$id] = [
            'id'    => $id,
            'name'  => $product->product_name,
            'price' => (float) $product->sale_price,
            'qty'   => ($cart[$id]['qty'] ?? 0) + $qty,
            'image' => $product->thumbnail,
        ];

        session(['cart' => $cart]);

        $count = collect($cart)->sum('qty');

        if ($r->expectsJson()) {
            return response()->json([
                'status'    => 'success',
                'cartCount' => $count,
            ]);
        }

        return back()->with('success', 'Product added to cart.');
    }

    public function update(Request $r)
    {
        $cart = $this->cart();

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
            $subtotal += ((float) ($item['price'] ?? 0))
                * ((int) ($item['qty'] ?? 0));
        }

        $count = collect($cart)->sum('qty');

        if ($r->expectsJson()) {
            return response()->json([
                'status'    => 'success',
                'cartCount' => $count,
                'subtotal'  => number_format($subtotal, 2, '.', ''),
                'cart'      => $cart,
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $r, $id)
    {
        $cart = $this->cart();

        unset($cart[$id]);

        session(['cart' => $cart]);

        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += ((float) ($item['price'] ?? 0))
                * ((int) ($item['qty'] ?? 0));
        }

        $count = collect($cart)->sum('qty');

        if ($r->expectsJson()) {
            return response()->json([
                'status'    => 'success',
                'cartCount' => $count,
                'subtotal'  => number_format($subtotal, 2, '.', ''),
            ]);
        }

        return back()->with('success', 'Product removed from cart.');
    }
}