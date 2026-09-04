<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model { protected $primaryKey='category_id'; protected $fillable=['category_name','category_image']; public function subcategories(){return $this->hasMany(Subcategory::class,'category_id','category_id');} public function products(){return $this->hasMany(Product::class,'category_id','category_id');} }
