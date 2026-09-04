<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model { protected $primaryKey='item_id'; public $timestamps=false; protected $fillable=['order_id','product_id','product_name','price','qty','total']; public function order(){return $this->belongsTo(Order::class,'order_id','order_id');} public function product(){return $this->belongsTo(Product::class,'product_id','product_id');} }
