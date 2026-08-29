<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $display = [
            'layout' => 'grid','mobile_columns' => 1,'tablet_columns' => 2,'desktop_columns' => 4,'gap' => 5,'card_padding' => 4,'text_align' => 'left',
            'card_style' => 'clean','card_radius' => '2xl','card_shadow' => 'sm','hover_effect' => 'lift-shadow','card_border' => true,
            'show_image' => true,'image_ratio' => '1/1','image_fit' => 'cover','image_zoom' => true,'image_background' => 'slate',
            'show_category' => true,'show_sku' => false,'show_brand' => true,'show_description' => false,'description_lines' => 2,
            'show_price' => true,'show_regular_price' => true,'show_rating' => false,'show_stock' => true,'show_stock_quantity' => false,
            'show_discount_badge' => true,'show_new_badge' => true,'show_featured_badge' => false,'show_view_button' => true,'show_add_to_cart' => true,'show_wishlist' => true,
            'button_style' => 'solid','button_full_width' => true,'button_layout' => 'row','name_lines' => 2,'price_size' => 'xl','price_decimals' => 2,'currency_position' => 'before',
            'show_home_products' => true,'show_shop_products' => true,'home_title' => 'Best Seller Products','home_limit' => 8,'home_desktop_columns' => 4,
        ];
        Setting::set('product.display', $display, 'product', 'json');

        foreach ([
            ['product.show_image', true, 'product', 'boolean'],['product.show_sku', false, 'product', 'boolean'],['product.show_brand', true, 'product', 'boolean'],
            ['product.show_category', true, 'product', 'boolean'],['product.show_stock', true, 'product', 'boolean'],['product.show_price', true, 'product', 'boolean'],
            ['product.show_rating', false, 'product', 'boolean'],['product.show_add_to_cart', true, 'product', 'boolean'],['product.layout', 'grid', 'layout', 'string'],['product.columns', 4, 'layout', 'integer'],
            ['general.site_name','Optimum Biomedical','general','string'],['general.tagline','Medical equipment and biomedical products','general','string'],
            ['general.currency','BDT','general','string'],['general.currency_symbol','৳','general','string'],['general.timezone','Asia/Dhaka','general','string'],
            ['general.date_format','d M Y','general','string'],['general.time_format','h:i A','general','string'],['general.contact_email','optimumbiomedical.50@gmail.com','general','string'],
            ['general.contact_phone','01862252456','general','string'],['general.whatsapp','','general','string'],['general.address','Khilkhet, Dhaka','general','string'],
            ['general.default_country','Bangladesh','general','string'],['general.tax_enabled',false,'general','boolean'],['general.tax_rate','0','general','string'],
            ['general.shipping_enabled',true,'general','boolean'],['general.default_shipping_cost','0','general','string'],['general.registration_enabled',true,'general','boolean'],
            ['general.guest_checkout',false,'general','boolean'],['general.order_notification_email','','general','string'],['general.maintenance',false,'general','boolean'],
            ['general.maintenance_message','We are currently performing scheduled maintenance. Please check back soon.','general','string'],
            ['layout.sidebar','right','layout','string'],['layout.container','7xl','layout','string'],['layout.section_spacing','normal','layout','string'],
            ['layout.shop_header',true,'layout','boolean'],['layout.breadcrumbs',true,'layout','boolean'],['layout.pagination',true,'layout','boolean'],
        ] as [$key,$value,$group,$type]) {
            Setting::set($key,$value,$group,$type);
        }
    }
}
