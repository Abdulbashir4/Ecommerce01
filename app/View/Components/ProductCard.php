<?php

namespace App\View\Components;

use App\Models\Setting;
use Illuminate\View\Component;
use Illuminate\View\View;

class ProductCard extends Component
{
    public function __construct(public mixed $product)
    {
    }

    public function render(): View
    {
        $defaults = [
            'show_image' => true,
            'show_sku' => false,
            'show_brand' => true,
            'show_category' => true,
            'show_stock' => true,
            'show_stock_quantity' => false,
            'show_price' => true,
            'show_regular_price' => true,
            'show_rating' => false,
            'show_add_to_cart' => true,
            'show_description' => false,
            'show_discount_badge' => true,
            'show_new_badge' => true,
            'show_featured_badge' => false,
            'show_view_button' => true,
            'show_wishlist' => true,
            'button_full_width' => true,
            'button_layout' => 'row',
            'layout' => 'grid',
            'mobile_columns' => 1,
            'tablet_columns' => 2,
            'desktop_columns' => 4,
            'gap' => 5,
            'card_padding' => 4,
            'text_align' => 'left',
            'card_style' => 'clean',
            'card_radius' => '2xl',
            'card_shadow' => 'sm',
            'hover_effect' => 'lift-shadow',
            'card_border' => true,
            'image_ratio' => '1/1',
            'image_fit' => 'cover',
            'image_zoom' => true,
            'image_background' => 'slate',
            'description_lines' => 2,
            'button_style' => 'solid',
            'name_lines' => 2,
            'price_size' => 'xl',
            'price_decimals' => 2,
            'currency_position' => 'before',
        ];

        $settings = Setting::get('product.display', []);
        if (!is_array($settings)) {
            $settings = [];
        }

        foreach ($defaults as $key => $default) {
            if (!array_key_exists($key, $settings)) {
                $legacyKey = 'product.' . $key;
                $legacy = Setting::get($legacyKey, null);
                $settings[$key] = $legacy !== null ? $legacy : $default;
            }
        }

        return view('components.product-card', [
            'settings' => array_replace($defaults, $settings),
        ]);
    }
}
