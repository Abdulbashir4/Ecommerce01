<?php
namespace App\Http\Controllers;
use App\Models\Order; use App\Models\OrderItem; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
class CheckoutController extends Controller {
 public function index(){abort_if(empty(session('cart')),302,'/shop');return view('checkout.index',['cart'=>session('cart',[])]);}
 public function place(Request $r){$data=$r->validate(['customer_name'=>'required|max:200','email'=>'nullable|email','phone'=>'required','address'=>'required','city'=>'required','postal_code'=>'nullable','country'=>'required','payment_method'=>'required|in:COD,Bkash,Nagad,Card']);$cart=session('cart',[]);if(!$cart)return back()->withErrors(['cart'=>'Your cart is empty.']);$total=array_sum(array_map(fn($i)=>$i['price']*$i['qty'],$cart));$order=null;DB::transaction(function()use(&$order,$data,$cart,$total,$r){$order=Order::create($data+['total_amount'=>$total,'created_at'=>now(),'user_id'=>$r->user()?->id]);foreach($cart as $i)OrderItem::create(['order_id'=>$order->order_id,'product_id'=>$i['id'],'product_name'=>$i['name'],'price'=>$i['price'],'qty'=>$i['qty'],'total'=>$i['price']*$i['qty']]);});session()->forget('cart');return redirect('/account/orders/'.$order->order_id)->with('success','Order placed successfully.');}
}
