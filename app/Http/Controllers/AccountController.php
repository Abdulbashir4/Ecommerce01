<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $orders = $user->orders()
            ->latest('order_id')
            ->take(5)
            ->get();

        $stats = [
            'total' => $user->orders()->count(),
            'pending' => $user->orders()->where('order_status', 'Pending')->count(),
            'processing' => $user->orders()->where('order_status', 'Processing')->count(),
            'shipped' => $user->orders()->where('order_status', 'Shipped')->count(),
            'completed' => $user->orders()->where('order_status', 'Completed')->count(),
            'wishlist' => $user->wishlists()->count(),
            'reviews' => $user->reviews()->count(),
            'addresses' => $user->addresses()->count(),
        ];

        return view('account.index', compact('user', 'orders', 'stats'));
    }

    public function orders(Request $request)
    {
        $statuses = ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'];
        $status = $request->query('status');

        $query = auth()->user()->orders()->latest('order_id');

        if ($status && in_array($status, $statuses, true)) {
            $query->where('order_status', $status);
        } else {
            $status = null;
        }

        return view('account.orders', [
            'orders' => $query->paginate(10)->withQueryString(),
            'statuses' => $statuses,
            'activeStatus' => $status,
        ]);
    }

    public function order(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load('items.product');

        $reviewedProductIds = ProductReview::where('user_id', auth()->id())
            ->where('order_id', $order->order_id)
            ->pluck('product_id')
            ->all();

        return view('account.order', compact('order', 'reviewedProductIds'));
    }

    public function profileEdit()
    {
        return view('account.profile', ['user' => auth()->user()]);
    }

    public function profileUpdate(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50', Rule::unique('users', 'phone')->ignore($user->id)],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && !str_starts_with($user->profile_image, 'http')) {
                $old = public_path($user->profile_image);
                if (is_file($old)) {
                    @unlink($old);
                }
            }

            $path = $request->file('profile_image')->store('customer-profiles', 'public');
            $data['profile_image'] = 'storage/' . $path;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function passwordEdit()
    {
        return view('account.password');
    }

    public function passwordUpdate(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($data['password']),
            'force_password_change' => false,
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        return redirect()->route('account')->with('success', 'Password changed successfully.');
    }

    public function addresses()
    {
        return view('account.addresses', [
            'addresses' => auth()->user()->addresses()->latest('is_default')->latest('id')->get(),
        ]);
    }

    public function addressStore(Request $request)
    {
        $data = $this->validateAddress($request);
        $data['user_id'] = $request->user()->id;

        DB::transaction(function () use ($request, &$data) {
            $hasAddresses = $request->user()->addresses()->exists();
            $data['is_default'] = $hasAddresses
                ? $request->boolean('is_default')
                : true;

            if ($data['is_default']) {
                $request->user()->addresses()->update(['is_default' => false]);
            }

            CustomerAddress::create($data);
        });

        return back()->with('success', 'Address added successfully.');
    }

    public function addressUpdate(Request $request, CustomerAddress $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $data = $this->validateAddress($request);

        DB::transaction(function () use ($request, $address, &$data) {
            if ($request->boolean('is_default')) {
                $request->user()->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            }
            $data['is_default'] = $request->boolean('is_default');
            $address->update($data);
        });

        return back()->with('success', 'Address updated successfully.');
    }

    public function addressDefault(Request $request, CustomerAddress $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        DB::transaction(function () use ($request, $address) {
            $request->user()->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return back()->with('success', 'Default address updated.');
    }

    public function addressDestroy(Request $request, CustomerAddress $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $request->user()->addresses()->latest('id')->first()?->update(['is_default' => true]);
        }

        return back()->with('success', 'Address removed.');
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:100'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    public function wishlist()
    {
        return view('account.wishlist', [
            'items' => auth()->user()->wishlists()->with('product')->latest()->paginate(12),
        ]);
    }

    public function wishlistAdd(Request $request, $product)
    {
        $productModel = \App\Models\Product::findOrFail($product);

        Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $productModel->product_id,
        ]);

        return back()->with('success', 'Product added to wishlist.');
    }

    public function wishlistRemove(Request $request, Wishlist $wishlist)
    {
        abort_unless($wishlist->user_id === $request->user()->id, 403);
        $wishlist->delete();

        return back()->with('success', 'Product removed from wishlist.');
    }

    public function reviews()
    {
        $user = auth()->user();

        $reviews = $user->reviews()
            ->with(['product', 'order'])
            ->latest()
            ->paginate(10);

        $reviewed = $user->reviews()->get(['order_id', 'product_id'])
            ->map(fn ($review) => $review->order_id . ':' . $review->product_id)
            ->all();

        $reviewableItems = $user->orders()
            ->with('items.product')
            ->latest('order_id')
            ->get()
            ->flatMap(fn ($order) => $order->items
                ->filter(fn ($item) => $item->product_id && $item->product)
                ->map(fn ($item) => [
                    'order' => $order,
                    'item' => $item,
                    'key' => $order->order_id . ':' . $item->product_id,
                ])
            )
            ->reject(fn ($entry) => in_array($entry['key'], $reviewed, true))
            ->unique(fn ($entry) => $entry['key'])
            ->take(20);

        return view('account.reviews', compact('reviews', 'reviewableItems'));
    }

    public function reviewStore(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,order_id'],
            'product_id' => ['required', 'integer', 'exists:products,product_id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        $order = auth()->user()->orders()
            ->with('items')
            ->whereKey($data['order_id'])
            ->firstOrFail();

        abort_unless(
            $order->items->contains(fn ($item) => (int) $item->product_id === (int) $data['product_id']),
            422
        );

        ProductReview::create([
            'user_id' => auth()->id(),
            'order_id' => $order->order_id,
            'product_id' => $data['product_id'],
            'rating' => $data['rating'],
            'review' => $data['review'] ?? null,
        ]);

        return back()->with('success', 'Thank you for your review.');
    }
}
