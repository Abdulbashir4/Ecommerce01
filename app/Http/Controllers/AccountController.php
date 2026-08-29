<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class AccountController extends Controller {
 public function index(){return view('account.index',['orders'=>auth()->user()->orders()->latest('order_id')->take(10)->get()]);}
 public function order(Order $order){abort_unless($order->user_id===auth()->id(),403);$order->load('items');return view('account.order',compact('order'));}
 public function passwordEdit(){return view('account.password');}
 public function passwordUpdate(Request $request){$data=$request->validate(['current_password'=>['required','current_password'],'password'=>['required','string','min:8','confirmed']]);$request->user()->update(['password'=>Hash::make($data['password']),'force_password_change'=>false,'failed_login_attempts'=>0,'locked_until'=>null]);return redirect('/account')->with('success','Password changed successfully.');}
}
