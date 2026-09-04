<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $primaryKey = 'subcategory_id';

    protected $fillable = ['category_id', 'parent_subcategory_id', 'subcategory_name'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_subcategory_id', 'subcategory_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_subcategory_id', 'subcategory_id')->orderBy('subcategory_name');
    }

    public function childrenRecursive()
    {
        return $this->children()->with(['childrenRecursive', 'brands']);
    }

    public function brands()
    {
        return $this->hasMany(Brand::class, 'subcategory_id', 'subcategory_id');
    }
}
