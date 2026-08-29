<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model { protected $primaryKey='order_id'; public $timestamps=false; protected $fillable=['customer_name','email','phone','address','city','postal_code','country','payment_method','total_amount','created_at','order_status','payment_status','user_id']; protected $casts=['created_at'=>'datetime','total_amount'=>'decimal:2']; public function items(){return $this->hasMany(OrderItem::class,'order_id','order_id');} public function user(){return $this->belongsTo(User::class);} }
