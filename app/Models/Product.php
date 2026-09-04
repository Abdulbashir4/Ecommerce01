<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_name','slug','sku','category_id','subcategory_id','brand_id',
        'short_description','long_description','specifications','price','discount_price',
        'discount_percent','purchase_price','stock_qty','stock_status','min_order_qty',
        'max_order_qty','thumbnail','featured_image','weight','dimensions','shipping_cost',
        'is_featured','is_trending','is_new','flash_sale','status','meta_title',
        'meta_keywords','meta_description','gallery_images',
        'source_url','source_platform','source_product_id','imported_at'
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_new' => 'boolean',
        'flash_sale' => 'boolean',
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'subcategory_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    /**
     * Regular/original product price.
     * The database stores the normal selling price in `price`.
     */
    public function getOriginalPriceAttribute()
    {
        return (float) ($this->price ?? 0);
    }

    /**
     * Discount amount.
     * The database stores the discount amount in `discount_price`.
     */
    public function getDiscountAmountAttribute()
    {
        return max(0, (float) ($this->discount_price ?? 0));
    }

    /**
     * Final/sale price shown to customers.
     * sale price = regular price - discount amount.
     */
    public function getSalePriceAttribute()
    {
        $regular = $this->original_price;
        $discount = $this->discount_amount;

        return max(0, $regular - $discount);
    }

    /**
     * Actual discount percentage calculated from the database prices.
     */
    public function getDiscountPercentCalculatedAttribute()
    {
        $regular = $this->original_price;
        $sale = $this->sale_price;

        if ($regular <= 0 || $sale >= $regular) {
            return 0;
        }

        return round((($regular - $sale) / $regular) * 100, 1);
    }
}
