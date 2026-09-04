<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Brand extends Model { protected $primaryKey='brand_id'; protected $fillable=['subcategory_id','brand_name']; public function subcategory(){return $this->belongsTo(Subcategory::class,'subcategory_id','subcategory_id');} }
